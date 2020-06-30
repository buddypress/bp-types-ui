<?php
/**
 * BuddyPress taxonomy functions.
 *
 * Most BuddyPress taxonomy functions are wrappers for their WordPress counterparts.
 * Because BuddyPress can be activated in various ways in a network environment, we
 * must switch to the root blog before using the WP functions.
 *
 * @package BuddyPress
 * @subpackage Core
 * @since 2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers a meta key for BuddyPress types.
 *
 * @since 7.0.0
 *
 * @param string $type_tax The BuddyPress type taxonomy.
 * @param string $meta_key The meta key to register.
 * @param array  $args     Data used to describe the meta key when registered. See
 *                         {@see register_meta()} for a list of supported arguments.
 * @return bool True if the meta key was successfully registered, false if not.
 */
function bp_register_type_meta( $type_tax, $meta_key, array $args ) {
	$taxonomies = array(
		bp_get_member_type_tax_name() => 'members',
		'bp_group_type'               => 'groups',
	);

	if ( ! isset( $taxonomies[ $type_tax ] ) ) {
		return false;
	}

	if ( isset( $args['label'] ) ) {
		unset( $args['label'] );
	}

	// register_term_meta() was introduced in WP 4.9.8.
	if ( ! function_exists( 'register_term_meta' ) ) {
		$args['object_subtype'] = $type_tax;

		return register_meta( 'term', $meta_key, $args );
	}

	return register_term_meta( $type_tax, $meta_key, $args );
}

/**
 * Add a new taxonomy term to the database.
 *
 * @since 7.0.0
 *
 * @param string       $term     The term name to add or update.
 * @param string       $taxonomy The taxonomy to which to add the term.
 * @param array|string $args {
 *     Optional. Array or string of arguments for inserting a term.
 *     @type string $description The term description. Default empty string.
 *     @type string $slug        The term slug to use. Default empty string.
 *     @type array  $metas       The term metas to add. Default empty array.
 * }
 * @return array|WP_Error An array containing the `term_id` and `term_taxonomy_id`,
 *                        WP_Error otherwise.
 */
function bp_insert_term( $term, $taxonomy = '', $args = array() ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy.', 'buddypress' ) );
	}

	$site_id = bp_get_taxonomy_term_site_id( $taxonomy );

	$switched = false;
	if ( $site_id !== get_current_blog_id() ) {
		switch_to_blog( $site_id );
		bp_register_taxonomies();
		$switched = true;
	}

	$term_metas = array();
	if ( isset( $args['metas'] ) ) {
		$term_metas = (array) $args['metas'];
		unset( $args['metas'] );
	}

	$tt_id = wp_insert_term( $term, $taxonomy, $args );

	if ( is_wp_error( $tt_id ) ) {
		return $tt_id;
	}

	$term_id = reset( $tt_id );

	if ( $term_metas ) {
		foreach ( $term_metas as $meta_key => $meta_value ) {
			if ( ! registered_meta_key_exists( 'term', $meta_key, $taxonomy ) ) {
				continue;
			}

			update_term_meta( $term_id, $meta_key, $meta_value );
		}
	}

	if ( $switched ) {
		restore_current_blog();
	}

	/**
	 * Fires when taxonomy terms have been set on BuddyPress objects.
	 *
	 * @since 7.0.0
	 *
	 * @param array  $tt_ids    An array containing the `term_id` and `term_taxonomy_id`.
	 * @param string $taxonomy  Taxonomy name.
	 * @param array  $term_metas The term metadata.
	 */
	do_action( 'bp_insert_term', $tt_id, $taxonomy, $term_metas );

	return $tt_id;
}

/**
 * Get taxonomy terms from the database.
 *
 * @since 7.0.0
 *
 * @param array  $args {
 *     Array of arguments to query terms.
 *     @see `get_terms()` for full description of arguments in case of a member type.
 * }
 * @return array The list of terms matching arguments.
 */
function bp_get_terms( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'taxonomy'   => '',
			'number'     => '',
			'hide_empty' => false,
		)
	);

	if ( ! $args['taxonomy'] ) {
		return array();
	}

	$site_id = bp_get_taxonomy_term_site_id( $args['taxonomy'] );

	$switched = false;
	if ( $site_id !== get_current_blog_id() ) {
		switch_to_blog( $site_id );
		bp_register_taxonomies();
		$switched = true;
	}

	$terms = get_terms( $args );

	if ( $switched ) {
		restore_current_blog();
	}

	return $terms;
}

/**
 * Deletes a term.
 *
 * @since 7.0.0
 *
 * @param int     $term     Term ID. Required.
 * @param string  $taxonomy Taxonomy Name. Required.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function bp_delete_term( $term_id = 0, $taxonomy = '' ) {
	if ( ! $term_id || ! $taxonomy ) {
		return new WP_Error( 'missing_arguments', __( 'Sorry, the term ID and the taxonomy are required arguments.', 'buddypress' ) );
	}

	$site_id = bp_get_taxonomy_term_site_id( $taxonomy );

	$switched = false;
	if ( $site_id !== get_current_blog_id() ) {
		switch_to_blog( $site_id );
		bp_register_taxonomies();
		$switched = true;
	}

	$deleted = wp_delete_term( $term_id, $taxonomy );

	if ( $switched ) {
		restore_current_blog();
	}

	if ( is_wp_error( $deleted ) ) {
		return $deleted;
	}

	if ( false === $deleted ) {
		return new WP_Error( 'inexistant_term', __( 'Sorry, the term does not exist.', 'buddypress' ) );
	}

	if ( 0 === $deleted ) {
		return new WP_Error( 'default_term', __( 'Sorry, the default term cannot be deleted.', 'buddypress' ) );
	}

	return $deleted;
}
