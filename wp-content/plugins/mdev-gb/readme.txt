=== mdev Gutenberg Blocks ===
Requires at least: 6.8
Requires PHP: 8.3
Stable tag: 0.1.0
License: GPL-2.0-or-later

Custom block editor blocks for the mdev site.

== Description ==

Blocks live in `blocks/<name>/` (block.json, edit/save, render.php, styles)
and are compiled to `build/` by `npm run build`. Every block in the generated
`build/blocks-manifest.php` is registered automatically.

Conventions: block names are `mdev-gb/<slug>` (lowercase), titles are
prefixed "mDev ", and every block uses the `mdev` category ("mDev").

Blocks:

* `mdev-gb/callout` (mDev Callout) - dynamic note/tip/warning box with inner blocks.
