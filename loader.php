<?php
/**
 * Plugin Name: BuddyPress Types UI
 * Plugin URI:  https://buddypress.org
 * Description: Adds an interface for adding member and group types.
 * Author:      The BuddyPress Community
 * Author URI:  https://buddypress.org
 * Version:     1.1.0
 * Text Domain: bp-types-ui
 * Domain Path: /bp-languages/
 * License:     GPLv2 or later (license.txt)
 */

/**
 * Include files.
 *
 * @since 1.0.0
 * @since 1.1.0 Edit description and try terms UI.
 */
function bptui_start() {
	$base_path = plugin_dir_path( __FILE__ );

	// Include files.
	require_once $base_path . 'includes/bp-core-taxonomy.php';
	require_once $base_path . 'includes/functions.php';

	if ( is_admin() ) {
		require_once $base_path . 'includes/admin.php';
	}
}
add_action( 'bp_loaded', 'bptui_start' );
