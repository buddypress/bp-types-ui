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
	// Adding member types
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bp-member-types-ui.php' );
	BP_Member_Types_UI::get_instance();

	// Adding group types
	if ( bp_is_active( 'groups' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-bp-group-types-ui.php' );
		BP_Group_Types_UI::get_instance();
	}
}
add_action( 'bp_loaded', 'bptui_start' );
