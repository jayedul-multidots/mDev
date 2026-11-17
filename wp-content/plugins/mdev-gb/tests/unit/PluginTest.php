<?php
/**
 * Covers Mdev\Gb\Plugin.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Mdev\Gb\Blocks\BlockCategory;
use Mdev\Gb\Blocks\BlockRegistrar;
use Mdev\Gb\HelpfulPoll\RestController as HelpfulPollRestController;
use Mdev\Gb\HelpfulPoll\VoteCounts;
use Mdev\Gb\Plugin;
use Mdev\Gb\PostGallery\RestController as PostGalleryRestController;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the composition root wires features to the right hooks.
 */
final class PluginTest extends TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Set up Brain Monkey.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down Brain Monkey.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Boot registers block types and the helpful poll meta on init, the
	 * category filter, and the helpful poll REST route.
	 */
	public function test_boot_wires_hooks(): void {
		Actions\expectAdded( 'init' )
			->once()
			->with( \Mockery::on( static fn ( $cb ) => is_array( $cb ) && $cb[0] instanceof BlockRegistrar && 'register' === $cb[1] ) );

		Actions\expectAdded( 'init' )
			->once()
			->with( \Mockery::on( static fn ( $cb ) => is_array( $cb ) && $cb[0] instanceof VoteCounts && 'register_meta' === $cb[1] ) );

		Actions\expectAdded( 'rest_api_init' )
			->once()
			->with( \Mockery::on( static fn ( $cb ) => is_array( $cb ) && $cb[0] instanceof PostGalleryRestController && 'register_routes' === $cb[1] ) );

		Actions\expectAdded( 'rest_api_init' )
			->once()
			->with( \Mockery::on( static fn ( $cb ) => is_array( $cb ) && $cb[0] instanceof HelpfulPollRestController && 'register_routes' === $cb[1] ) );

		Filters\expectAdded( 'block_categories_all' )
			->once()
			->with( \Mockery::on( static fn ( $cb ) => is_array( $cb ) && $cb[0] instanceof BlockCategory && 'register' === $cb[1] ) );

		( new Plugin( '/srv/plugin' ) )->boot();
	}
}
