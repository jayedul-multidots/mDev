#!/usr/bin/env bash
#
# Server-side deploy. Invoked by .github/workflows/deploy.yml over SSH, or by
# hand for a rollback:
#
#   ./scripts/deploy.sh ghcr.io/<owner>/<repo>:sha-<commit>
#
# Expects, in the current directory: docker-compose.yml, scripts/, and an
# environment-specific .env (never committed, never overwritten by CI).
#
# Docker access, in order of preference:
#   1. the user can run docker directly (member of the `docker` group);
#   2. a sudo password is supplied (CI: --stdin-secrets, see below);
#   3. passwordless sudo;
#   4. interactive sudo prompt (manual runs in a terminal).
#
# --stdin-secrets: read two lines from stdin before doing anything else,
#   line 1: sudo password (may be empty), line 2: registry token (may be empty).
#   Secrets never appear in command lines, process lists, or logs.
#   REGISTRY (default ghcr.io) and REGISTRY_USER are read from the environment.

set -euo pipefail

SUDO_PASSWORD=""
REGISTRY_TOKEN=""
if [ "${1:-}" = "--stdin-secrets" ]; then
	shift
	IFS= read -r SUDO_PASSWORD || true
	IFS= read -r REGISTRY_TOKEN || true
	exec </dev/null
fi

IMAGE="${1:?Usage: deploy.sh [--stdin-secrets] <image:tag>}"
REGISTRY="${REGISTRY:-ghcr.io}"
cd "$(dirname "$0")/.."

log() { printf '[deploy] %s\n' "$*"; }
fail() { printf '[deploy] ERROR: %s\n' "$*" >&2; exit 1; }

[ -f .env ] || fail ".env missing in $(pwd)"

# --- Docker access -------------------------------------------------------------
if docker info >/dev/null 2>&1; then
	DOCKER_MODE=direct
elif [ -n "${SUDO_PASSWORD}" ]; then
	printf '%s\n' "${SUDO_PASSWORD}" | sudo -S -k -p '' true 2>/dev/null \
		|| fail "SUDO_PASSWORD was rejected by sudo for user $(id -un)."
	DOCKER_MODE=sudo-password
elif sudo -n true 2>/dev/null; then
	DOCKER_MODE=sudo
elif [ -t 0 ]; then
	DOCKER_MODE=sudo
else
	fail "User $(id -un) cannot use Docker. Add it to the docker group (sudo usermod -aG docker $(id -un)) or set the SUDO_PASSWORD secret."
fi
log "Docker access: ${DOCKER_MODE}"

# Run docker; extra stdin (e.g. a registry token) can be piped in by the caller.
docker_cmd() {
	case "${DOCKER_MODE}" in
		direct) docker "$@" ;;
		sudo) sudo docker "$@" ;;
		# sudo reads the first line as the password; the rest of stdin reaches docker.
		# -k: always prompt, so a cached sudo ticket can never let the password line
		# leak into docker's stdin (e.g. become the registry token).
		sudo-password) { printf '%s\n' "${SUDO_PASSWORD}"; cat; } | sudo -S -k -p '' docker "$@" ;;
	esac
}

# Base file only, so a stray docker-compose.override.yml on a server is never used.
compose() { docker_cmd compose -f docker-compose.yml "$@" </dev/null; }

# --- Deploy ----------------------------------------------------------------------
if [ -n "${REGISTRY_TOKEN}" ]; then
	log "Logging in to ${REGISTRY}..."
	printf '%s\n' "${REGISTRY_TOKEN}" \
		| docker_cmd login "${REGISTRY}" -u "${REGISTRY_USER:?REGISTRY_USER is required with a registry token}" --password-stdin >/dev/null
fi

# Pin the image tag in .env so manual `docker compose` runs use the same build.
if grep -q '^WP_IMAGE=' .env; then
	sed -i.bak "s|^WP_IMAGE=.*|WP_IMAGE=${IMAGE}|" .env && rm -f .env.bak
else
	printf '\nWP_IMAGE=%s\n' "${IMAGE}" >> .env
fi

# Create the shared network only when Docker says it does not exist; any other
# error (permissions, daemon down) stops the deploy instead of masking it.
if network_err="$(docker_cmd network inspect traenet </dev/null 2>&1 >/dev/null)"; then
	log "Network traenet exists."
elif grep -qi 'not found' <<<"${network_err}"; then
	log "Creating network traenet..."
	docker_cmd network create traenet </dev/null >/dev/null
else
	fail "Could not inspect network traenet: ${network_err}"
fi

log "Pulling ${IMAGE}..."
compose pull wordpress db

# --- wp-content on the host ------------------------------------------------------
# Value of KEY in .env (last one wins, surrounding quotes removed), or DEFAULT.
env_value() {
	local value
	value="$(grep -E "^$1=" .env | tail -n 1 | cut -d= -f2-)" || true
	value="${value%\"}"; value="${value#\"}"
	value="${value%\'}"; value="${value#\'}"
	printf '%s' "${value:-$2}"
}

# Same default as docker-compose.yml; relative paths are relative to this folder.
WP_CONTENT_HOST_DIR="$(env_value WP_CONTENT_HOST_DIR ./wp-content)"
case "${WP_CONTENT_HOST_DIR}" in
	/*) ;;
	*) WP_CONTENT_HOST_DIR="$(pwd)/${WP_CONTENT_HOST_DIR#./}" ;;
esac

# Uploads used to live in the `<project>_uploads` volume: offer it to the sync,
# which copies it over once, when the host uploads folder is still empty. The
# volume itself is kept (remove it by hand once the site checks out).
sync_mounts=(
	-v "${WP_CONTENT_HOST_DIR}:/target"
	-v "$(pwd)/scripts/sync-wp-content.sh:/usr/local/bin/mdev-sync-wp-content:ro"
)
old_uploads="$(env_value COMPOSE_PROJECT_NAME "$(basename "$(pwd)")")_uploads"
if docker_cmd volume inspect "${old_uploads}" </dev/null >/dev/null 2>&1; then
	sync_mounts+=(-v "${old_uploads}:/old-uploads:ro")
fi

log "Syncing the image's plugins and themes into ${WP_CONTENT_HOST_DIR}..."
docker_cmd run --rm --user root --entrypoint bash "${sync_mounts[@]}" "${IMAGE}" \
	/usr/local/bin/mdev-sync-wp-content /var/www/html/wp-content /target </dev/null

log "Starting containers..."
compose up -d --no-build --remove-orphans --wait

log "Provisioning WordPress..."
compose exec -T -u www-data wordpress mdev-provision

log "Pruning dangling images..."
docker_cmd image prune -f </dev/null >/dev/null

log "Deployed ${IMAGE}."
