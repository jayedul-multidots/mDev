#!/usr/bin/env bash
#
# Copy the plugins and themes shipped in the WordPress image into the host
# wp-content folder that the live `wordpress` service bind-mounts.
#
# Run by scripts/deploy.sh in a one-off container of the NEW image (as root,
# before the site restarts), with the host folder mounted at <target>:
#
#   sync-wp-content.sh <source wp-content> <target>
#
# - Every plugin/theme folder in the image replaces the host copy of the same
#   name (each swap is two renames, so the running site never sees a
#   half-copied folder). The image always wins for what it ships.
# - Everything else in the host folder is left alone: uploads, plugins and
#   themes added on the server (e.g. premium ones), caches.
# - Folders the image shipped last time but no longer does are removed; the
#   list lives in <target>/.mdev-image-managed.
# - First run only: when /old-uploads is mounted (the former `uploads` Docker
#   volume) and <target>/uploads is empty, its files are copied over.
#
# Idempotent: safe to re-run.

set -euo pipefail

SRC="${1:?Usage: sync-wp-content.sh <source wp-content> <target>}"
DST="${2:?Usage: sync-wp-content.sh <source wp-content> <target>}"
MANIFEST="${DST}/.mdev-image-managed"
OWNER="www-data:www-data"

log() { printf '[sync-wp-content] %s\n' "$*"; }

[ -d "${SRC}/plugins" ] || { log "ERROR: ${SRC} is not a wp-content folder."; exit 1; }

# Replace <dst> with a copy of <src>: copy beside it, then swap by renaming.
replace_dir() {
	local src="$1" dst="$2" parent name
	parent="$(dirname "${dst}")"
	name="$(basename "${dst}")"
	# Dot-prefixed, so WordPress never lists the temporary copies as plugins.
	rm -rf "${parent}/.mdev-new-${name}" "${parent}/.mdev-old-${name}"
	cp -a "${src}" "${parent}/.mdev-new-${name}"
	if [ -e "${dst}" ]; then
		mv "${dst}" "${parent}/.mdev-old-${name}"
	fi
	mv "${parent}/.mdev-new-${name}" "${dst}"
	rm -rf "${parent}/.mdev-old-${name}"
}

# Folder skeleton, writable by WordPress like the image's own wp-content.
mkdir -p "${DST}/plugins" "${DST}/themes" "${DST}/uploads"
chown "${OWNER}" "${DST}" "${DST}/plugins" "${DST}/themes" "${DST}/uploads"

# "Silence is golden" index files: only when missing.
for file in index.php plugins/index.php themes/index.php; do
	if [ -f "${SRC}/${file}" ] && [ ! -e "${DST}/${file}" ]; then
		cp -a "${SRC}/${file}" "${DST}/${file}"
	fi
done

# Uploads from the former Docker volume (first deploy with a host folder).
if [ -d /old-uploads ] && [ -z "$(ls -A "${DST}/uploads")" ] && [ -n "$(ls -A /old-uploads)" ]; then
	log "Copying uploads from the former uploads volume..."
	cp -a /old-uploads/. "${DST}/uploads/"
fi

# Plugins and themes shipped in the image.
managed=()
for kind in plugins themes; do
	for dir in "${SRC}/${kind}"/*/; do
		[ -d "${dir}" ] || continue
		rel="${kind}/$(basename "${dir}")"
		replace_dir "${SRC}/${rel}" "${DST}/${rel}"
		managed+=("${rel}")
		log "Updated ${rel}"
	done
done

# Remove what an earlier image shipped and this one no longer does.
if [ -f "${MANIFEST}" ]; then
	while IFS= read -r rel; do
		# Only ever plugins/<name> or themes/<name>, never anything else.
		[[ "${rel}" =~ ^(plugins|themes)/[A-Za-z0-9._-]+$ ]] || continue
		[[ "${rel}" == */.* ]] && continue
		if ! printf '%s\n' "${managed[@]}" | grep -qxF -- "${rel}"; then
			rm -rf "${DST:?}/${rel}"
			log "Removed ${rel} (no longer in the image)"
		fi
	done < "${MANIFEST}"
fi

printf '%s\n' "${managed[@]}" > "${MANIFEST}.tmp"
mv "${MANIFEST}.tmp" "${MANIFEST}"

log "Done: ${#managed[@]} folders from the image."
