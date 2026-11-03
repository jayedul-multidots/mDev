<?php
/**
 * View: mdev-gb/post-gallery.
 *
 * Included by GalleryRenderer::render(). Directive values are processed by
 * the Interactivity API on the server (first render, including the
 * data-wp-each list) and in the browser (later updates).
 *
 * Escaping: static values are escaped here; card fields are plain text and
 * raw URLs that the Interactivity API escapes on output (esc_html / esc_attr
 * on the server, textContent / setAttribute in the browser).
 *
 * @package Mdev\Gb
 *
 * @var array{id: string, prefix: string, settings: \Mdev\Gb\PostGallery\GallerySettings, query: \Mdev\Gb\PostGallery\GalleryQuery, context: array<string, mixed>} $view
 */

use Mdev\Gb\PostGallery\GalleryRenderer;
use Mdev\Gb\PostGallery\SortOption;
use Mdev\Gb\PostGallery\UrlParams;

defined( 'ABSPATH' ) || exit;

$mdev_gb_settings = $view['settings'];
$mdev_gb_query    = $view['query'];
$mdev_gb_context  = $view['context'];
$mdev_gb_id       = $view['id'];
$mdev_gb_heading  = tag_escape( 'h' . $mdev_gb_settings->heading_level );
$mdev_gb_wrapper  = get_block_wrapper_attributes(
	[
		'style' => '--mdev-pg-columns:' . $mdev_gb_settings->columns . ';',
	]
);
?>
<div
	<?php echo $mdev_gb_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes every attribute value. ?>
	data-wp-interactive="<?php echo esc_attr( GalleryRenderer::STORE ); ?>"
	<?php echo wp_interactivity_data_wp_context( $mdev_gb_context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns an attribute escaped with esc_attr(). ?>
	data-wp-class--is-loading="context.isLoading"
>
	<?php if ( $mdev_gb_settings->show_search || $mdev_gb_settings->show_sort ) : ?>
		<form class="wp-block-mdev-gb-post-gallery__filters" role="search" method="get" data-wp-on--submit="actions.submit">
			<?php if ( $mdev_gb_settings->show_search ) : ?>
				<div class="wp-block-mdev-gb-post-gallery__field">
					<label for="<?php echo esc_attr( $mdev_gb_id . '-search' ); ?>"><?php esc_html_e( 'Search posts', 'mdev-gb' ); ?></label>
					<input
						type="search"
						id="<?php echo esc_attr( $mdev_gb_id . '-search' ); ?>"
						name="<?php echo esc_attr( $view['prefix'] . UrlParams::SEARCH ); ?>"
						value="<?php echo esc_attr( $mdev_gb_query->search ); ?>"
						maxlength="<?php echo esc_attr( (string) \Mdev\Gb\PostGallery\GalleryQuery::MAX_SEARCH_LENGTH ); ?>"
						autocomplete="off"
						data-wp-on--input="actions.updateSearch"
					/>
				</div>
			<?php endif; ?>

			<?php if ( $mdev_gb_settings->show_sort ) : ?>
				<div class="wp-block-mdev-gb-post-gallery__field">
					<label for="<?php echo esc_attr( $mdev_gb_id . '-sort' ); ?>"><?php esc_html_e( 'Sort by', 'mdev-gb' ); ?></label>
					<select
						id="<?php echo esc_attr( $mdev_gb_id . '-sort' ); ?>"
						name="<?php echo esc_attr( $view['prefix'] . UrlParams::SORT ); ?>"
						data-wp-on--change="actions.updateSort"
					>
						<?php foreach ( SortOption::cases() as $mdev_gb_option ) : ?>
							<option value="<?php echo esc_attr( $mdev_gb_option->value ); ?>" <?php selected( $mdev_gb_option->value, $mdev_gb_query->sort->value ); ?>>
								<?php echo esc_html( $mdev_gb_option->label() ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<button type="submit" class="wp-block-mdev-gb-post-gallery__submit wp-element-button"><?php esc_html_e( 'Apply', 'mdev-gb' ); ?></button>
		</form>
	<?php endif; ?>

	<p class="wp-block-mdev-gb-post-gallery__summary" role="status" aria-live="polite" data-wp-text="context.summary"></p>

	<ul class="wp-block-mdev-gb-post-gallery__grid" data-wp-bind--aria-busy="context.isLoading">
		<template data-wp-each--post="context.items" data-wp-each-key="context.post.id">
			<li class="wp-block-mdev-gb-post-gallery__item">
				<article class="wp-block-mdev-gb-post-gallery__card">
					<?php // Decorative duplicate of the title link: hidden from keyboard and screen readers. ?>
					<a class="wp-block-mdev-gb-post-gallery__media" data-wp-bind--href="context.post.url" data-wp-bind--hidden="!context.post.image" tabindex="-1" aria-hidden="true">
						<img
							alt=""
							loading="lazy"
							decoding="async"
							data-wp-bind--src="context.post.image.src"
							data-wp-bind--srcset="context.post.image.srcset"
							data-wp-bind--sizes="context.post.image.sizes"
							data-wp-bind--width="context.post.image.width"
							data-wp-bind--height="context.post.image.height"
						/>
					</a>
					<<?php echo $mdev_gb_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag_escape() of h2..h6. ?> class="wp-block-mdev-gb-post-gallery__title">
						<a data-wp-bind--href="context.post.url" data-wp-text="context.post.title"></a>
					</<?php echo $mdev_gb_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag_escape() of h2..h6. ?>>
					<?php if ( $mdev_gb_settings->show_date ) : ?>
						<time class="wp-block-mdev-gb-post-gallery__date" data-wp-bind--datetime="context.post.datetime" data-wp-text="context.post.date"></time>
					<?php endif; ?>
					<?php if ( $mdev_gb_settings->show_excerpt ) : ?>
						<p class="wp-block-mdev-gb-post-gallery__excerpt" data-wp-text="context.post.excerpt"></p>
					<?php endif; ?>
				</article>
			</li>
		</template>
	</ul>

	<p class="wp-block-mdev-gb-post-gallery__error" role="alert" data-wp-bind--hidden="!context.error">
		<span data-wp-text="context.error"></span>
		<button type="button" class="wp-block-mdev-gb-post-gallery__retry" data-wp-on--click="actions.retry"><?php esc_html_e( 'Try again', 'mdev-gb' ); ?></button>
	</p>

	<p class="wp-block-mdev-gb-post-gallery__more" data-wp-bind--hidden="!context.hasMore">
		<a
			class="wp-element-button"
			href="<?php echo esc_url( (string) $mdev_gb_context['nextUrl'] ); ?>"
			data-wp-bind--href="context.nextUrl"
			data-wp-bind--aria-disabled="context.isLoading"
			data-wp-on--click="actions.loadMore"
		><?php esc_html_e( 'Load more', 'mdev-gb' ); ?></a>
	</p>
</div>
