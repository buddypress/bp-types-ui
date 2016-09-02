<?php
/**
 * @package   BuddyPressTypesUI
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 */

class BP_Member_Types_UI {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'bp-types-ui';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Register Member Type custom post type.
		add_action( 'init', array( $this, 'register_bp_member_types_cpt' ), 99 );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register Member Type custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_bp_member_types_cpt() {

		$labels = array(
			'name'                  => _x( 'Member Types', 'Post Type General Name', 'bp-types-ui' ),
			'singular_name'         => _x( 'Member Type', 'Post Type Singular Name', 'bp-types-ui' ),
			'parent_item_colon'     => __( 'Parent Type:', 'bp-types-ui' ),
			'all_items'             => __( 'Member Types', 'bp-types-ui' ),
			'add_new_item'          => __( 'Add New Type', 'bp-types-ui' ),
			'add_new'               => __( 'Add New', 'bp-types-ui' ),
			'new_item'              => __( 'New Type', 'bp-types-ui' ),
			'edit_item'             => __( 'Edit Type', 'bp-types-ui' ),
			'update_item'           => __( 'Update Type', 'bp-types-ui' ),
			'view_item'             => __( 'View Type', 'bp-types-ui' ),
			'search_items'          => __( 'Search Types', 'bp-types-ui' ),
			'not_found'             => __( 'Not found', 'bp-types-ui' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'bp-types-ui' ),
			'insert_into_item'      => __( 'Insert into item', 'bp-types-ui' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'bp-types-ui' ),
			'items_list'            => __( 'Types list', 'bp-types-ui' ),
			'items_list_navigation' => __( 'Types list navigation', 'bp-types-ui' ),
			'filter_items_list'     => __( 'Filter types list', 'bp-types-ui' ),
		);
		$args = array(
			'label'                 => __( 'Member Type', 'bp-types-ui' ),
			'description'           => __( 'Create generated member types.', 'bp-types-ui' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'users.php',
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			// 'capability_type'       => 'bp_type',
			// 'map_meta_cap'          => true,
		);
		register_post_type( 'bp_member_type', $args );
	}
}