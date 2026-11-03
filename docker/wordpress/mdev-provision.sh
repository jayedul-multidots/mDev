#!/usr/bin/env bash
#
# mdev-provision: idempotent post-start/post-deploy WordPress setup.
#
# Runs inside the wordpress container as www-data:
#   docker compose exec -T -u www-data wordpress mdev-provision
#
# 1. Waits for the database.
# 2. Installs WordPress on first run when WP_AUTO_INSTALL=true.
# 3. Runs core database upgrades.
# 4. Activates WP_THEME and WP_ACTIVATE_PLUGINS (space-separated or `all`).
#
# Safe to re-run any number of times.

set -euo pipefail

cd /var/www/html

log() { printf '[mdev-provision] %s\n' "$*"; }
fail() { printf '[mdev-provision] ERROR: %s\n' "$*" >&2; exit 1; }

wait_for_db() {
	local attempt
	for attempt in $(seq 1 30); do
		if php -r '
			mysqli_report( MYSQLI_REPORT_OFF );
			$host = getenv( "WORDPRESS_DB_HOST" );
			$port = 3306;
			if ( str_contains( $host, ":" ) ) { [ $host, $port ] = explode( ":", $host, 2 ); }
			$link = @mysqli_connect( $host, getenv( "WORDPRESS_DB_USER" ), getenv( "WORDPRESS_DB_PASSWORD" ), getenv( "WORDPRESS_DB_NAME" ), (int) $port );
			exit( $link ? 0 : 1 );
		'; then
			return 0
		fi
		log "Waiting for database (${attempt}/30)..."
		sleep 2
	done
	fail "Database not reachable."
}

install_core() {
	if wp core is-installed 2>/dev/null; then
		return 0
	fi

	if [ "${WP_AUTO_INSTALL:-false}" != "true" ]; then
		fail "WordPress is not installed and WP_AUTO_INSTALL is not true. Import a database or enable auto install."
	fi

	: "${WP_HOME:?WP_HOME is required}"
	: "${WP_ADMIN_USER:?WP_ADMIN_USER is required}"
	: "${WP_ADMIN_PASSWORD:?WP_ADMIN_PASSWORD is required}"
	: "${WP_ADMIN_EMAIL:?WP_ADMIN_EMAIL is required}"

	log "Installing WordPress at ${WP_HOME}..."
	wp core install \
		--url="${WP_HOME}" \
		--title="${WP_TITLE:-mdev}" \
		--admin_user="${WP_ADMIN_USER}" \
		--admin_password="${WP_ADMIN_PASSWORD}" \
		--admin_email="${WP_ADMIN_EMAIL}" \
		--skip-email

	# Runs once, on install only: sets pretty permalinks and flushes rules.
	wp rewrite structure '/%postname%/'
}

activate_theme() {
	local theme="${WP_THEME:-}"
	[ -z "${theme}" ] && return 0

	if ! wp theme is-installed "${theme}"; then
		fail "Theme '${theme}' is not present in the image."
	fi
	if wp theme is-active "${theme}"; then
		log "Theme '${theme}' already active."
		return 0
	fi
	wp theme activate "${theme}"
}

activate_plugins() {
	local plugins="${WP_ACTIVATE_PLUGINS:-}"
	[ -z "${plugins}" ] && return 0

	if [ "${plugins}" = "all" ]; then
		wp plugin activate --all
		return 0
	fi

	local plugin missing=()
	for plugin in ${plugins}; do
		if ! wp plugin is-installed "${plugin}"; then
			missing+=("${plugin}")
			continue
		fi
		if ! wp plugin is-active "${plugin}"; then
			wp plugin activate "${plugin}"
		fi
	done

	# if [ "${#missing[@]}" -gt 0 ]; then
		# fail "Plugins listed in WP_ACTIVATE_PLUGINS are not installed: ${missing[*]} (add them to composer.json)."
	# fi
}

wait_for_db
install_core
log "Running database upgrades..."
wp core update-db
# WordPress caches each theme's pattern file list; a deploy that adds or
# removes pattern files must clear it or the change is invisible.
wp eval 'foreach ( wp_get_themes() as $theme ) { $theme->delete_pattern_cache(); }'
activate_theme
activate_plugins
wp cache flush >/dev/null
log "Done."
