<?php
/**
 * BP Types UI Functions.
 *
 * @package   bp-types-ui
 * @subpackage \includes\functions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the BuddyPress type taxonomy arguments.
 *
 * @since 1.1.0
 *
 * @param string $type The type name.
 * @return array The BuddyPress type taxonomy arguments.
 */
function bptui_get_bp_type_taxonomy_args( $type = '' ) {
	$taxonomy_args = array();
	$common_args   = array(
		'public'        => false,
		'show_in_rest'  => false,
		'query_var'     => false,
		'rewrite'       => false,
		'show_in_menu'  => false,
		'show_tagcloud' => false,
		'show_ui'       => bp_is_root_blog() && bp_current_user_can( 'bp_moderate' ),
	);

	$member_type_tax_name = bp_get_member_type_tax_name();
	$taxonomy_args[ $member_type_tax_name ] = array_merge(
		$common_args,
		array(
			'description' => _x( 'BuddyPress Member types', 'Member type taxonomy description', 'bp-types-ui' ),
			'labels'      => array(
				'name'                       => _x( 'Member types', 'Member type taxonomy name', 'bp-types-ui' ),
				'singular_name'              => _x( 'Member type', 'Member type taxonomy singular name', 'bp-types-ui' ),
				'search_items'               => _x( 'Search Member types', 'Member type taxonomy search items label', 'bp-types-ui' ),
				'popular_items'              => _x( 'Most used Member types', 'Member type taxonomy popular items label', 'bp-types-ui' ),
				'all_items'                  => _x( 'All Member types', 'Member type taxonomy all items label', 'bp-types-ui' ),
				'edit_item'                  => _x( 'Edit Member type', 'Member type taxonomy edit item label', 'bp-types-ui' ),
				'view_item'                  => _x( 'View Member type', 'Member type taxonomy view item label', 'bp-types-ui' ),
				'update_item'                => _x( 'Update Member type', 'Member type taxonomy update item label', 'bp-types-ui' ),
				'add_new_item'               => _x( 'Add new Member type', 'Member type taxonomy add new item label', 'bp-types-ui' ),
				'new_item_name'              => _x( 'New Member type name', 'Member type taxonomy new item name label', 'bp-types-ui' ),
				'separate_items_with_commas' => _x( 'Separate Member types with commas', 'Member type taxonomy separate items with commas label', 'bp-types-ui' ),
				'add_or_remove_items'        => _x( 'Add or remove Member types', 'Member type taxonomy add or remove items label', 'bp-types-ui' ),
				'choose_from_most_used'      => _x( 'Choose from the most used Member types', 'Member type taxonomy choose from most used label', 'bp-types-ui' ),
				'not_found'                  => _x( 'No Member types found', 'Member type taxonomy not found label', 'bp-types-ui' ),
				'no_terms'                   => _x( 'No Member types', 'Member type taxonomy no terms label', 'bp-types-ui' ),
				'items_list_navigation'      => _x( 'Member types list navigation', 'Member type taxonomy items list navigation label', 'bp-types-ui' ),
				'items_list'                 => _x( 'Member types list', 'Member type taxonomy items list label', 'bp-types-ui' ),
				'back_to_items'              => _x( 'Back to all Member types', 'Member type taxonomy back to items label', 'bp-types-ui' ),
			),
			/**
			 * 'show_in_rest' should be true.
			 * 'rest_base'    should be set.
			 * 'rest_controller_class' should be handle into the BP REST API.
			 */
		)
	);

	$taxonomy_args['bp_group_type'] = array_merge(
		$common_args,
		array(
			'description' => _x( 'BuddyPress Groyp types', 'Group type taxonomy description', 'bp-types-ui' ),
			'labels'      => array(
				'name'                       => _x( 'Group types', 'Group type taxonomy name', 'bp-types-ui' ),
				'singular_name'              => _x( 'Group type', 'Group type taxonomy singular name', 'bp-types-ui' ),
				'search_items'               => _x( 'Search Group types', 'Group type taxonomy search items label', 'bp-types-ui' ),
				'popular_items'              => _x( 'Most used Group types', 'Group type taxonomy popular items label', 'bp-types-ui' ),
				'all_items'                  => _x( 'All Group types', 'Group type taxonomy all items label', 'bp-types-ui' ),
				'edit_item'                  => _x( 'Edit Group type', 'Group type taxonomy edit item label', 'bp-types-ui' ),
				'view_item'                  => _x( 'View Group type', 'Group type taxonomy view item label', 'bp-types-ui' ),
				'update_item'                => _x( 'Update Group type', 'Group type taxonomy update item label', 'bp-types-ui' ),
				'add_new_item'               => _x( 'Add new Group type', 'Group type taxonomy add new item label', 'bp-types-ui' ),
				'new_item_name'              => _x( 'New Group type name', 'Group type taxonomy new item name label', 'bp-types-ui' ),
				'separate_items_with_commas' => _x( 'Separate Group types with commas', 'Group type taxonomy separate items with commas label', 'bp-types-ui' ),
				'add_or_remove_items'        => _x( 'Add or remove Group types', 'Group type taxonomy add or remove items label', 'bp-types-ui' ),
				'choose_from_most_used'      => _x( 'Choose from the most used Group types', 'Group type taxonomy choose from most used label', 'bp-types-ui' ),
				'not_found'                  => _x( 'No Group types found', 'Group type taxonomy not found label', 'bp-types-ui' ),
				'no_terms'                   => _x( 'No Group types', 'Group type taxonomy no terms label', 'bp-types-ui' ),
				'items_list_navigation'      => _x( 'Group types list navigation', 'Group type taxonomy items list navigation label', 'bp-types-ui' ),
				'items_list'                 => _x( 'Group types list', 'Group type taxonomy items list label', 'bp-types-ui' ),
				'back_to_items'              => _x( 'Back to all Group types', 'Group type taxonomy back to items label', 'bp-types-ui' ),
			)
			/**
			 * 'show_in_rest' should be true.
			 * 'rest_base'    should be set.
			 * 'rest_controller_class' should be handle into the BP REST API.
			 */
		)
	);

	if ( ! isset( $taxonomy_args[ $type ] ) ) {
		return array();
	}

	return $taxonomy_args[ $type ];
}

/**
 * Get the BuddyPress type meta arguments.
 *
 * @since 1.1.0
 *
 * @param string $type The type name.
 * @return array The BuddyPress type meta arguments.
 */
function bptui_get_bp_type_meta_args( $type = '' ) {
	$meta_args = array(
		'bp_type_name' => array(
			'label'             => __( 'Name', 'bp-types-ui' ),
			'description'       => __( 'The name of your type, at the plural form.', 'bp-types-ui' ),
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
		),
		'bp_type_singular_name' => array(
			'label'             => __( 'Singular name', 'bp-types-ui' ),
			'description'       => __( 'The name of your type, at the singular form.', 'bp-types-ui' ),
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
		),
		'bp_type_has_directory' => array(
			'label'             => __( 'Add Type-Filtered Directory View', 'bp-types-ui' ),
			'description'       => __( 'Add a list of members matching the member type available on the Members Directory page (e.g. site.url/members/type/teacher/).', 'bp-types-ui' ),
			'type'              => 'boolean',
			'single'            => true,
			'sanitize_callback' => 'absint',
		),
		'bp_type_directory_slug' => array(
			'label'             => __( 'Custom type directory slug', 'bp-types-ui' ),
			'description'       => __( 'If you want to use a slug that is different from the Member Type ID above, enter it here.', 'bp-types-ui' ),
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_title',
		),
	);

	if ( 'bp_group_type' === $type ) {
		$meta_args['bp_type_has_directory']['description'] = __( 'Add a list of groups matching the member type available on the Groups Directory page (e.g. site.url/groups/type/ninja/).', 'bp-types-ui' );
		$meta_args['bp_type_directory_slug']['description'] = __( 'If you want to use a slug that is different from the Group Type ID above, enter it here.', 'bp-types-ui' );

		$meta_args = array_merge(
			$meta_args,
			array(
				'bp_type_show_in_create_screen' => array(
					'label'             => __( 'Add to Available Types on Create Screen', 'bp-types-ui' ),
					'description'       => __( 'Include this group type during group creation and when a group administrator is on the group&rsquo;s &ldquo;Manage > Settings&rdquo; page.', 'bp-types-ui' ),
					'type'              => 'boolean',
					'single'            => true,
					'sanitize_callback' => 'absint',
				),
				'bp_type_show_in_list'          => array(
					'label'             => __( 'Include when Group Types are Listed for a Group', 'bp-types-ui' ),
					'description'       => __( 'Include this group type when group types are listed, like in the group header.', 'bp-types-ui' ),
					'type'              => 'boolean',
					'single'            => true,
					'sanitize_callback' => 'absint',
				),
			)
		);
	}

	return $meta_args;
}

/**
 * Reset BuddyPress type taxonomies to use new arguments.
 *
 * @since 1.1.0
 */
function bptui_reset_bp_type_taxonomies() {
	// Reset only once.
	remove_action( 'bp_register_taxonomies', 'bptui_reset_bp_type_taxonomies', 11 );

	$bp_type_taxonomies = array_intersect(
		get_taxonomies( array( 'public' => false ) ),
		array( bp_get_member_type_tax_name(), 'bp_group_type' )
	);

	$objects = array(
		bp_get_member_type_tax_name() => 'user',
		'bp_group_type'               => 'bp_group',
	);

	foreach ( $bp_type_taxonomies as $bp_type_taxonomy ) {
		unregister_taxonomy( $bp_type_taxonomy );
		register_taxonomy(
			$bp_type_taxonomy,
			$objects[ $bp_type_taxonomy ],
			bptui_get_bp_type_taxonomy_args( $bp_type_taxonomy )
		);

		foreach ( bptui_get_bp_type_meta_args( $bp_type_taxonomy ) as $meta_key => $meta_args ) {
			bp_register_type_meta( $bp_type_taxonomy, $meta_key, $meta_args );
		}
	}
}
add_action( 'bp_register_taxonomies', 'bptui_reset_bp_type_taxonomies', 11 );

/**
 * Adds a `code` property set to true for object type registered using code.
 *
 * @since 1.0.0
 *
 * @param array  $args {
 *     Array of arguments describing the object type.
 *     @see `bp_register_member_type()` for full description of arguments in case of a member type.
 *     @see `bp_groups_register_group_type()` for full description of arguments in case of a group type.
 * }
 * @return array Arguments describing the object type.
 */
function bptui_set_registered_by_code_property( $args = array() ) {
	$args['code']  = true;
	$args['db_id'] = 0;
	return $args;
}
add_filter( 'bp_after_register_member_type_parse_args', 'bptui_set_registered_by_code_property', 0, 1 );
add_filter( 'bp_after_register_group_type_parse_args', 'bptui_set_registered_by_code_property', 0, 1 );

/**
 * Gets the registered by code types only.
 *
 * @since 1.1.0
 *
 * @param string $taxonomy The taxonomy to get types from.
 * @return array The registered by code types.
 */
function bptui_get_types_registered_by_code( $taxonomy = '' ) {
	$callbacks = array(
		bp_get_member_type_tax_name() => 'bp_get_member_types',
		'bp_group_type'               => 'bp_groups_get_group_types',
	);

	if ( ! isset( $callbacks[ $taxonomy ] ) ) {
		return array();
	}

	return call_user_func_array(
		$callbacks[ $taxonomy ],
		array(
			'args'   => array(
				'code' => true
			),
			'output' => 'objects',
		)
	);
}

/**
 * Add a cache group for Database object types.
 *
 * @since 1.1.0
 */
function bptui_set_object_type_terms_cache_group() {
	wp_cache_add_global_groups( 'bp_object_terms' );
}
add_action( 'bp_setup_cache_groups', 'bptui_set_object_type_terms_cache_group' );

/**
 * Clear the Database object types cache.
 *
 * @since 2.2.0
 *
 * @param int $type_id The Type's term ID.
 * @param string $taxonomy The Type's taxonomy name.
 */
function bptui_clear_object_type_terms_cache( $type_id = 0, $taxonomy = '' ) {
	wp_cache_delete( $taxonomy, 'bp_object_terms' );
}
add_action( 'bp_type_inserted', 'bptui_clear_object_type_terms_cache' );
add_action( 'bp_type_updated', 'bptui_clear_object_type_terms_cache' );
add_action( 'bp_type_deleted', 'bptui_clear_object_type_terms_cache' );

/**
 * Transform saved terms for the taxonomy in corresponding types.
 *
 * @since 1.1.0
 *
 * @param string $taxonomy The taxonomy to transform terms in types for.
 * @param array  $types    The registered by code types.
 * @return array DB/code Merged types.
 */
function bptui_set_types_from_terms( $taxonomy = '', $types = array() ) {
	if ( ! $taxonomy ) {
		return $types;
	}

	$db_types = wp_cache_get( $taxonomy, 'bp_object_terms' );

	if ( ! $db_types ) {
		$terms = bp_get_terms(
			array(
				'taxonomy' => $taxonomy,
			)
		);

		if ( ! is_array( $terms ) ) {
			return $types;
		}

		$metas = array_keys( bptui_get_bp_type_meta_args( $taxonomy ) );

		foreach ( $terms as $term ) {
			$type_name                      = $term->name;
			$db_types[ $type_name ]         = new stdClass();
			$db_types[ $type_name ]->db_id  = $term->term_id;
			$db_types[ $type_name ]->labels = array();
			$db_types[ $type_name ]->name   = $type_name;


			foreach ( $metas as $meta_key ) {
				$type_key = str_replace( 'bp_type_', '', $meta_key );
				if ( in_array( $type_key, array( 'name', 'singular_name' ), true ) ) {
					$db_types[ $type_name ]->labels[ $type_key ] = get_term_meta( $term->term_id, $meta_key, true );
				} else {
					$db_types[ $type_name ]->{$type_key} = get_term_meta( $term->term_id, $meta_key, true );
				}
			}
		}

		wp_cache_set( $taxonomy, $db_types, 'bp_object_terms' );
	}


	if ( is_array( $db_types ) ) {
		foreach ( $db_types as $db_type_name => $db_type ) {
			// Override props of registered by code types if customized by the admun user.
			if ( isset( $types[ $db_type_name ] ) && isset( $types[ $db_type_name ]->code ) && $types[ $db_type_name ]->code ) {
				// Merge Labels.
				if ( $db_type->labels ) {
					foreach ( $db_type->labels as $key_label => $value_label ) {
						if ( '' !== $value_label ) {
							$types[ $db_type_name ]->labels[ $key_label ] = $value_label;
						}
					}
				}

				// Merge other properties.
				foreach ( get_object_vars( $types[ $db_type_name ] ) as $key_prop => $value_prop ) {
					if ( 'labels' === $key_prop || 'name' === $key_prop ) {
						continue;
					}

					if ( isset( $db_type->{$key_prop} ) && '' !== $db_type->{$key_prop} ) {
						$types[ $db_type_name  ]->{$key_prop} = $db_type->{$key_prop};
					}
				}

				unset( $db_types[ $db_type_name ] );
			}
		}
	}

	return array_merge( $types, (array) $db_types );
}

/**
 * Filters the `bp_get_member_types()` function to include member types added via DB insertion.
 *
 * @since 1.1.0
 *
 * @param array  $types     List of Member type objects, keyed by name.
 * @param array  $args      Array of key=>value arguments for filtering.
 * @param string $operator  'or' to match any of $args, 'and' to require all.
 * @return array The complete list of Member type objects, keyed by name.
 */
function bptui_get_all_member_types( $types = array(), $args = array(), $operator = 'and' ) {
	if ( isset( $args['code'] ) && true === $args['code'] ) {
		return $types;
	}

	$types = bptui_set_types_from_terms( bp_get_member_type_tax_name(), $types );

	// Filter the list if needed.
	if ( array_filter( $args ) ) {
		$types = wp_filter_object_list( $types, $args, $operator );
	}

	return $types;
}
add_filter( 'bp_get_member_types', 'bptui_get_all_member_types', 10, 3 );

/**
 * Filters the `bp_groups_get_group_types()` function to include group types added via DB insertion.
 *
 * @since 1.1.0
 *
 * @param array  $types     List of Group type objects, keyed by name.
 * @param array  $args      Array of key=>value arguments for filtering.
 * @param string $operator  'or' to match any of $args, 'and' to require all.
 * @return array The complete list of Group type objects, keyed by name.
 */
function bptui_get_all_group_types( $types = array(), $args = array(), $operator = 'and' ) {
	if ( isset( $args['code'] ) && true === $args['code'] ) {
		return $types;
	}

	$types = bptui_set_types_from_terms( 'bp_group_type', $types );

	// Filter the list if needed.
	if ( array_filter( $args ) ) {
		$types = wp_filter_object_list( $types, $args, $operator );
	}

	return $types;
}
add_filter( 'bp_groups_get_group_types', 'bptui_get_all_group_types', 10, 3 );
