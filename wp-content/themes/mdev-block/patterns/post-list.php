<?php
/**
 * Title: Post list
 * Slug: mdev-block/post-list
 * Categories: mdev-block, query
 * Inserter: no
 *
 * @package Mdev\Block
 */

?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true}} -->
<div class="wp-block-query">
	<!-- wp:post-template -->
		<!-- wp:group {"style":{"spacing":{"padding":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group" style="padding-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:post-title {"isLink":true,"level":2} /-->
			<!-- wp:post-date /-->
			<!-- wp:post-excerpt {"moreText":""} /-->
		</div>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph -->
		<p><?php echo esc_html__( 'Nothing found. Try a different search.', 'mdev-block' ); ?></p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
