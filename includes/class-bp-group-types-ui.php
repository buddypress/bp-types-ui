<?php
/**
 * @package   BuddyPressTypesUI
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 */

class BP_Group_Types_UI extends BP_Types_UI {

	/**
	 * Initialize the class.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Add actions and filters to WordPress/BuddyPress hooks.
	 *
	 * @since    1.0.0
	 *
	 * @return    void.
	 */
	public function add_action_hooks() {
		// Register Group Type custom post type.
		add_action( 'init', array( $this, 'register_bp_group_types_cpt' ) );

		// Add the Group Type management page to the BP Groups menu item.
		add_action( bp_core_admin_hook(), array( $this, 'relocate_cpt_admin_screen' ), 99 );
	}

	/**
	 * Register Group Type custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_bp_group_types_cpt() {

		$labels = array(
			'name'                  => _x( 'Group Types', 'Group Type General Name', 'bp-types-ui' ),
			'singular_name'         => _x( 'Group Type', 'Group Type Singular Name', 'bp-types-ui' ),
			'parent_item_colon'     => __( 'Parent Type:', 'bp-types-ui' ),
			'all_items'             => __( 'Group Types', 'bp-types-ui' ),
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
			'label'                 => __( 'Group Type', 'bp-types-ui' ),
			'description'           => __( 'Create generated group types.', 'bp-types-ui' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_in_menu'          => false,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			// 'capability_type'       => 'bp_type',
			// 'map_meta_cap'          => true,
		);
		register_post_type( 'bp_group_type', $args );
	}

	/**
	 * Add the Group Type management page to the BP Groups menu item.
	 *
	 * @since 1.0.0
	 */
	function relocate_cpt_admin_screen() {
		add_submenu_page( 'bp-groups', _x( 'Group Types', 'Group Type General Name', 'bp-types-ui' ), _x( 'Group Types', 'Group Type General Name', 'bp-types-ui' ), 'manage_options', 'edit.php?post_type=bp_group_type' );
	}

}
