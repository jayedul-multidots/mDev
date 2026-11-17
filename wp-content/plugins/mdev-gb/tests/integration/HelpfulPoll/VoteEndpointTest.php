<?php
/**
 * Covers Mdev\Gb\HelpfulPoll\RestController, VoteCounts and the block's
 * render.php against a real WordPress: REST server, argument validation,
 * registered meta, REST exposure contexts and Interactivity API rendering.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Integration\HelpfulPoll;

use Mdev\Gb\HelpfulPoll\Vote;
use Mdev\Gb\HelpfulPoll\VoteCounts;
use WP_REST_Request;
use WP_REST_Response;
use WP_UnitTestCase;

/**
 * `POST /wp-json/mdev-gb/v1/helpful-poll/<id>` and the rendered block.
 */
final class VoteEndpointTest extends WP_UnitTestCase {

	private const CUSTOM_TYPE = 'deep_pockets_videos';

	/**
	 * Fresh REST server per test, and the poll meta registered again: the
	 * core test case unregisters every meta key after each test.
	 */
	public function set_up(): void {
		parent::set_up();
		$GLOBALS['wp_rest_server'] = null;
		( new VoteCounts() )->register_meta();
	}

	/**
	 * Drop the REST server built for this test, the custom post type, and
	 * restore the block's supports (the block registry is global).
	 */
	public function tear_down(): void {
		$GLOBALS['wp_rest_server'] = null;
		if ( post_type_exists( self::CUSTOM_TYPE ) ) {
			unregister_post_type( self::CUSTOM_TYPE );
		}
		$block = \WP_Block_Type_Registry::get_instance()->get_registered( 'mdev-gb/helpful-poll' );
		if ( null !== $block && is_array( $block->supports ) ) {
			unset( $block->supports['inserter'] );
		}
		set_current_screen( 'front' );
		parent::tear_down();
	}

	/**
	 * A public custom post type: the poll is never available for it.
	 *
	 * @return void
	 */
	private function register_custom_type(): void {
		register_post_type(
			self::CUSTOM_TYPE,
			[
				'public'       => true,
				'show_in_rest' => true,
				'supports'     => [ 'title', 'editor', 'custom-fields' ],
			]
		);
	}

	/**
	 * Pages accept votes like posts.
	 */
	public function test_pages_accept_votes(): void {
		$page_id = self::factory()->post->create( [ 'post_type' => 'page' ] );

		$this->assertSame( 201, $this->vote( $page_id, 'no' )->get_status() );
		$this->assertSame( 1, ( new VoteCounts() )->get( $page_id )['no'] );
	}

	/**
	 * Published posts of a public custom post type are rejected and never
	 * render the poll.
	 */
	public function test_custom_post_types_are_rejected(): void {
		$this->register_custom_type();
		$post_id = self::factory()->post->create( [ 'post_type' => self::CUSTOM_TYPE ] );

		$this->assertSame( 404, $this->vote( $post_id, 'yes' )->get_status() );
		$this->assertFalse( metadata_exists( 'post', $post_id, Vote::Yes->meta_key() ) );
		$this->assertSame( '', trim( $this->render( '<!-- wp:mdev-gb/helpful-poll /-->', $post_id ) ) );
	}

	/**
	 * The admin column is added to the Posts and Pages lists only.
	 */
	public function test_admin_column_on_posts_and_pages_only(): void {
		$this->register_custom_type();

		$this->assertArrayHasKey( 'mdev_gb_helpful', apply_filters( 'manage_post_posts_columns', [ 'date' => 'Date' ] ) );
		$this->assertArrayHasKey( 'mdev_gb_helpful', apply_filters( 'manage_page_posts_columns', [ 'date' => 'Date' ] ) );
		$this->assertArrayNotHasKey( 'mdev_gb_helpful', apply_filters( 'manage_' . self::CUSTOM_TYPE . '_posts_columns', [ 'date' => 'Date' ] ) );
	}

	/**
	 * In the editor, the block definitions sent to the browser hide the
	 * block from the inserter for custom post types only.
	 */
	public function test_inserter_hidden_in_custom_post_type_editor(): void {
		$this->register_custom_type();

		set_current_screen( 'post' );
		$this->assertArrayNotHasKey( 'inserter', $this->editor_supports() );

		set_current_screen( self::CUSTOM_TYPE );
		$this->assertFalse( $this->editor_supports()['inserter'] );
	}

	/**
	 * The poll block's supports as the editor receives them.
	 *
	 * @return array<string, mixed>
	 */
	private function editor_supports(): array {
		return get_block_editor_server_block_settings()['mdev-gb/helpful-poll']['supports'] ?? [];
	}

	/**
	 * Anonymous votes on a published post increment that answer's meta.
	 */
	public function test_anonymous_vote_increments_meta(): void {
		$post_id = self::factory()->post->create();
		wp_set_current_user( 0 );

		$this->assertSame( 201, $this->vote( $post_id, 'yes' )->get_status() );
		$this->assertSame( 201, $this->vote( $post_id, 'yes' )->get_status() );
		$this->assertSame( 201, $this->vote( $post_id, 'no' )->get_status() );

		$this->assertSame(
			[
				'yes' => 2,
				'no'  => 1,
			],
			( new VoteCounts() )->get( $post_id )
		);
		$this->assertSame( [ 'recorded' => true ], $this->vote( $post_id, 'no' )->get_data() );
	}

	/**
	 * Posts visitors cannot read are 404s and keep no counts.
	 *
	 * @dataProvider unreadable_posts
	 *
	 * @param array<string, string> $props Post properties.
	 */
	public function test_unreadable_posts_are_not_found( array $props ): void {
		$post_id = self::factory()->post->create( $props );

		$response = $this->vote( $post_id, 'yes' );

		$this->assertSame( 404, $response->get_status() );
		$this->assertFalse( metadata_exists( 'post', $post_id, Vote::Yes->meta_key() ) );
	}

	/**
	 * Unreadable post cases.
	 *
	 * @return array<string, array{array<string, string>}>
	 */
	public function unreadable_posts(): array {
		return [
			'draft'              => [ [ 'post_status' => 'draft' ] ],
			'private'            => [ [ 'post_status' => 'private' ] ],
			'password protected' => [ [ 'post_password' => 'secret' ] ],
		];
	}

	/**
	 * A post ID that does not exist is a 404 too.
	 */
	public function test_missing_post_is_not_found(): void {
		$this->assertSame( 404, $this->vote( 999999, 'yes' )->get_status() );
	}

	/**
	 * Votes outside the enum, or none, are rejected by the schema.
	 */
	public function test_invalid_votes_are_rejected(): void {
		$post_id = self::factory()->post->create();

		foreach ( [ 'maybe', '', null ] as $vote ) {
			$response = $this->vote( $post_id, $vote );
			$this->assertSame( 400, $response->get_status(), 'Vote: ' . var_export( $vote, true ) );
		}

		$this->assertSame( 0, ( new VoteCounts() )->get( $post_id )['yes'] );
	}

	/**
	 * Visitors do not see the counts in the core posts endpoint; editors
	 * (edit context) do, but cannot change them through it.
	 */
	public function test_counts_are_exposed_to_editors_only(): void {
		$post_id = self::factory()->post->create();
		update_post_meta( $post_id, Vote::Yes->meta_key(), 4 );

		wp_set_current_user( 0 );
		$public = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/posts/' . $post_id ) )->get_data();
		$this->assertArrayNotHasKey( Vote::Yes->meta_key(), (array) ( $public['meta'] ?? [] ) );

		wp_set_current_user( self::factory()->user->create( [ 'role' => 'editor' ] ) );
		$request = new WP_REST_Request( 'GET', '/wp/v2/posts/' . $post_id );
		$request->set_param( 'context', 'edit' );
		$edit = rest_do_request( $request )->get_data();
		$this->assertSame( 4, $edit['meta'][ Vote::Yes->meta_key() ] );

		$update = new WP_REST_Request( 'POST', '/wp/v2/posts/' . $post_id );
		$update->set_body_params( [ 'meta' => [ Vote::Yes->meta_key() => 1000 ] ] );
		$this->assertSame( 403, rest_do_request( $update )->get_status() );
		$this->assertSame( '4', get_post_meta( $post_id, Vote::Yes->meta_key(), true ) );
	}

	/**
	 * The block renders the poll for a published post, escaped, with the
	 * Interactivity API context; it renders nothing for drafts.
	 */
	public function test_block_renders_for_published_posts_only(): void {
		$published = self::factory()->post->create();
		$draft     = self::factory()->post->create( [ 'post_status' => 'draft' ] );
		$markup    = '<!-- wp:mdev-gb/helpful-poll {"yesLabel":"Yes <script>"} /-->';

		$html = $this->render( $markup, $published );

		$this->assertStringContainsString( 'data-wp-interactive="mdev-gb/helpful-poll"', $html );
		$this->assertStringContainsString( 'Was this article helpful?', $html );
		$this->assertStringContainsString( 'Yes &lt;script&gt;', $html );
		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringContainsString( 'helpful-poll\/' . $published, $html );
		$this->assertSame( '', trim( $this->render( $markup, $draft ) ) );
	}

	/**
	 * Render block markup as part of a post's content.
	 *
	 * @param string $markup  Block markup.
	 * @param int    $post_id Post ID (block context).
	 * @return string
	 */
	private function render( string $markup, int $post_id ): string {
		$blocks = parse_blocks( $markup );
		$block  = new \WP_Block(
			$blocks[0],
			[
				'postId'   => $post_id,
				'postType' => 'post',
			]
		);
		return $block->render();
	}

	/**
	 * Dispatch a vote.
	 *
	 * @param int   $post_id Post ID.
	 * @param mixed $vote    Vote value, null to omit it.
	 * @return WP_REST_Response
	 */
	private function vote( int $post_id, mixed $vote ): WP_REST_Response {
		$request = new WP_REST_Request( 'POST', '/mdev-gb/v1/helpful-poll/' . $post_id );
		if ( null !== $vote ) {
			$request->set_header( 'Content-Type', 'application/json' );
			$request->set_body( (string) wp_json_encode( [ 'vote' => $vote ] ) );
		}
		return rest_do_request( $request );
	}
}
