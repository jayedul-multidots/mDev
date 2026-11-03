<?php
/**
 * Covers Mdev\Gb\Blocks\BlockRegistrar.
 *
 * @package Mdev\Gb\Tests
 */

declare( strict_types=1 );

namespace Mdev\Gb\Tests\Unit\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mdev\Gb\Blocks\BlockRegistrar;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Registration happens only when a built manifest exists.
 */
final class BlockRegistrarTest extends TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Temporary build directory.
	 *
	 * @var string
	 */
	private string $build_dir;

	/**
	 * Set up Brain Monkey and an empty build directory.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		$this->build_dir = sys_get_temp_dir() . '/mdev-gb-build-' . uniqid();
		mkdir( $this->build_dir );
	}

	/**
	 * Tear down Brain Monkey and remove the build directory.
	 */
	protected function tearDown(): void {
		$manifest = $this->build_dir . '/' . BlockRegistrar::MANIFEST;
		if ( file_exists( $manifest ) ) {
			unlink( $manifest );
		}
		rmdir( $this->build_dir );
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Blocks are registered from the manifest when it exists.
	 */
	public function test_registers_collection_when_manifest_exists(): void {
		$manifest = $this->build_dir . '/' . BlockRegistrar::MANIFEST;
		file_put_contents( $manifest, "<?php return [];\n" );

		Functions\expect( 'wp_register_block_types_from_metadata_collection' )
			->once()
			->with( $this->build_dir, $manifest );

		( new BlockRegistrar( $this->build_dir ) )->register();
	}

	/**
	 * Nothing is registered (and nothing fatals) before assets are built.
	 */
	public function test_does_nothing_without_manifest(): void {
		Functions\expect( 'wp_register_block_types_from_metadata_collection' )->never();

		( new BlockRegistrar( $this->build_dir ) )->register();
	}
}
