<?php
/**
 * Shared setup for helpful poll unit tests.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\HelpfulPoll;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Brain Monkey with translation/escaping stubs and an in-memory post store:
 * get_post() returns the posts added with add_post(), and
 * is_post_publicly_viewable() treats published posts of `post`/`page` as
 * public.
 */
abstract class HelpfulPollTestCase extends TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Posts by ID.
	 *
	 * @var array<int, WP_Post>
	 */
	protected array $posts = [];

	/**
	 * Set up Brain Monkey and common stubs.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\stubTranslationFunctions();
		Functions\stubEscapeFunctions();
		Functions\stubs(
			[
				'absint'                    => static fn ( mixed $value ): int => abs( (int) $value ),
				'wp_strip_all_tags'         => static fn ( string $text ): string => trim( strip_tags( $text ) ),
				'get_post'                  => fn ( mixed $id ): ?WP_Post => $this->posts[ (int) $id ] ?? null,
				'is_post_publicly_viewable' => static fn ( WP_Post $post ): bool => 'publish' === $post->post_status
					&& in_array( $post->post_type, [ 'post', 'page' ], true ),
				'rest_url'                  => static fn ( string $path ): string => 'https://example.test/wp-json/' . ltrim( $path, '/' ),
			]
		);
	}

	/**
	 * Tear down Brain Monkey.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Add a post to the in-memory store.
	 *
	 * @param int                  $id    Post ID.
	 * @param array<string, mixed> $props Extra properties.
	 * @return WP_Post
	 */
	protected function add_post( int $id, array $props = [] ): WP_Post {
		$this->posts[ $id ] = new WP_Post( [ 'ID' => $id ] + $props );
		return $this->posts[ $id ];
	}
}
