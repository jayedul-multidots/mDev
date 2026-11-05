# mdev

Dockerized WordPress with versioned custom code, Composer-managed third-party
plugins, and a GitHub Actions CI/CD pipeline that ships an immutable image.

| Path | What | Versioned |
|---|---|---|
| `wp-content/plugins/mdev-gb` | Custom block plugin (PSR-4 PHP + `@wordpress/scripts` blocks) | yes |
| `wp-content/themes/mdev-fse` | Full site editing block theme (templates, parts, patterns, dark style variation) | yes |
| `wp-content/themes/mdev-block` | Lean editorial block theme (minimal template set, serif tokens) | yes |
| `composer.json` | Third-party plugins (wpackagist) + quality tooling | yes |
| other `wp-content/plugins/*` | Installed by Composer | no |
| `Dockerfile` | `dev` and `production` targets | yes |
| `docker-compose.yml` | Base stack (servers) | yes |
| `docker-compose.override.yml` | Local bind mounts + tool containers | yes, never deployed |
| `.env` | All environment-specific values | **no** (template: `.env.example`) |

## Local setup

Requires Docker and the Traefik stack on the external `traenet` network, with
`mdev.test` in `/etc/hosts`.

```sh
make init     # .env (with fresh salts), composer + npm install, build, up, install WP
```

Then open http://mdev.test/wp-admin. phpMyAdmin is at http://localhost:8081
(local only, bound to `127.0.0.1`, logged in as the WordPress database user;
port via `PMA_PORT` in `.env`). Admin credentials are `WP_ADMIN_USER` /
`WP_ADMIN_PASSWORD` in `.env`.

Daily commands (`make help` lists everything):

```sh
make up / make down       # start / stop
make watch                # rebuild blocks on change
make wp ARGS="plugin list"
make check                # PHPCS, PHPStan, PHPUnit, ESLint, Stylelint, Vitest
make provision            # re-run install/activation (idempotent)
make image                # build the production image locally
```

The custom plugin and themes are bind-mounted, so edits are live. PHP tooling
runs in a `composer:2` container; npm uses host Node (22.13 or newer) when
available, otherwise a `node:22` container.

The `wordpress` dev container also has Composer and Node/npm, so tests can run
inside it:

```sh
make npm ARGS=ci                 # once: Linux node_modules (kept in a Docker volume)
make npm ARGS="run test:unit"    # Vitest
docker compose exec -w /var/www/html/wp-content/plugins/mdev-gb wordpress composer test   # PHPUnit
```

The container's `node_modules` lives in the `mdev_gb_node_modules` volume and
hides the host's copy, because packages such as esbuild ship OS-specific
binaries. Your macOS `node_modules` keeps working on the host.

## Third-party plugins

Add them to `composer.json`, then refresh the lock:

```sh
make composer-update                                   # after editing composer.json
# e.g. "wpackagist-plugin/wp-mail-smtp": "^4.0" in "require"
```

- `require` → baked into the production image on every deploy.
- `require-dev` → local only (e.g. Query Monitor).

Activation is controlled per environment by `WP_ACTIVATE_PLUGINS` in `.env`
(space-separated slugs, or `all`). Provisioning fails loudly if a listed plugin
is not in the image.

## Adding a block

Create `wp-content/plugins/mdev-gb/blocks/<name>/block.json` (plus edit/save,
`render.php` for dynamic blocks, styles). `npm run build` adds it to
`build/blocks-manifest.php` and `BlockRegistrar` registers it. No PHP changes
needed. See `blocks/callout` for the pattern.

## CI/CD

- **CI** (`.github/workflows/ci.yml`): every PR and push to `main` runs PHPCS
  (WordPress-Extra + Docs), PHPStan level 8, PHPUnit + Brain Monkey, ESLint,
  Stylelint, Vitest, the block build, and a production image build.
- **Deploy** (`.github/workflows/deploy.yml`): pushing to `staging` or
  `production` runs CI, builds and pushes
  `ghcr.io/<owner>/<repo>:sha-<commit>`, then over SSH uploads
  `docker-compose.yml` + `scripts/` and runs `scripts/deploy.sh`. That pins the
  image tag in `.env`, pulls, runs `up --wait`, then runs `mdev-provision`
  (DB upgrade + theme/plugin activation).

### Server preparation (once per environment)

1. Install Docker with the compose plugin and run the Traefik stack on `traenet`.
   The deploy user needs Docker access: either membership of the `docker` group
   (`sudo usermod -aG docker ubuntu`, then log in again), or sudo rights plus the
   `SUDO_PASSWORD` secret below.
2. Create the deploy directory (e.g. `/home/ubuntu/mdev`) and put a `.env` in it
   based on `.env.example`, with:
   - a unique `COMPOSE_PROJECT_NAME` (e.g. `mdev`, `mdev-staging`), which also names the Traefik router
   - `WP_DOMAIN`, `WP_HOME` (`https://…`), `TRAEFIK_ENTRYPOINTS=http`
   - HTTPS settings: see [Cloudflare origin certificate](#cloudflare-origin-certificate-https) below
   - `WP_ENVIRONMENT_TYPE=production`, `WP_DISALLOW_FILE_MODS=true`, `WORDPRESS_DEBUG=`
   - strong DB passwords and salts (`make salts`)
   - `WP_AUTO_INSTALL=true` for a fresh site, or `false` if you import a database
3. In GitHub → Settings → Environments, create `staging` and/or `production` with:
   - secrets: `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY` (the private key file, not `.pub`),
     `SSH_PASSPHRASE` if that key has a passphrase, `SUDO_PASSWORD` if the user
     needs sudo for Docker, optional `SSH_KNOWN_HOSTS`,
     optional `GHCR_PULL_TOKEN` (a PAT with `read:packages`, so the server can pull for manual rollbacks)
   - variables: `DEPLOY_PATH`, optional `SSH_PORT`, optional `SITE_URL`

### Cloudflare origin certificate (HTTPS)

The site serves HTTPS itself, using a Cloudflare Origin Certificate kept in a
host folder (`SSL_DIR`, default `./ssl`) that is mounted into the container.
The shared Traefik needs **no changes**. A Traefik TCP router (labels in
`docker-compose.yml`) passes TLS for `WP_DOMAIN` through untouched, selected by
SNI, to Apache on `:443`. Every other domain on Traefik's `:443` keeps working.

```
Visitor ─HTTPS─► Cloudflare ─HTTPS (origin cert)─► Traefik :443 ─passthrough─► Apache :443 (mdev)
```

HTTPS turns on only when **both** `origin.crt` and `origin.key` exist in
`SSL_DIR`. With an empty folder (local, CI) Apache does not listen on `:443`.

1. Cloudflare dashboard → your domain → **SSL/TLS → Origin Server → Create
   Certificate** (RSA or ECC, hostnames e.g. `example.com, *.example.com`, PEM).
   Copy the **Origin Certificate** and the **Private Key**. Cloudflare shows the
   key only once.
2. On the server, in `DEPLOY_PATH`:
   ```sh
   mkdir -p ssl && chmod 700 ssl
   nano ssl/origin.crt   # paste the Origin Certificate
   nano ssl/origin.key   # paste the Private Key
   chmod 600 ssl/origin.key
   ```
   Create the folder yourself before the first deploy. Otherwise Docker creates
   it owned by root. Deploys never touch `ssl/`.
3. In the server's `.env`: `WP_HOME=https://example.com`, `WP_FORCE_SSL_PROXY=false`
   (Apache sees the HTTPS connection itself). Keep `SSL_DIR=./ssl` and
   `TRAEFIK_TLS_ENTRYPOINTS=https` (the defaults).
4. Cloudflare → **SSL/TLS → Overview → Full (strict)**, and turn on
   **Edge Certificates → Always Use HTTPS**.
5. Deploy, or on the server run `docker compose -f docker-compose.yml up -d`
   to restart with the certificate (Apache reads it at start-up).

Origin certificates are trusted **only by Cloudflare**, not by browsers, so
the DNS records must stay **Proxied** (orange cloud). Apache allows TLS 1.2+ and
sends an HSTS header (one year). To renew or replace the certificate, swap the
two files and restart the `wordpress` container.

### Rollback

```sh
ssh -t server 'cd /home/ubuntu/mdev && ./scripts/deploy.sh ghcr.io/<owner>/<repo>:sha-<previous>'
```

`-t` lets sudo ask for your password if the user is not in the `docker` group.

Code rollbacks are image swaps. The database (`DB_DATA_DIR`, default
`./data/mariadb` in the deploy directory) and uploads (`uploads` volume) are not
rolled back, so back them up separately.

## Runtime model

- Production containers are immutable. Core, themes and plugins live in the
  image, `DISALLOW_FILE_MODS` is on, and only `wp-content/uploads` (volume) is
  writable.
- `wp-config.php` is generated from environment variables
  (`docker/wordpress/wp-config-mdev.php`). No secrets in the repo.
- MariaDB files live in a host directory, `DB_DATA_DIR` (default
  `./data/mariadb` under the project root). It is git-ignored and excluded from
  the image build context. Deleting it deletes the database.
- The DB is reachable only on the project's private network, under the alias
  `<COMPOSE_PROJECT_NAME>-db`. Only WordPress joins `traenet`.

## Standards

The engineering contract is in `AGENTS.md`. Run `make check` before pushing.
