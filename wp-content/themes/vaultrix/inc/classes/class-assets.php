<?php
/**
 * Enqueue theme assets
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Assets
 */
class Assets {
	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'get_header', array( $this, 'remove_wp_emoji' ) );
		add_action( 'get_header', array( $this, 'move_scripts_to_footer' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		/**
		 * The 'enqueue_block_assets' hook includes styles and scripts both in editor and frontend,
		 * except when is_admin() is used to include them conditionally
		 */
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_admin_assets' ), 8 );
		add_action( 'upload_mimes', array( $this, 'add_file_types_to_uploads' ) ); //phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.upload_mimes

		add_filter( 'script_loader_tag', array( $this, 'script_additional_attrs' ), 10, 2 );
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );
		add_action( 'wp_print_footer_scripts', array( $this, 'lazy_load_scripts' ) );

		add_action( 'template_redirect', array( $this, 'dp_redirect_page_to_specific_page' ) );
	}

	/**
	 * Load styles in footer.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_styles_in_footer() {
		wp_enqueue_style(
			'vaultrix-slick-css',
			VAULTRIX_LIBRARY_URI . '/css/slick.css',
			array(),
			filemtime( VAULTRIX_LIBRARY_DIR_PATH . '/css/slick.css' ),
			'all'
		);
	}


	/**
	 * Remove Emoji from the page.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_wp_emoji() {

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}

	/**
	 * Move render blocking JS to the footer.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function move_scripts_to_footer() {
		// Remove default jQuery registration through WordPress.
		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'jquery-migrate' );
		wp_dequeue_script( 'wp-embed' );
		wp_deregister_script( 'jquery' );
		wp_deregister_script( 'jquery-migrate' );
		wp_deregister_script( 'wp-embed' );

		wp_enqueue_script( 'jquery', '/wp-includes/js/jquery/jquery.min.js', array(), VAULTRIX_THEME_VERSION, true );
	}

	/**
	 * Load critical CSS.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function hook_critical_css() {

		$css_path = VAULTRIX_BUILD_PATH . '/inline.css';
		$css      = '';
		$version  = true;

		if ( is_readable( $css_path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme build file.
			$css = file_get_contents( $css_path );

			if ( false !== $css && '' !== $css ) {
				$version = filemtime( $css_path );
			} else {
				$css = '';
			}
		}

		if ( '' === $css ) {
			return;
		}

		wp_register_style( 'vaultrix-inline-css', false, array(), $version, true );
		wp_add_inline_style( 'vaultrix-inline-css', $css );
		wp_enqueue_style( 'vaultrix-inline-css' );
	}

	/**
	 * Register and Enqueue styles.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_styles() {
		$this->hook_critical_css();

		// Register styles.
		wp_register_style( 'main-css', VAULTRIX_BUILD_URI . '/main.css', array(), filemtime( VAULTRIX_BUILD_PATH . '/main.css' ), 'all' );

		// Enqueue Styles.
		wp_enqueue_style( 'main-css' );

	}

	/**
	 * Register and Enqueue Scripts.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_scripts() {
		// Register scripts.
		wp_register_script( 'main-js', VAULTRIX_BUILD_URI . '/main.js', array( 'jquery' ), filemtime( VAULTRIX_BUILD_PATH . '/main.js' ), true );

		// Enqueue Scripts.
		wp_enqueue_script( 'main-js' );

		$delete_account_confirmation_text = get_field( 'delete_account_confirmation_text', 'option' );
		wp_localize_script(
			'main-js',
			'siteConfig',
			array(
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'        => wp_create_nonce( 'loadmore_post_nonce' ),
				'deleteAccountText' => $delete_account_confirmation_text,
				'siteUrl'           => site_url(),
			)
		);

		wp_register_script(
			'vaultrix-jquery-validation',
			VAULTRIX_LIBRARY_URI . '/js/jquery-validate.min.js',
			array( 'jquery' ),
			filemtime( VAULTRIX_LIBRARY_DIR_PATH . '/js/jquery-validate.min.js' ),
			true
		);

		if ( is_page( 'contact' ) ) {
			wp_add_inline_script(
				'main-js',
				"setTimeout(function(){
				var script = document.createElement('SCRIPT');
				script.src = 'https://www.google.com/recaptcha/api.js?render=" . VAULTRIX_RECAPTCHA_V3_SECRET_KEY . "#asyncload';
				script.type = 'text/javascript';
				document.getElementsByTagName('footer')[0].appendChild(script);
			}, 2000)"
			);
		}

		if ( has_block( 'vaultrix/video' ) ) {
			wp_enqueue_script( 'video-js', 'https://players.brightcove.net/6415818262001/xnBItxlS2_default/index.min.js', array( 'jquery' ), '1.0.0', true );
		}
	}

	/**
	 * Enqueue editor scripts and styles.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets() {

		// Slick.
		wp_enqueue_script(
			'vaultrix-slick-js',
			VAULTRIX_LIBRARY_URI . '/js/slick.min.js',
			array( 'jquery' ),
			filemtime( VAULTRIX_LIBRARY_DIR_PATH . '/js/slick.min.js' ),
			true
		);

		wp_enqueue_style(
			'vaultrix-slick-css',
			VAULTRIX_LIBRARY_URI . '/css/slick.css',
			array(),
			filemtime( VAULTRIX_LIBRARY_DIR_PATH . '/css/slick.css' ),
			'all'
		);

		wp_enqueue_style(
			'vaultrix-fontawesome-theme-css',
			VAULTRIX_LIBRARY_URI . '/css/fontawesome-all.min.css',
			array(),
			'5.15.4',
			'all'
		);

		// Editor CSS.
		if ( is_admin() ) {
			wp_enqueue_style(
				'vaultrix-editor-css',
				VAULTRIX_BUILD_URI . '/editor.css',
				array(),
				filemtime( VAULTRIX_BUILD_PATH . '/editor.css' ),
				'all'
			);
		}

		// Change block Priority to head.
		$blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
		foreach ( $blocks as $block ) {
			if ( has_block( $block->name ) ) {
				wp_enqueue_style( $block->style );
			}
		}
	}

	/**
	 * Enqueue editor scripts and styles.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_block_admin_assets() {
		wp_enqueue_script(
			'vaultrix-editor-js',
			VAULTRIX_BUILD_URI . '/editor.js',
			array( 'jquery' ),
			filemtime( VAULTRIX_BUILD_PATH . '/editor.js' ),
			true
		);
	}

	/**
	 * Action Function to add SVG support in file uploads.
	 *
	 * @param array $file_types Supported file types.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function add_file_types_to_uploads( $file_types ) {
		if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
			$new_filetypes        = array();
			$new_filetypes['svg'] = 'image/svg+xml';
			$file_types           = array_merge( $file_types, $new_filetypes );
		}

		return $file_types;
	}

	/**
	 * Lazy load script code.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function lazy_load_scripts() {
		$timeout = '5';
		?>
		<script type="text/javascript" id="flying-scripts">const loadScriptsTimer = setTimeout(loadScripts,<?php echo esc_html( $timeout ); ?>* 1000
			)
			;const userInteractionEvents = ["mouseover", "keydown", "touchstart", "touchmove", "wheel"];
			userInteractionEvents.forEach(function (event) {
				window.addEventListener(event, triggerScriptLoader, {passive: !0})
			});

			function triggerScriptLoader() {
				loadScripts();
				clearTimeout(loadScriptsTimer);
				userInteractionEvents.forEach(function (event) {
					window.removeEventListener(event, triggerScriptLoader, {passive: !0})
				})
			}

			function loadScripts() {
				document.querySelectorAll("script[data-type='lazy']").forEach(function (elem) {
					elem.setAttribute("src", elem.getAttribute("data-src"))
				})
			}</script>
		<?php
	}


	/**
	 * Identify script and do the lazy load.
	 *
	 * @param string $tag Tags string.
	 * @param string $handle Handle name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function script_additional_attrs( $tag, $handle ) {
		if ( 'grs-ad' === $handle ) {
			return str_replace( ' src', ' data-type="lazy" data-src', $tag );
		}

		return $tag;
	}

	public function dp_redirect_page_to_specific_page() {
		$current_user_id = get_current_user_id();
		if ( is_user_logged_in() && pmpro_hasMembershipLevel( null, $current_user_id ) && is_front_page() ) {
			wp_redirect( '/home/', 301 );
			exit;
		}
	}

}

