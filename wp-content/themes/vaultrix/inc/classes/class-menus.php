<?php
/**
 * Register Menus.
 *
 * @package vaultrix
 */

namespace VAULTRIX\Inc;

use VAULTRIX\Inc\Traits\Singleton;

/**
 * Class Menus
 */
class Menus {
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
		add_action( 'init', array( $this, 'register_menus' ) );

		/**
		 * Add Mega Menu custom setting in Navigation Menu.
		 */
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'nav_menu_item_custom_fields' ), 10, 3 );

		/**
		 * Saves custom setting to postmeta for navigation.
		 */
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 2 );

		/**
		 * Display custom megament for navigation.
		 */
		add_filter( 'walker_nav_menu_start_el', array( $this, 'walker_nav_menu_start_el' ), 10, 4 );

		/**
		 * Display custom megamenu class for navigation.
		 */
		add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 2 );
	}

	/**
	 * Register Menus
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_menus() {
		register_nav_menus(
			array(
				'logged-in-menu' => esc_html__( 'Logged in menu', 'vaultrix' ),
			)
		);

		register_nav_menus(
			array(
				'logged-out-menu' => esc_html__( 'Logged out menu', 'vaultrix' ),
			)
		);

		register_nav_menus(
			array(
				'my-account-menu' => esc_html__( 'My account menu', 'vaultrix' ),
			)
		);

	}

		/**
		 * Add Mega Menu custom setting in Navigation Menu
		 *
		 * @param string  $item_id  Menu item ID as a numeric string.
		 * @param WP_Post $menu_item  Menu item data object.
		 * @param int     $depth  Depth of menu item. Used for padding.
		 * @return void
		 * @since 1.0.0
		 */
	public function nav_menu_item_custom_fields( $item_id, $menu_item, $depth ) {
		$megamenu_args  = array(
			'post_type'        => VAULTRIX_MEGAMENU_POST_TYPE,
			'post_status'      => 'publish',
			'posts_per_page'   => '-1',
			'suppress_filters' => false,
		);
		$megamenu_posts = get_posts( $megamenu_args );
		if ( 0 === $depth && ! is_wp_error( $megamenu_posts ) ) { // Check parent menu & megamenu data.
			$mega_menu_setting = get_post_meta( $item_id, '_vaultrix_mega_menu_setting', true );
			?>
			<p class="field-css-megamenu description description-wide">
				<label for="edit-menu-item-megamenu-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Mega Menu', 'vaultrix' ); ?><br />
					<select id="edit-menu-item-megamenu-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-megamenu" name="menu-item-megamenu[<?php echo esc_attr( $item_id ); ?>]">
						<option value=""><?php esc_html_e( 'Select', 'vaultrix' ); ?></option>
						<?php foreach ( $megamenu_posts as $megamenu_post ) { ?>
							<option value="<?php echo esc_attr( $megamenu_post->ID ); ?>" <?php selected( $megamenu_post->ID, $mega_menu_setting ); ?>><?php echo esc_html( $megamenu_post->post_title ); ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<?php
		}
	}
		/**
		 * Saves custom setting to postmeta for navigation.
		 *
		 * @param int $menu_id         ID of the updated menu.
		 * @param int $menu_item_db_id ID of the updated menu item.
		 * @return void
		 * @since 1.0.0
		 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id ) {
		$megamenu = filter_input( INPUT_POST, 'menu-item-megamenu', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		if ( ! isset( $megamenu[ $menu_item_db_id ] ) ) {
			$megamenu[ $menu_item_db_id ] = '';
		}
		update_post_meta( $menu_item_db_id, '_vaultrix_mega_menu_setting', $megamenu[ $menu_item_db_id ] );
	}

	/**
	 * Display custom megamenu for navigation and menu description.
	 *
	 * @param string   $item_output The menu item's starting HTML output.
	 * @param WP_Post  $item   Menu item data object.
	 * @param string   $depth The menu position.
	 * @param string[] $args The menu arguments.
	 * @return string $item_output The menu item's starting HTML output.
	 * @since 1.0.0
	 */
	public function walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
		$mega_menu_setting = get_post_meta( $item->ID, '_vaultrix_mega_menu_setting', true );
		if ( ! empty( $mega_menu_setting ) ) {
			$mega_menu_content = get_post_field( 'post_content', $mega_menu_setting );
			if ( ! empty( $mega_menu_content ) ) {
				$item_output .= '<div class="megamenu-wrap">';
				$item_output .= apply_filters( 'the_content', $mega_menu_content );
				$item_output .= '</div>';
			}
		}
		if ( ! empty( $item->description ) ) {
			$item_output = str_replace( $args->link_after . '</a>', $args->link_after . '</a><span class="menu-item-description">' . $item->description . '</span>', $item_output );
		}
		return $item_output;
	}

	/**
	 * Display custom megamenu for navigation.
	 *
	 * @param string[] $classes   Array of the CSS classes that are applied to the menu item's `<li>` element.
	 * @param WP_Post  $item The current menu item object.
	 * @return string[] $classes   Array of the CSS classes that are applied to the menu item's `<li>` element.
	 * @since 1.0.0
	 */
	public function nav_menu_css_class( $classes, $item ) {
		$mega_menu_setting = get_post_meta( $item->ID, '_vaultrix_mega_menu_setting', true );
		if ( ! empty( $mega_menu_setting ) ) {
			$classes[] = 'menu-item-has-megamenu';
		}
		return $classes;
	}


}
