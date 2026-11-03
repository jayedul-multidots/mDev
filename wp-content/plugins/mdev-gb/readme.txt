=== mdev Gutenberg Blocks ===
Requires at least: 6.8
Requires PHP: 8.3
Stable tag: 0.2.0
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
* `mdev-gb/post-gallery` (mDev Post Gallery) - grid of posts with search,
  sort and "Load more", powered by the Interactivity API and a REST endpoint.

== mDev Post Gallery ==

* Server-rendered first page (works without JavaScript: the filters are a GET
  form and "Load more" is a link, using `?pg1_q=…&pg1_sort=…&pg1_page=…`).
* With JavaScript: debounced search-as-you-type, instant sort, appended
  "Load more", outdated requests aborted (latest wins), shareable URL kept in
  sync, error message with retry, results announced in a live region.
* REST: `GET /wp-json/mdev-gb/v1/post-gallery` with `search`, `sort`,
  `default_sort`, `page`, `per_page` (max 24), `post_type` (public types
  only), `categories`, `image_size`. Published, non-password posts only.
* Results are object-cached for 5 minutes per query; any post change
  invalidates them (keys include the `posts` last-changed stamp).

Code: PHP in `src/PostGallery/`, template in `views/post-gallery.php`, block
and front-end store in `blocks/post-gallery/` (logic in `lib/`, tests in
`test/`). PHPUnit tests in `tests/unit/PostGallery/`.

== Changelog ==

= 0.2.0 =
* New block: mDev Post Gallery.

= 0.1.0 =
* mDev Callout block.
