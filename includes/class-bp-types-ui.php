<?php
/**
 * @package   BuddyPressTypesUI
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 */

class BP_Types_UI {

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
	 * Post type name.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $post_type;

	/**
	 * ID of the meta box. Used for nonce generation, too.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $meta_box_id;

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
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Listen for AJAX Type ID checks.
		add_action( 'wp_ajax_check-bp-type-id', array( $this, 'ajax_check_type_id' ) );
	}

	/**
	 * Return the plugin's version.
	 *
	 * @since    1.0.0
	 *
	 * @return string Plugin version.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return string Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Listen for AJAX Type ID checks.
	 *
	 * @since    1.0.0
	 */
	public function ajax_check_type_id() {
		if ( ! isset( $_POST['pagenow'] ) ) {
			return;
		}

		// We want to check the registered types right before ours are added.
		//@TODO: This isn't working. Maybe the ajax action hooks in after bp_register_member_types?
		if ( 'bp_member_type' == $_POST['pagenow'] ) {
			add_action( 'bp_register_member_types', array( $this, 'ajax_check_type_id_send_response' ), 11 );
		} elseif( 'bp_group_type' == $_POST['pagenow'] ) {
			add_action( 'bp_groups_register_group_types', array( $this, 'ajax_check_type_id_send_response' ), 11 );
		}
	}

	/**
	 * Check whether a type ID has been registered via code.
	 *
	 * @since    1.0.0
	 */
	public function ajax_check_type_id_send_response() {
		//@TODO: This isn't working. Maybe the ajax action hooks in after bp_register_member_types?
		if ( ! isset( $_POST['type'] ) ) {
			if ( ! isset( $_POST['singular_name'] ) ) {
				wp_send_json_error( __( 'Unknown type' , 'bp-types-ui' ) );
			} else {
				$type = sanitize_title( $_POST['singular_name'] );
			}
		} else {
			$type = sanitize_title( $_POST['type'] );
		}

		if ( 'bp_member_type' == $_POST['pagenow'] ) {
			if ( null == bp_get_member_type_object( $type ) ) {
				wp_send_json_success( __( 'Type ID is unique.' , 'bp-types-ui' ) );
			}
		} elseif( 'bp_group_type' == $_POST['pagenow'] ) {
			if ( null == bp_groups_get_group_type_object( $type ) ) {
				wp_send_json_success( __( 'Type ID is unique.' , 'bp-types-ui' ) );
			}
		}

		wp_send_json_error( __( 'Type ID is already in use.' , 'bp-types-ui' ) );
	}

	/**
	 * Load admin scripts and styles.
	 *
	 * @since    1.0.0
	 */
	function enqueue_admin_scripts_styles( $hook_suffix ) {
		// Only load the scripts and styles when on our pages.
		$screen = get_current_screen();
		if ( ! isset( $screen->post_type ) || $this->post_type != $screen->post_type ) {
			return;
		}
		wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/bp-types-admin.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/bp-types-admin.css', __FILE__ ), array(), $this->version, 'all' );
	}

	/**
	 * Change the default placeholder text in the title input.
	 *
	 * @since    1.0.0
	 *
	 * @param string  $text Placeholder text. Default 'Enter title here'.
	 * @param WP_Post $post Post object.
	 *
	 * @return string  $text Placeholder text.
	 */
	function filter_title_placeholder( $placeholder, $post ){
		if ( $this->post_type == $post->post_type ) {
			$placeholder = _x( 'Enter plural name for type here', 'BuddyPress group and member type edit screen title input placeholder text', 'bp-types-ui' );
		}
		return $placeholder;
	}

	/**
	 * Determines whether or not the current user has the ability to save meta data associated with this post.
	 *
	 * @param  int    $post_id      The ID of the post being saved.
	 * @param  string $nonce_name   The name of the passed nonce.
	 * @param  string $nonce_action The action of the passed nonce.
	 *
	 * @return bool Whether or not the user has the ability to save this post.
	 */
	public function user_can_save( $post_id, $nonce_name, $nonce_action ) {

		// Don't save if the user hasn't submitted the changes.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Verify that the input is well nonced.
		if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_action ) ) {
			return false;
		}

		// Make sure the user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * General handler for saving post meta.
	 *
	 * @since   1.0.0
	 *
	 * @param 	int $post_id
	 * @param 	array meta_key names to save
	 *
	 * @return  bool
	 */
	function save_meta_fields( $post_id, $fields = array() ) {
		$successes = 0;

		// Check that this user is allowed to take this action.
		$nonce_name   = $this->meta_box_id . '_nonce';
		$nonce_action = 'edit_' . $this->post_type . '_' . $post_id;
		if ( ! $this->user_can_save( $post_id, $nonce_name, $nonce_action ) ) {
			return false;
		}

		// Generate fallbacks for needed information if left blank.
		if ( empty( $_POST['post_title'] ) ) {
			// @TODO: This won't set the title. Should we enforce titles?
			$_POST['post_title'] = $this->post_type . '-' . $post_id;
		}
		if ( empty( $_POST['singular_name'] ) ) {
			$_POST['singular_name'] = $_POST['post_title'];
		}
		if ( empty( $_POST['type_id'] ) ) {
			$_POST['type_id'] = $_POST['singular_name'];
		}
		// Sanitize the type_id and custom directory slug.
		$_POST['type_id'] = sanitize_title( $_POST['type_id'] );
		if ( $_POST['has_directory_slug'] ) {
			$_POST['has_directory_slug'] = sanitize_title( $_POST['has_directory_slug'] );
		}

		foreach ( $fields as $field ) {
			$old_setting = get_post_meta( $post_id, $field, true );
			$new_setting = ( isset( $_POST[$field] ) ) ? $_POST[$field] : '';
			$success = false;

			if ( empty( $new_setting ) && ! empty( $old_setting ) ) {
				$success = delete_post_meta( $post_id, $field );
			} elseif ( $new_setting == $old_setting ) {
				/*
				 * No need to resave settings if they're the same.
				 * Also, update_post_meta returns false in this case,
				 * which is confusing.
				 */
				$success = true;
			} else {
				$success = update_post_meta( $post_id, $field, $new_setting );
			}

			if ( $success ) {
				$successes++;
			}
		}

		if ( $successes == count( $fields ) ) {
			return true;
		} else {
			return false;
		}
	}

}
