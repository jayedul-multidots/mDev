# Copilot instructions

Follow all rules in the repository's `AGENTS.md` file. They are non-negotiable and override task-specific instructions where they conflict.

Summary of the non-negotiables (see AGENTS.md for the full contract):

- Escape all output late (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); sanitize all input early.
- All SQL through `$wpdb->prepare()`; prefer core APIs over raw SQL.
- Nonce + `current_user_can()` capability check on every state change; real `permission_callback` on every REST route.
- No filesystem writes, no `ini_set()`, no PHP sessions, no `eval()`, no hardcoded secrets, no ACF.
- Cache remote requests and expensive queries; no unbounded queries (`posts_per_page => -1`), no `orderby => rand`, no `query_posts()`.
- Internationalize all user-facing strings with the project text domain.
- Business logic in small, dependency-injected, testable classes — hooks stay thin. Deliver PHPUnit tests with every non-trivial change.
- Code must pass the WordPress-Extra and WordPress-Docs PHPCS rulesets (plus WordPress-VIP-Go if configured in `phpcs.xml.dist`) and PHPStan at the repo's configured level.
