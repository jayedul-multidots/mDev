<?php
/**
 * Minimal stand-ins for WordPress classes used by unit tests.
 *
 * Unit tests run without WordPress; these provide just the surface the
 * plugin touches. Each is defined only if the real class is absent.
 *
 * @package Mdev\Gb\Tests
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound, Squiz.Commenting, WordPress.NamingConventions

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Post stand-in.
	 */
	class WP_Post {
		public int $ID = 0;
		public string $post_title = '';

		public function __construct( array $props = [] ) {
			foreach ( $props as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
}

if ( ! class_exists( 'WP_Query' ) ) {
	/**
	 * Query stand-in: tests set the result properties directly.
	 */
	class WP_Query {
		/** @var array<int, mixed>|null */
		public $posts = [];
		public int $found_posts = 0;
		public int $max_num_pages = 0;
		/** @var array<string, mixed> */
		public array $query_vars = [];

		public function __construct( array $args = [] ) {
			$this->query_vars = $args;
		}
	}
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
	/**
	 * Request stand-in backed by a parameter array.
	 */
	class WP_REST_Request {
		public function __construct( private array $params = [] ) {
		}

		public function get_param( string $key ): mixed {
			return $this->params[ $key ] ?? null;
		}
	}
}

if ( ! class_exists( 'WP_REST_Response' ) ) {
	/**
	 * Response stand-in recording data and headers.
	 */
	class WP_REST_Response {
		/** @var array<string, string> */
		public array $headers = [];

		public function __construct( public mixed $data = null, public int $status = 200 ) {
		}

		public function header( string $key, string $value ): void {
			$this->headers[ $key ] = $value;
		}

		public function get_data(): mixed {
			return $this->data;
		}

		public function get_headers(): array {
			return $this->headers;
		}
	}
}
