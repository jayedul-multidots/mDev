<?php
/**
 * Title: Footer
 * Slug: mdev-fse/footer
 * Categories: mdev-fse, footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * Site identity, three link columns and a copyright line. Links are built
 * with home_url() so they are correct on every environment; edit them in the
 * Site Editor (Patterns → Footer) or here.
 *
 * @package Mdev\Fse
 */

$mdev_fse_privacy_url = get_privacy_policy_url();

$mdev_fse_columns = [
	[
		'title' => __( 'Explore', 'mdev-fse' ),
		'links' => [
			[ __( 'Home', 'mdev-fse' ), home_url( '/' ) ],
			[ __( 'Blog', 'mdev-fse' ), home_url( '/blog/' ) ],
			[ __( 'Search', 'mdev-fse' ), home_url( '/?s=' ) ],
		],
	],
	[
		'title' => __( 'Company', 'mdev-fse' ),
		'links' => [
			[ __( 'About', 'mdev-fse' ), home_url( '/about/' ) ],
			[ __( 'Contact', 'mdev-fse' ), home_url( '/contact/' ) ],
		],
	],
	[
		'title' => __( 'Legal', 'mdev-fse' ),
		'links' => [
			[ __( 'Privacy Policy', 'mdev-fse' ), '' !== $mdev_fse_privacy_url ? $mdev_fse_privacy_url : home_url( '/privacy-policy/' ) ],
			[ __( 'Terms of Use', 'mdev-fse' ), home_url( '/terms/' ) ],
		],
	],
];
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:site-title {"level":0} /-->
			<!-- wp:site-tagline {"textColor":"muted","fontSize":"small"} /-->
		</div>
		<!-- /wp:column -->

		<?php foreach ( $mdev_fse_columns as $mdev_fse_column ) : ?>
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2,"fontSize":"medium"} -->
			<h2 class="wp-block-heading has-medium-font-size"><?php echo esc_html( $mdev_fse_column['title'] ); ?></h2>
			<!-- /wp:heading -->

			<?php
			// serialize_block_attributes() escapes for the block comment context.
			echo '<!-- wp:navigation ' . serialize_block_attributes( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see above.
				[
					'overlayMenu' => 'never',
					'ariaLabel'   => $mdev_fse_column['title'],
					'layout'      => [
						'type'        => 'flex',
						'orientation' => 'vertical',
					],
				]
			) . ' -->';
			foreach ( $mdev_fse_column['links'] as [ $mdev_fse_label, $mdev_fse_url ] ) {
				echo '<!-- wp:navigation-link ' . serialize_block_attributes( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see above.
					[
						'label' => $mdev_fse_label,
						'url'   => esc_url_raw( $mdev_fse_url ),
						'kind'  => 'custom',
					]
				) . ' /-->';
			}
			echo '<!-- /wp:navigation -->';
			?>
		</div>
		<!-- /wp:column -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"padding":{"top":"var:preset|spacing|30"}},"border":{"top":{"color":"var:preset|color|muted","width":"1px"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group alignwide" style="border-top-color:var(--wp--preset--color--muted);border-top-width:1px;margin-top:var(--wp--preset--spacing--50);padding-top:var(--wp--preset--spacing--30)">
		<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
		<p class="has-muted-color has-text-color has-small-font-size">
			<?php
			/* translators: 1: current year, 2: site name. */
			echo esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'mdev-fse' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) );
			?>
		</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
