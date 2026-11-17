<?php
/**
 * Helpful poll column in the Posts and Pages lists.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

/**
 * Adds a "Helpful" column with each post's Yes / No counts to the admin
 * list tables of the post types the poll is available for.
 *
 * The list table primes the meta cache for every listed post, so reading
 * the counts adds no queries per row. Counts are shown only to users who can
 * edit the post, matching their REST exposure (edit context only).
 *
 * @since 0.3.0
 */
final class AdminColumns {

	public const COLUMN = 'mdev_gb_helpful';

	/**
	 * Constructor.
	 *
	 * @since 0.3.0
	 *
	 * @param VoteCounts $counts Count storage.
	 */
	public function __construct( private readonly VoteCounts $counts ) {
	}

	/**
	 * Add the column before the date column (or last when there is none).
	 * Hooked to `manage_{$post_type}_posts_columns`.
	 *
	 * @since 0.3.0
	 *
	 * @param array<string, string> $columns Column labels by key.
	 * @return array<string, string>
	 */
	public function add_column( array $columns ): array {
		$column = [ self::COLUMN => __( 'Helpful', 'mdev-gb' ) ];
		$offset = array_search( 'date', array_keys( $columns ), true );

		if ( false === $offset ) {
			return $columns + $column;
		}

		return array_slice( $columns, 0, $offset, true ) + $column + array_slice( $columns, $offset, null, true );
	}

	/**
	 * Print the cell. Hooked to `manage_{$post_type}_posts_custom_column`.
	 *
	 * @since 0.3.0
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_column( string $column, int $post_id ): void {
		if ( self::COLUMN !== $column ) {
			return;
		}

		echo $this->cell( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cell() escapes every value it returns.
	}

	/**
	 * Cell markup (escaped).
	 *
	 * @since 0.3.0
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function cell( int $post_id ): string {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $this->empty_cell( __( 'Not available', 'mdev-gb' ) );
		}

		$counts = $this->counts->get( $post_id );

		if ( 0 === $counts['yes'] + $counts['no'] ) {
			return $this->empty_cell( __( 'No votes yet', 'mdev-gb' ) );
		}

		return sprintf(
			'<span class="mdev-gb-helpful-yes">%s</span><br><span class="mdev-gb-helpful-no">%s</span>',
			esc_html(
				sprintf(
					/* translators: %s: number of "Yes" answers. */
					__( 'Yes: %s', 'mdev-gb' ),
					number_format_i18n( $counts['yes'] )
				)
			),
			esc_html(
				sprintf(
					/* translators: %s: number of "No" answers. */
					__( 'No: %s', 'mdev-gb' ),
					number_format_i18n( $counts['no'] )
				)
			)
		);
	}

	/**
	 * An em dash with a screen reader explanation, as core list tables do.
	 *
	 * @param string $label Screen reader text.
	 * @return string
	 */
	private function empty_cell( string $label ): string {
		return sprintf(
			'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
			esc_html( $label )
		);
	}
}
