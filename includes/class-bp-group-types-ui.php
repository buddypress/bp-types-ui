<?php
/**
 * @package   BuddyPressTypesUI
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 */

class BP_Group_Types_UI extends BP_Types_UI {

	/**
	 * Post type name.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $post_type = 'bp_group_type';

	/**
	 * ID of the meta box. Used for nonce generation, too.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $meta_box_id = 'bp-group-type-parameters';

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

		// Customize the post type input form.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Register saved group types.
		add_action( 'bp_groups_register_group_types', array( $this, 'register_group_types' ), 12 );

		parent::add_action_hooks();
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
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'rewrite'               => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			// 'capability_type'       => 'bp_type',
			// 'map_meta_cap'          => true,
		);
		register_post_type( $this->post_type, $args );
	}

	/**
	 * Add the Group Type management page to the BP Groups menu item.
	 *
	 * @since 1.0.0
	 */
	public function relocate_cpt_admin_screen() {
		add_submenu_page(
			'bp-groups',
			_x( 'Group Types', 'Group Type General Name', 'bp-types-ui' ),
			_x( 'Group Types', 'Group Type General Name', 'bp-types-ui' ),
			'manage_options',
			'edit.php?post_type=' . $this->post_type
		);
	}

	/**
	 * Add a meta box for the properties for this type.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->meta_box_id,
			_x( 'Group Type Properties', 'Title of Group Type post meta box.', 'bp-types-ui' ),
			array( $this, 'output_meta_box' ),
			$this->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Create markup for type properties meta box.
	 *
	 * @since    1.0.0
	 *
	 * @return   html
	 */
	public function output_meta_box( $post ) {
		// Fetch the saved properties.
		$meta = $this->get_type_meta( $post->ID );

		// Add a nonce field
		wp_nonce_field( 'edit_' . $this->post_type . '_' . $post->ID, $this->meta_box_id . '_nonce' );
		?>
		<h4><?php _e( 'Group Type ID', 'bp-types-ui' ); ?></h4>
		<p>
			<input type="text" id="type_id" name="type_id" value="<?php echo $meta['type_id']; ?>" />
			<span class="description"><?php _e( 'Enter a lower-case string without spaces or special characters (used internally to identify the group type).', 'bp-types-ui' ); ?></span>
		</p>

		<h4><?php _e( 'Singular Name', 'bp-types-ui' ); ?></h4>
		<p>
			<input type="text" name="singular_name" value="<?php echo $meta['singular_name']; ?>" />
			<span class="description"><?php _e( 'Enter a capitalized string (used as a label).', 'bp-types-ui' ); ?></span>
		</p>

		<fieldset>
			<legend><?php _e( 'Add Type-Filtered Directory View', 'bp-types-ui' ); ?></legend>
			<p>
				<input type="checkbox" id="has_directory" name="has_directory" class="prerequisite"<?php checked( $meta['has_directory'], 'on' ); ?>/> <label for="has_directory" class="selectit"><?php _e( 'Add a list of groups matching the group type available on the Groups Directory page (e.g. http://example.com/groups/type/ninja/).', 'bp-types-ui' ); ?></label><br />
			</p>
			<div class="contingent-container">
				<p>
					<label for="has_directory_slug"><strong><?php _e( 'Custom type directory slug', 'bp-types-ui' ); ?></strong></label><br />
					<input type="text" id="has_directory_slug" name="has_directory_slug"  class="contingent" value="<?php echo $meta['has_directory_slug']; ?>" />
					<span class="description"><?php _e( 'If you want to use a slug that is different from the Group Type ID above, enter it here.', 'bp-types-ui' ); ?></span>
				</p>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php _e( 'Add to Available Types on Create Screen', 'bp-types-ui' ); ?></legend>
			<p>
				<input type="checkbox" id="show_in_create_screen" name="show_in_create_screen" class="prerequisite"<?php checked( $meta['show_in_create_screen'], 'on' ); ?>/> <label for="show_in_create_screen" class="selectit"><?php _e( 'Include this group type during group creation and when a group administrator is on the group&rsquo;s &ldquo;Manage > Settings&rdquo; page.', 'bp-types-ui' ); ?></label><br />
			</p>
			<div class="contingent-container">
				<p>
					<input type="checkbox" id="create_screen_checked" name="create_screen_checked" class="contingent"<?php checked( $meta['create_screen_checked'], 'on' ); ?>/> <label for="create_screen_checked"><?php _e( 'Pre-select this group type on the group creation screen.', 'bp-types-ui' ); ?></label>
				</p>
			</div>
		</fieldset>

		<h4><?php _e( 'Include when Group Types are Listed for a Group', 'bp-types-ui' ); ?></h4>
		<p>
			<input type="checkbox" id="show_in_list" name="show_in_list"<?php checked( $meta['show_in_list'], 'on' ); ?>/> <label for="show_in_list" class="selectit"><?php _e( 'Include this group type when group types are listed, like in the group header. ', 'bp-types-ui' ); ?></label>
		</p>

		<h4><?php _e( 'Description', 'bp-types-ui' ) ?></h4>
		<?php
		// We're saving this as the post's content.
		// We have to decide what is allowable in the description. HTML tags? Images? Text only?
		?>
		<textarea class="wp-editor-area" autocomplete="off" name="content" id="content" aria-hidden="true"><?php echo $post->post_content; ?></textarea>
		<?php
	}

	/**
	 * Save the group type definition parameters.
	 *
	 * @since    1.0.0
	 *
	 * @return   bool True if all meta fields save successfully, false otherwise.
	 */
	public function save( $post_id ) {
		if ( get_post_type( $post_id ) != $this->post_type ) {
			return;
		}

		// Save meta.
		$meta_fields_to_save = array(
			'type_id',
			'singular_name',
			'has_directory',
			'has_directory_slug',
			'show_in_create_screen',
			'show_in_list',
			'create_screen_checked'
		);
		return $this->save_meta_fields( $post_id, $meta_fields_to_save );
	}

	/**
	 * Fetch the post meta that contains the type's properties and parse against defaults.
	 *
	 * @param int $post_id The ID of the type's post.
	 *
	 * @return array The type's parsed properties.
	 */
	public function get_type_meta( $post_id = 0 ) {
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}
		$saved_meta = array(
			'type_id'               => get_post_meta( $post_id, 'type_id', true ),
			'singular_name'         => get_post_meta( $post_id, 'singular_name', true ),
			'plural_name'           => get_the_title( $post_id ),
			'has_directory'         => get_post_meta( $post_id, 'has_directory', true ),
			'has_directory_slug'    => get_post_meta( $post_id, 'has_directory_slug', true ),
			'show_in_create_screen' => get_post_meta( $post_id, 'show_in_create_screen', true ),
			'show_in_list'          => get_post_meta( $post_id, 'show_in_list', true ),
			'create_screen_checked' => get_post_meta( $post_id, 'create_screen_checked', true ),
		);
		$meta = wp_parse_args( $saved_meta, array(
			'type_id'               => '',
			'singular_name'         => '',
			'plural_name'           => '',
			'has_directory'         => false,
			'has_directory_slug'    => '',
			'show_in_create_screen' => false,
			'show_in_list'          => null,
			'create_screen_checked' => false,
		) );
		return $meta;
	}

	/**
	 * Fetch the saved member types and register them.
	 *
	 * @since 1.0.0
	 */
	public function register_group_types() {
		$group_types = new WP_Query( array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
			'nopaging'    => true,
		) );

		if ( $group_types->have_posts() ) {
			while ( $group_types->have_posts() ) {
				$group_types->the_post();
				$meta = $this->get_type_meta();

				// Types added via code take precedence.
				if ( null !== bp_groups_get_group_type_object( $meta['type_id'] ) ) {
					continue;
				}

				// Should this type have a directory? Custom slug?
				if ( 'on' == $meta['has_directory'] ) {
					$has_directory = $meta['has_directory_slug'] ? $meta['has_directory_slug'] : true;
				}

				bp_groups_register_group_type( $meta['type_id'], array(
					'labels' => array(
						'name' => get_the_title(),
						'singular_name' => $meta['singular_name']
					),
					'has_directory'         => $has_directory,
					'show_in_list'          => ( 'on' == $meta['show_in_list'] ),
					'show_in_create_screen' => ( 'on' == $meta['show_in_create_screen'] ),
					'create_screen_checked' => ( 'on' == $meta['create_screen_checked'] ),
					'description'           => get_the_content(),
				) );
			}
		}
	}

}
