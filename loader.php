<?php
/**
 * Plugin Name: BuddyPress Types UI
 * Plugin URI:  https://buddypress.org
 * Description: Adds an interface for adding member and group types.
 * Author:      The BuddyPress Community
 * Author URI:  https://buddypress.org
 * Version:     1.0.0
 * Text Domain: bp-types-ui
 * Domain Path: /bp-languages/
 * License:     GPLv2 or later (license.txt)
 */

/**
 * Load the main class
 *
 * @since    1.0.0
 */
function bptui_start() {
	$base_path = plugin_dir_path( __FILE__ );

	// Include base class.
	require_once( $base_path . 'includes/class-bp-types-ui.php' );
	$base_types_class = new BP_Types_UI();
	$base_types_class->add_action_hooks();

	// Include member types class.
	require_once( $base_path . 'includes/class-bp-member-types-ui.php' );
	$member_types_class = new BP_Member_Types_UI();
	$member_types_class->add_action_hooks();

	// Include group types class.
	if ( bp_is_active( 'groups' ) ) {
		require_once( $base_path . 'includes/class-bp-group-types-ui.php' );
		$group_types_class = new BP_Group_Types_UI();
		$group_types_class->add_action_hooks();
	}
}
add_action( 'bp_loaded', 'bptui_start' );
