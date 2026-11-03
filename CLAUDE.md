# mdev

Dockerized WordPress site. Custom code: plugin `wp-content/plugins/mdev-gb`,
block themes `wp-content/themes/mdev-block` and `wp-content/themes/mdev-fse`.
Third-party plugins are Composer (wpackagist) dependencies in the root
`composer.json` and are never committed. See README.md for workflows.

- Host has no working PHP/Composer: use `make` targets (they run in containers).
- Environment-specific values go in `.env` only (template: `.env.example`).
- New blocks go in `wp-content/plugins/mdev-gb/blocks/<name>/`; they are
  auto-registered from the build manifest.

## ewpDOT

Follow all rules in `AGENTS.md`. They are non-negotiable and override
task-specific instructions where they conflict. Before declaring a coding task
complete, run `make check` (PHPCS, PHPStan, PHPUnit, ESLint, Stylelint, Jest)
and fix all failures.
