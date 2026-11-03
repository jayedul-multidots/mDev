<?php
/**
 * Server render for the post gallery block.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\PostGallery;

use Closure;

/**
 * Renders `mdev-gb/post-gallery`: the first page of results plus the
 * Interactivity API context the browser store continues from.
 *
 * The output works without JavaScript: filters are a GET form and "Load more"
 * is a link, both driven by UrlParams. With JavaScript the store intercepts
 * them and loads results over REST instead.
 *
 * @since 0.2.0
 */
final class GalleryRenderer {

	public const BLOCK_NAME = 'mdev-gb/post-gallery';
	public const STORE      = 'mdev-gb/post-gallery';

	/**
	 * Galleries rendered so far in this request (drives the URL prefix).
	 *
	 * @var int
	 */
	private int $instance = 0;

	/**
	 * Returns the current request's query parameters (unslashed).
	 *
	 * @var Closure(): array<string, mixed>
	 */
	private Closure $query_params;

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param GalleryRepository $repository   Cached queries.
	 * @param ResultSummary     $summary      Summary text.
	 * @param string            $template     Absolute path to the view template.
	 * @param Closure           $query_params Current query parameters provider.
	 *
	 * @phpstan-param Closure(): array<string, mixed> $query_params
	 */
	public function __construct(
		private readonly GalleryRepository $repository,
		private readonly ResultSummary $summary,
		private readonly string $template,
		Closure $query_params,
	) {
		$this->query_params = $query_params;
	}

	/**
	 * Attach the render callback when the block is registered from block.json.
	 * Hooked to `register_block_type_args`.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $args       Block type arguments.
	 * @param string               $block_name Block name.
	 * @return array<string, mixed>
	 */
	public function filter_block_type_args( array $args, string $block_name ): array {
		if ( self::BLOCK_NAME === $block_name ) {
			$args['render_callback'] = [ $this, 'render' ];
		}

		return $args;
	}

	/**
	 * Render callback.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	public function render( array $attributes ): string {
		++$this->instance;

		$prefix   = UrlParams::prefix( $this->instance );
		$params   = ( $this->query_params )();
		$settings = GallerySettings::from_attributes( $attributes );
		$query    = GalleryQuery::from_input( $settings, UrlParams::read( $params, $prefix ) );
		$result   = $this->repository->find( $query );

		$view = [
			'id'       => 'mdev-pg-' . $this->instance,
			'prefix'   => $prefix,
			'settings' => $settings,
			'query'    => $query,
			'context'  => $this->context( $query, $result, $prefix, $params ),
		];

		ob_start();
		$this->include_template( $view );
		return (string) ob_get_clean();
	}

	/**
	 * Interactivity API context: everything the browser store needs to
	 * continue from this server render. Mirrors the REST response shape.
	 *
	 * @since 0.2.0
	 *
	 * @param GalleryQuery         $query  Query.
	 * @param GalleryResult        $result First result page.
	 * @param string               $prefix URL prefix.
	 * @param array<string, mixed> $params Current query parameters.
	 * @return array<string, mixed>
	 */
	public function context( GalleryQuery $query, GalleryResult $result, string $prefix, array $params ): array {
		return [
			'restUrl'      => esc_url_raw( rest_url( RestController::NAMESPACE . RestController::ROUTE ) ),
			'prefix'       => $prefix,
			'settings'     => $query->settings->to_request_params(),
			'defaultSort'  => $query->settings->default_sort->value,
			'search'       => $query->search,
			'sort'         => $query->sort->value,
			'loadedSearch' => $query->search,
			'loadedSort'   => $query->sort->value,
			'retry'        => null,
			'page'         => $result->page,
			'items'        => $result->items,
			'total'        => $result->total,
			'totalPages'   => $result->total_pages,
			'hasMore'      => $result->has_more(),
			'nextUrl'      => $result->has_more() ? UrlParams::build( $params, $prefix, $query->with_page( $result->page + 1 ) ) : '',
			'summary'      => $this->summary->describe( $result, $query->search ),
			'isLoading'    => false,
			'error'        => '',
			'errorMessage' => __( 'Could not load posts. Please try again.', 'mdev-gb' ),
		];
	}

	/**
	 * Include the template with an isolated scope.
	 *
	 * @param array<string, mixed> $view Template variables.
	 * @return void
	 */
	private function include_template( array $view ): void {
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- $view is read by the included template.
		( static function ( string $mdev_gb_template, array $view ): void {
			include $mdev_gb_template;
		} )( $this->template, $view );
	}
}
