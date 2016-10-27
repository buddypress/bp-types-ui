<?php
/**
 * @package   BuddyPressTypesUI
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 */

class BP_Member_Types_UI extends BP_Types_UI {

	/**
	 * Post type name.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $post_type = 'bp_member_type';

	/**
	 * ID of the meta box. Used for nonce generation, too.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $meta_box_id = 'bp-member-type-parameters';

	/**
	 * Initialize the class.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Add actions and filters to WordPress/BuddyPress hooks.
	 *
	 * @since    1.0.0
	 */
	public function add_action_hooks() {
		// Register Member Type custom post type.
		add_action( 'init', array( $this, 'register_bp_member_types_cpt' ), 99 );

		// Customize the post type input form.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Change the placeholder text in the title input.
		add_filter( 'enter_title_here', array( $this, 'filter_title_placeholder' ), 10, 2 );

		// Save meta when posts are saved.
		add_action( 'save_post', array( $this, 'save' ) );

		// Register saved member types.
		add_action( 'bp_register_member_types', array( $this, 'register_member_types' ), 12 );

		// Add admin scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
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
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'users.php',
			'menu_position'         => 5,
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
	 * Add a meta box for the properties for this type.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->meta_box_id,
			_x( 'Member Type Properties', 'Title of Member Type post meta box.', 'bp-types-ui' ),
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
		<h4><?php _e( 'Member Type ID', 'bp-types-ui' ); ?></h4>
		<p>
			<input type="text" id="type_id" name="type_id" value="<?php echo $meta['type_id']; ?>" />
			<span class="description"><?php _e( 'Enter a lower-case string without spaces or special characters (used internally to identify the member type).', 'bp-types-ui' ); ?></span>
		</p>
		<h4><?php _e( 'Singular Name', 'bp-types-ui' ); ?></h4>
		<p>
			<input type="text" name="singular_name" value="<?php echo $meta['singular_name']; ?>" />
		</p>
		<fieldset>
			<legend><?php _e( 'Add Type-Filtered Directory View', 'bp-types-ui' ); ?></legend>
			<p>
				<input type="checkbox" id="has_directory" name="has_directory" class="prerequisite"<?php checked( $meta['has_directory'], 'on' ); ?>/> <label for="has_directory" class="selectit"><?php _e( 'Add a list of members matching the member type available on the Members Directory page (e.g. http://example.com/members/type/teacher/).', 'bp-types-ui' ); ?></label><br />
			</p>
			<div class="contingent-container">
				<p>
					<label for="has_directory_slug"><strong><?php _e( 'Custom type directory slug', 'bp-types-ui' ); ?></strong></label><br />
					<input type="text" id="has_directory_slug" name="has_directory_slug"  class="contingent" value="<?php echo $meta['has_directory_slug']; ?>" />
					<span class="description"><?php _e( 'If you want to use a slug that is different from the Member Type ID above, enter it here.', 'bp-types-ui' ); ?></span>
				</p>
			</div>
		</fieldset>
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
		);
		return $this->save_meta_fields( $post_id, $meta_fields_to_save );
	}

	/**
	 * Fetch the post meta that contains the type's properties and parse against defaults.
	 *
 	 * @since 1.0.0
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
		);
		$meta = wp_parse_args( $saved_meta, array(
			'type_id'               => '',
			'singular_name'         => '',
			'plural_name'           => '',
			'has_directory'         => false,
			'has_directory_slug'    => '',
		) );
		return $meta;
	}

	/**
	 * Fetch the saved member types and register them.
	 *
	 * @since 1.0.0
	 */
	public function register_member_types() {
		$member_types = new WP_Query( array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
			'nopaging'    => true,
		) );

		if ( $member_types->have_posts() ) {
			while ( $member_types->have_posts() ) {
				$member_types->the_post();
				$meta = $this->get_type_meta();

				// Types added via code take precedence.
				if ( null !== bp_get_member_type_object( $meta['type_id'] ) ) {
					continue;
				}

				// Should this type have a directory? Custom slug?
				if ( 'on' == $meta['has_directory'] ) {
					$has_directory = $meta['has_directory_slug'] ? $meta['has_directory_slug'] : true;
				}
				bp_register_member_type( $meta['type_id'], array(
					'labels' => array(
						'name'          => get_the_title(),
						'singular_name' => $meta['singular_name'],
					),
					'has_directory' => $has_directory
				) );
			}
		}
	}

}
