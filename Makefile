# mdev local development helpers. Run `make help`.
# Everything runs in containers: no host PHP/Composer required.

SHELL := /bin/bash
COMPOSE := docker compose
TOOLS := $(COMPOSE) --profile tools run --rm
WP := $(COMPOSE) exec -u www-data wordpress wp
# Host npm is much faster than a bind-mounted container on macOS; fall back to the container.
NPM := $(shell command -v npm >/dev/null 2>&1 && echo "npm --prefix wp-content/plugins/mdev-gb" || echo "$(TOOLS) node npm")

export MDEV_UID := $(shell id -u)
export MDEV_GID := $(shell id -g)

.DEFAULT_GOAL := help

.PHONY: help
help: ## List available targets
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: .env network install build up provision ## First-time setup: deps, assets, containers, WordPress install
	@echo "Ready: $$(grep '^WP_HOME=' .env | cut -d= -f2)/wp-admin"

.env:
	cp .env.example .env
	@$(MAKE) --no-print-directory salts-write
	@echo "Created .env from .env.example - review it."

.PHONY: network
network: ## Create the external traenet network if missing
	@docker network inspect traenet >/dev/null 2>&1 || docker network create traenet

.PHONY: install
install: composer-install npm-install ## Install Composer and npm dependencies

.PHONY: composer-install
composer-install: ## Composer install (third-party plugins + tooling)
	$(TOOLS) composer install

.PHONY: composer-update
composer-update: ## Composer update (refresh composer.lock)
	$(TOOLS) composer update

.PHONY: npm-install
npm-install: ## npm ci for mdev-gb
	$(NPM) ci

.PHONY: build
build: ## Build mdev-gb block assets
	$(NPM) run build

.PHONY: watch
watch: ## Rebuild mdev-gb block assets on change
	$(NPM) run start

.PHONY: up
up: network ## Start containers
	$(COMPOSE) up -d --build --wait

.PHONY: down
down: ## Stop containers
	$(COMPOSE) down

.PHONY: provision
provision: ## Install/upgrade WordPress, activate theme and plugins
	$(COMPOSE) exec -T -u www-data wordpress mdev-provision

.PHONY: logs
logs: ## Follow container logs
	$(COMPOSE) logs -f

.PHONY: debug-log
debug-log: ## Follow the WordPress debug log (PHP notices, warnings, errors)
	$(COMPOSE) exec wordpress tail -n 50 -f /tmp/wp-debug.log

.PHONY: shell
shell: ## Shell in the wordpress container (www-data)
	$(COMPOSE) exec -u www-data wordpress bash

.PHONY: wp
wp: ## Run WP-CLI, e.g. make wp ARGS="plugin list"
	$(WP) $(ARGS)

.PHONY: npm
npm: ## Run npm for mdev-gb inside the container, e.g. make npm ARGS="run test:unit" (first time: ARGS=ci)
	$(COMPOSE) exec -w /var/www/html/wp-content/plugins/mdev-gb wordpress npm $(ARGS)

.PHONY: check
check: lint test ## Run the full quality gate (PHP + JS)

.PHONY: lint
lint: ## PHPCS, PHPStan, ESLint, Stylelint
	$(TOOLS) composer phpcs
	$(TOOLS) composer phpstan
	$(NPM) run lint:js
	$(NPM) run lint:css

.PHONY: test
test: ## PHPUnit and Jest
	$(TOOLS) composer test
	$(NPM) run test:unit

.PHONY: image
image: ## Build the production image locally (mdev/wordpress:prod)
	docker build --target production -t mdev/wordpress:prod .

.PHONY: salts
salts: ## Print fresh WordPress salts for .env
	@for k in AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT; do \
		echo "WORDPRESS_$$k=$$(openssl rand -base64 48 | tr -d '\n=+/')"; \
	done

.PHONY: salts-write
salts-write:
	@for k in AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT; do \
		v=$$(openssl rand -base64 48 | tr -d '\n=+/'); \
		sed -i.bak "s|^WORDPRESS_$$k=.*|WORDPRESS_$$k=$$v|" .env; \
	done; rm -f .env.bak
