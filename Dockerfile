# syntax=docker/dockerfile:1.7
#
# mdev WordPress image.
#
#   dev        WordPress core + tooling; custom code is bind-mounted locally.
#   production Immutable image: core + custom themes/plugin + Composer-managed
#              third-party plugins. Only wp-content/uploads is writable state.

ARG WP_VERSION=7.1
ARG PHP_VERSION=8.3

# -----------------------------------------------------------------------------
# Block editor assets for mdev-gb.
# -----------------------------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /build/mdev-gb
COPY wp-content/plugins/mdev-gb/package.json wp-content/plugins/mdev-gb/package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY wp-content/plugins/mdev-gb/ ./
RUN npm run build

# -----------------------------------------------------------------------------
# Composer: third-party plugins (wpackagist) + mdev-gb autoloader.
# -----------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --no-autoloader
COPY wp-content/plugins/mdev-gb/composer.json wp-content/plugins/mdev-gb/
COPY wp-content/plugins/mdev-gb/src/ wp-content/plugins/mdev-gb/src/
RUN composer dump-autoload --no-dev --classmap-authoritative --working-dir=wp-content/plugins/mdev-gb

# -----------------------------------------------------------------------------
# Runtime base.
# -----------------------------------------------------------------------------
FROM wordpress:${WP_VERSION}-php${PHP_VERSION}-apache AS base

ADD --chmod=0755 https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar /usr/local/bin/wp

COPY docker/wordpress/php.ini /usr/local/etc/php/conf.d/zz-mdev.ini
COPY docker/wordpress/wp-config-mdev.php /usr/local/etc/mdev/wp-config-mdev.php
COPY --chmod=0755 docker/wordpress/mdev-provision.sh /usr/local/bin/mdev-provision

# Optional HTTPS vhost; it only activates when certificates are mounted at
# /etc/ssl/mdev (SSL_DIR in .env), e.g. a Cloudflare Origin Certificate.
# ports.conf is reduced to :80 because the vhost file declares `Listen 443`
# itself, only when the certificate files exist.
COPY docker/wordpress/apache-ssl.conf /etc/apache2/sites-available/mdev-ssl.conf
RUN a2enmod ssl headers && a2ensite mdev-ssl && echo 'Listen 80' > /etc/apache2/ports.conf

# Bake core into the docroot so the upstream entrypoint never copies files at
# start-up (which would also write into bind mounts). Bundled sample plugins
# are dropped; plugins are managed through Composer.
RUN set -eux; \
	rm -rf /usr/src/wordpress/wp-content/plugins/akismet /usr/src/wordpress/wp-content/plugins/hello.php; \
	cp -a /usr/src/wordpress/. /var/www/html/; \
	mkdir -p /var/www/html/wp-content/uploads; \
	chown -R www-data:www-data /var/www/html

# Loaded by wp-config-docker.php when the entrypoint generates wp-config.php.
ENV WORDPRESS_CONFIG_EXTRA="require_once '/usr/local/etc/mdev/wp-config-mdev.php';" \
	WP_CLI_CACHE_DIR=/tmp/wp-cli-cache

# -----------------------------------------------------------------------------
# Local development.
# -----------------------------------------------------------------------------
FROM base AS dev

# Composer for running the quality toolchain inside the container
# (the repository is mounted at /app by docker-compose.override.yml).
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_HOME=/tmp/composer \
	COMPOSER_ALLOW_SUPERUSER=1 \
	MDEV_ROOT=/app

# -----------------------------------------------------------------------------
# Production.
# -----------------------------------------------------------------------------
FROM base AS production

COPY --from=vendor --chown=www-data:www-data /app/wp-content/plugins/ /var/www/html/wp-content/plugins/

COPY --chown=www-data:www-data wp-content/themes/mdev-block/ /var/www/html/wp-content/themes/mdev-block/
COPY --chown=www-data:www-data wp-content/themes/mdev-fse/ /var/www/html/wp-content/themes/mdev-fse/

COPY --chown=www-data:www-data wp-content/plugins/mdev-gb/mdev-gb.php wp-content/plugins/mdev-gb/readme.txt /var/www/html/wp-content/plugins/mdev-gb/
COPY --chown=www-data:www-data wp-content/plugins/mdev-gb/src/ /var/www/html/wp-content/plugins/mdev-gb/src/
COPY --from=assets --chown=www-data:www-data /build/mdev-gb/build/ /var/www/html/wp-content/plugins/mdev-gb/build/
