#!/usr/bin/env bash
#
# Server-side deploy. Invoked by .github/workflows/deploy.yml over SSH, or by
# hand for a rollback:
#
#   ./scripts/deploy.sh ghcr.io/<owner>/<repo>:sha-<commit>
#
# Expects, in the current directory: docker-compose.yml, scripts/, and an
# environment-specific .env (never committed, never overwritten by CI).

set -euo pipefail

IMAGE="${1:?Usage: deploy.sh <image:tag>}"
cd "$(dirname "$0")/.."

log() { printf '[deploy] %s\n' "$*"; }

[ -f .env ] || { echo "[deploy] ERROR: .env missing in $(pwd)" >&2; exit 1; }

# Pin the image tag in .env so manual `docker compose` runs use the same build.
if grep -q '^WP_IMAGE=' .env; then
	sed -i.bak "s|^WP_IMAGE=.*|WP_IMAGE=${IMAGE}|" .env && rm -f .env.bak
else
	printf '\nWP_IMAGE=%s\n' "${IMAGE}" >> .env
fi

# Base file only, so a stray docker-compose.override.yml on a server is never used.
compose() { docker compose -f docker-compose.yml "$@"; }

log "Ensuring network traenet exists..."
docker network inspect traenet >/dev/null 2>&1 || docker network create traenet

log "Pulling ${IMAGE}..."
compose pull wordpress db

log "Starting containers..."
compose up -d --no-build --remove-orphans --wait

log "Provisioning WordPress..."
compose exec -T -u www-data wordpress mdev-provision

log "Pruning dangling images..."
docker image prune -f >/dev/null

log "Deployed ${IMAGE}."
