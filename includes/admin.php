<?php
/**
 * BP Types UI Admin.
 *
 * @package   bp-types-ui
 * @subpackage \includes\admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues script and style.
 *
 * @since 1.1.0
 */
function bptui_admin_screen_types_enqueue_scripts() {
	wp_dequeue_script( 'admin-tags' );
	wp_dequeue_script( 'inline-edit-tax' );

	wp_enqueue_script(
		'bp-admin-types',
		plugin_dir_url( __FILE__ ) . 'js/bp-types-admin.js',
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'js/bp-types-admin.js' ),
		true
	);

	wp_add_inline_style(
		'common',
		'.form-field:not(.bp-types-form), .term-bp_type_directory_slug-wrap:not(.bp-set-directory-slug), .edit-tag-actions #delete-link { display: none; }'
	);
}

/**
 * Set the feedback messages for Types Admin action.
 *
 * @since 1.1.0
 *
 * @param array $messages The messages to be displayed.
 * @return array The messages to be displayed.
 */
function bptui_admin_types_updated_messages( $messages = array() ) {
	$member_type_tax_name = bp_get_member_type_tax_name();

	$messages[ $member_type_tax_name ] = array(
		0 => '',
		1 => __( 'Please define the Member Type ID field.', 'bp-types-ui' ),
		2 => __( 'Member type successfully added.', 'bp-types-ui' ),
		3 => __( 'Sorry, there was an error and the Member type wasn’t added.', 'bp-types-ui' ),
		// The following one needs to be != 5.
		4 => __( 'Member type successfully updated.', 'bp-types-ui' ),
		5 => __( 'Sorry, this Member type already exists.', 'bp-types-ui' ),
		6 => __( 'Sorry, the Member type was not deleted: it does not exist.', 'bp-types-ui' ),
		7 => __( 'Sorry, This Member type is registered using code, deactivate the plugin or remove the custom code before trying to delete it again.', 'bp-types-ui' ),
		8 => __( 'Sorry, there was an error while trying to delete this Member type.', 'bp-types-ui' ),
		9 => __( 'Member type successfully deleted.', 'bp-types-ui' ),
	);

	$messages['bp_group_type'] = array(
		0 => '',
		1 => __( 'Please define the Group Type ID field.', 'bp-types-ui' ),
		2 => __( 'Group type successfully added.', 'bp-types-ui' ),
		3 => __( 'Sorry, there was an error and the Group type wasn’t added.', 'bp-types-ui' ),
		// The following one needs to be != 5.
		4 => __( 'Group type successfully updated.', 'bp-types-ui' ),
		5 => __( 'Sorry, this Group type already exists.', 'bp-types-ui' ),
		6 => __( 'Sorry, the Group type was not deleted: it does not exist.', 'bp-types-ui' ),
		7 => __( 'Sorry, This Group type is registered using code, deactivate the plugin or remove the custom code before trying to delete it again.', 'bp-types-ui' ),
		8 => __( 'Sorry, there was an error while trying to delete this Group type.', 'bp-types-ui' ),
		9 => __( 'Group type successfully deleted.', 'bp-types-ui' ),
	);

	return $messages;
}
add_filter( 'term_updated_messages', 'bptui_admin_types_updated_messages', 1 );

/**
 * Override the Admin parent file to highlight the right menu.
 *
 * @since 1.1.0
 */
function bptui_admin_screen_types_head() {
	global $parent_file, $taxnow;

	if ( bp_get_member_type_tax_name() === $taxnow ) {
		$parent_file = 'users.php';
	}

	if ( 'bp_group_type' === $taxnow ) {
		$parent_file = 'bp-groups';
	}
}

/**
 * Filters the terms list table column headers to customize them for BuddyPress Types.
 *
 * @since 1.1.0
 *
 * @param array $column_headers The column header labels keyed by column ID.
 * @return array The column header labels keyed by column ID.
 */
function bptui_admin_list_table_column_headers( $column_headers = array() ) {
	if ( isset( $column_headers['name'] ) ) {
		$column_headers['name'] = __( 'Type ID', 'bp-types-ui' );
	}

	unset( $column_headers['cb'], $column_headers['description'], $column_headers['posts'] );

	$column_headers['plural_name'] = __( 'Name', 'bp-types-ui' );
	$column_headers['counts']      = _x( 'Count', 'Number/count of types', 'bp-types-ui' );

	return $column_headers;
}

/**
 * Sets the content for the Plural name & Counts columns.
 *
 * @since 1.1.0
 *
 * @param string $string      Blank string.
 * @param string $column_name Name of the column.
 * @param int    $type_id     The type's term ID.
 * @return string The Type Plural name.
 */
function bptui_admin_list_table_column_content( $column_content = '', $column_name = '', $type_id = 0 ) {
	if ( 'plural_name' !== $column_name && 'counts' !== $column_name || ! $type_id ) {
		return $column_content;
	}

	$screen = get_current_screen();
	if ( ! isset( $screen->taxonomy ) || ! $screen->taxonomy ) {
		return;
	}

	$type = bp_get_term_by( 'id', $type_id, $screen->taxonomy );

	// Set the Plural name column.
	if ( 'plural_name' === $column_name ) {
		$type_plural_name = get_term_meta( $type_id, 'bp_type_name', true );

		if ( ! $type_plural_name ) {
			if ( 'bp_group_type' === $screen->taxonomy ) {
				$registered_type = bp_groups_get_group_type_object( $type->name );
			} else {
				$registered_type = bp_get_member_type_object( $type->name );
			}

			if ( isset( $registered_type->labels['name'] ) && $registered_type->labels['name'] ) {
				$type_plural_name = $registered_type->labels['name'];
			}
		}

		echo esc_html( $type_plural_name );

		// Set the Totals column.
	} elseif ( 'counts' === $column_name ) {
		$count = number_format_i18n( $type->count );

		if ( 0 === (int) $type->count ) {
			return 0;
		}

		$args = array(
			str_replace( '_', '-', $screen->taxonomy ) => $type->slug,
		);

		$base_url = $screen->parent_file;
		if ( 'bp_group_type' === $screen->taxonomy ) {
			$base_url = add_query_arg( 'page', $screen->parent_file, 'admin.php' );
		}

		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( $args, bp_get_admin_url( $base_url ) ) ),
			esc_html( $count )
		);
	}
}

/**
 * Customize the Types Admin list table row actions.
 *
 * @since 1.1.0
 */
function bptui_admin_list_table_row_actions( $actions = array(), $type = null ) {
	if ( ! isset( $type->taxonomy ) || ! $type->taxonomy ) {
		return $actions;
	}

	// Get the "registered by code" types.
	$registered_by_code_types = bptui_get_types_registered_by_code( $type->taxonomy );

	// Types registered by code cannot be deleted as long as the custom registration code exists.
	if ( isset( $registered_by_code_types[ $type->name ] ) ) {
		unset( $actions['delete'] );
	}

	// Inline edits are disabled for all types.
	unset( $actions['inline hide-if-no-js'] );

	// Removes the post type query argument for the edit action.
	if ( isset( $actions['edit'] ) ) {
		$actions['edit'] = str_replace( '&#038;post_type=post', '', $actions['edit'] );
	}

	return $actions;
}

/**
 * Outputs the BuddyPress type meta fields.
 *
 * @since 1.1.0
 *
 * @param string $taxonomy The type taxonomy name.
 */
function bptui_admin_type_form_fields( $taxonomy = '', $type = null ) {
	$type_id_label = __( 'Member Type ID', 'bp-types-ui' );
	$type_id_desc  = __( 'Enter a lower-case string without spaces or special characters (used internally to identify the member type).', 'bp-types-ui' );
	if ( 'bp_group_type' === $taxonomy ) {
		$type_id_label = __( 'Group Type ID', 'bp-types-ui' );
		$type_id_desc  = __( 'Enter a lower-case string without spaces or special characters (used internally to identify the group type).', 'bp-types-ui' );
	}

	if ( isset( $type->name ) ) {
		printf(
			'<tr class="form-field bp-types-form form-required term-bp_type_id-wrap">
				<th scope="row"><label for="bp_type_id">%1$s</label></th>
				<td>
					<input name="bp_type_id" id="bp_type_id" type="text" value="%2$s" size="40" disabled="disabled">
				</td>
			</tr>',
			esc_html( $type_id_label ),
			esc_attr( $type->name ),
			esc_html( $type_id_desc )
		);
	} else {
		printf(
			'<div class="form-field bp-types-form form-required term-bp_type_id-wrap">
				<label for="bp_type_id">%1$s</label>
				<input name="bp_type_id" id="bp_type_id" type="text" value="" size="40" aria-required="true">
				<p>%2$s</p>
			</div>',
			esc_html( $type_id_label ),
			esc_html( $type_id_desc )
		);
	}

	foreach ( bptui_get_bp_type_meta_args( $taxonomy ) as $meta_key => $meta_args ) {
		$type_key = str_replace( 'bp_type_', '', $meta_key );

		if ( 'string' === $meta_args['type'] ) {
			if ( isset( $type->name ) ) {
				if ( in_array( $type_key, array( 'name', 'singular_name' ), true ) ) {
					$type_prop_value = $type->labels[ $type_key ];
				} else {
					$type_prop_value = $type->{$type_key};
				}

				printf(
					'<tr class="form-field bp-types-form form-required term-%1$s-wrap">
						<th scope="row"><label for="%1$s">%2$s</label></th>
						<td>
							<input name="%1$s" id="%1$s" type="text" value="%3$s" size="40" aria-required="true">
							<p class="description">%4$s</p>
						</td>
					</tr>',
					esc_attr( $meta_key ),
					esc_html( $meta_args['label'] ),
					esc_attr( $type_prop_value ),
					esc_html( $meta_args['description'] )
				);

			} else {
				printf(
					'<div class="form-field bp-types-form form-required term-%1$s-wrap">
						<label for="%1$s">%2$s</label>
						<input name="%1$s" id="%1$s" type="text" value="" size="40">
						<p>%3$s</p>
					</div>',
					esc_attr( $meta_key ),
					esc_html( $meta_args['label'] ),
					esc_html( $meta_args['description'] )
				);
			}
		} else {
			if ( isset( $type->name ) ) {
				$checked = '';
				if ( isset( $type->{$type_key} ) && true === (bool) $type->{$type_key} ) {
					$checked = ' checked="checked"';
				}

				printf(
					'<tr class="form-field bp-types-form term-%1$s-wrap">
						<th scope="row"><label for="%1$s">%2$s</label></th>
						<td>
							<input name="%1$s" id="%1$s" type="checkbox" value="1"%3$s> %4$s
							<p class="description">%5$s</p>
						</td>
					</tr>',
					esc_attr( $meta_key ),
					esc_html( $meta_args['label'] ),
					$checked,
					esc_html__( 'Yes', 'bp-types-ui' ),
					esc_html( $meta_args['description'] )
				);
			} else {
				printf(
					'<div class="form-field bp-types-form term-%1$s-wrap">
						<label for="%1$s">
							<input name="%1$s" id="%1$s" type="checkbox" value="1"> %2$s
						</label>
						<p>%3$s</p>
					</div>',
					esc_attr( $meta_key ),
					esc_html( $meta_args['label'] ),
					esc_html( $meta_args['description'] )
				);
			}
		}
	}
}

/**
 * Prepare the Type's edit form fields for output.
 *
 * @since 1.1.0
 *
 * @param WP_Term $term The WP Term object.
 * @param string  $taxonomy The name of the taxonomy.
 * @return string HTML Output.
 */
function bptui_admin_type_edit_form_fields( $term = null, $taxonomy = '' ) {
	if ( ! isset( $term->name ) || ! $taxonomy ) {
		return;
	}

	if ( 'bp_group_type' === $taxonomy ) {
		$type = null;
		if ( bp_is_active( 'groups') ) {
			$type = bp_groups_get_group_type_object( $term->name );
		}
	} else {
		$type = bp_get_member_type_object( $term->name );
	}

	return bptui_admin_type_form_fields( $taxonomy, $type );
}

/**
 * Insert the registrered by code types not yet added into the DB.
 *
 * @since 1.1.0
 *
 * @param string $taxonomoy The Type taxonomy name.
 */
function bptui_admin_type_insert_code_registered_types( $taxonomy = '' ) {
	if ( 'bp_group_type' === $taxonomy ) {
		$all_types = bp_groups_get_group_types( array(), 'objects' );
	} else {
		$all_types = bp_get_member_types( array(), 'objects' );
	}

	$unsaved_types = wp_filter_object_list( $all_types, array( 'db_id' => 0 ), 'and', 'name' );
	if ( $unsaved_types ) {
		foreach ( $unsaved_types as $type_name ) {
			bp_insert_term(
				$type_name,
				$taxonomy,
				array(
					'slug'  => $type_name,
				)
			);
		}
	}
}

/**
 * Handle BuddyPress types specific admin actions.
 *
 * @since 1.1.0
 */
function bptui_admin_screen_types_load() {
	$taxonomy             = '';
	$member_type_tax_name = bp_get_member_type_tax_name();
	$current_screen       = get_current_screen();

	if ( ! isset( $current_screen->taxonomy ) || ! $current_screen->taxonomy ) {
		return;
	}

	$taxonomy  = $current_screen->taxonomy;
	$screen_id = $current_screen->id;

	if ( $member_type_tax_name !== $taxonomy && 'bp_group_type' !== $taxonomy ) {
		return;
	}

	if ( isset( $_POST['action'] ) || isset( $_GET['action'] ) ) {
		if ( isset( $_GET['action'] ) ) {
			$action = wp_unslash( $_GET['action'] );
		} else {
			$action = wp_unslash( $_POST['action'] );
		}

		// Adding a new type into DB.
		if ( 'add-tag' === $action ) {
			check_admin_referer( 'add-tag', '_wpnonce_add-tag' );

			$referer      = wp_get_referer();
			$default_args = array(
				'taxonomy'                      => '',
				'bp_type_id'                    => '',
				'bp_type_name'                  => '',
				'bp_type_singular_name'         => '',
				'bp_type_has_directory'         => 0,
				'bp_type_directory_slug'        => '',
				'bp_type_show_in_create_screen' => 0,
				'bp_type_show_in_list'          => 0,
			);

			$add_type_arguments = wp_parse_args( array_map( 'wp_unslash', $_POST ), $default_args );

			if ( ! $add_type_arguments['bp_type_id'] || ! $add_type_arguments['taxonomy'] ) {
				$referer = add_query_arg(
					array(
						'message' => 1,
						'error'   => 1,
					),
					$referer
				);

				wp_safe_redirect( $referer );
				exit;
			}

			$type_id     = sanitize_title( $add_type_arguments['bp_type_id'] );
			$taxonomy    = sanitize_key( $add_type_arguments['taxonomy'] );
			$type_exists = false;

			if ( $member_type_tax_name === $taxonomy ) {
				$type_exists = ! is_null( bp_get_member_type_object( $type_id ) );
			} elseif ( 'bp_group_type' === $taxonomy && bp_is_active( 'groups' ) ) {
				$type_exists = ! is_null( bp_groups_get_group_type_object( $type_id ) );
			}

			if ( $type_exists ) {
				$referer = add_query_arg(
					array(
						'message' => 5,
						'error'   => 1,
					),
					$referer
				);

				wp_safe_redirect( $referer );
				exit;
			}

			unset( $default_args['bp_type_id'], $default_args['taxonomy'] );
			$metas = array_filter( array_intersect_key( $add_type_arguments, $default_args ) );

			$tt_id = bp_insert_term(
				$type_id,
				$taxonomy,
				array(
					'slug'  => $type_id,
					'metas' => $metas,
				)
			);

			$result = array( 'message' => 2 );
			if ( is_wp_error( $tt_id ) ) {
				$result = array(
					'message' => 3,
					'error'   => 1,
				);
			}

			/**
			 * Hook here to add code once the type has been inserted.
			 *
			 * @since 1.1.0
			 *
			 * @param integer $type_id The Type's term_ID.
			 * @param string $taxonomy The Type's taxonomy name.
			 */
			do_action( 'bp_type_inserted', $type_id, $taxonomy );

			wp_safe_redirect( add_query_arg( $result, $referer ) );
			exit;

			// Updating an existing type intot the DB.
		} elseif ( 'editedtag' === $action ) {
			$type_id  = 0;
			$taxonomy = '';

			if ( isset( $_POST['tag_ID'] ) ) {
				$type_id = (int) wp_unslash( $_POST['tag_ID'] );
			}

			if ( isset( $_POST['taxonomy'] ) ) {
				$taxonomy = wp_unslash( $_POST['taxonomy'] );
			}

			check_admin_referer( 'update-tag_' . $type_id );

			$referer      = wp_get_referer();
			$default_args = array(
				'bp_type_name'                  => '',
				'bp_type_singular_name'         => '',
				'bp_type_has_directory'         => 0,
				'bp_type_directory_slug'        => '',
			);

			if ( 'bp_group_type' === $taxonomy ) {
				$default_args = array_merge(
					$default_args,
					array(
						'bp_type_show_in_create_screen' => 0,
						'bp_type_show_in_list'          => 0,
					)
				);
			}

			$update_meta_arguments = wp_parse_args( array_map( 'wp_unslash', $_POST ), $default_args );
			$all_meta_keys         = array_fill_keys( array_keys( $default_args ), true );
			$update_meta_arguments = array_intersect_key( $update_meta_arguments, $all_meta_keys );

			foreach ( $update_meta_arguments as $meta_key => $meta_value ) {
				if ( '' === $meta_value ) {
					delete_term_meta( $type_id, $meta_key );
				} else {
					update_term_meta( $type_id, $meta_key, $meta_value );
				}
			}

			/**
			 * Hook here to add code once the type has been updated.
			 *
			 * @since 1.1.0
			 *
			 * @param integer $type_id The Type's term_ID.
			 * @param string $taxonomy The Type's taxonomy name.
			 */
			do_action( 'bp_type_updated', $type_id, $taxonomy );

			wp_safe_redirect( add_query_arg( 'message', 4, $referer ) );
			exit;

			// Deletes a type.
		} elseif ( 'delete' === $action ) {
			$type_id  = 0;
			$taxonomy = '';

			if ( isset( $_GET['tag_ID'] ) ) {
				$type_id = (int) wp_unslash( $_GET['tag_ID'] );
			}

			if ( isset( $_GET['taxonomy'] ) ) {
				$taxonomy = wp_unslash( $_GET['taxonomy'] );
			}

			check_admin_referer( 'delete-tag_' . $type_id );

			$type    = bp_get_term_by( 'id', $type_id, $taxonomy );
			$referer = wp_get_referer();

			if ( ! $type ) {
				$referer = add_query_arg(
					array(
						'message' => 6,
						'error'   => 1,
					),
					$referer
				);

				wp_safe_redirect( $referer );
				exit;
			}

			// Get the "registered by code" types.
			$registered_by_code_types = bptui_get_types_registered_by_code( $taxonomy );
			if ( isset( $registered_by_code_types[ $type->name ] ) ) {
				$referer = add_query_arg(
					array(
						'message' => 7,
						'error'   => 1,
					),
					$referer
				);

				wp_safe_redirect( $referer );
				exit;
			}

			$deleted = bp_delete_term( $type->term_id, $taxonomy );

			if ( true !== $deleted ) {
				$referer = add_query_arg(
					array(
						'message' => 8,
						'error'   => 1,
					),
					$referer
				);

				wp_safe_redirect( $referer );
				exit;
			}

			/**
			 * Hook here to add code once the type has been deleted.
			 *
			 * @since 1.1.0
			 *
			 * @param integer $type_id The Type's term_ID.
			 * @param string $taxonomy The Type's taxonomy name.
			 */
			do_action( 'bp_type_deleted', $type_id, $taxonomy );

			wp_safe_redirect( add_query_arg( 'message', 9, $referer ) );
			exit;
		}
	}

	// Customize the WP Terms UI.
	add_action( 'admin_enqueue_scripts', 'bptui_admin_screen_types_enqueue_scripts' );
	add_action( 'admin_head-edit-tags.php', 'bptui_admin_screen_types_head' );
	add_action( 'admin_head-term.php', 'bptui_admin_screen_types_head' );
	add_action( "{$taxonomy}_add_form_fields", 'bptui_admin_type_form_fields', 10, 1 );
	add_action( "{$taxonomy}_edit_form_fields", 'bptui_admin_type_edit_form_fields', 10, 2 );
	add_action( "{$current_screen->taxonomy}_add_form", 'bptui_admin_type_insert_code_registered_types', 10, 1 );
	add_filter( "manage_{$screen_id}_columns", 'bptui_admin_list_table_column_headers', 10, 1 );
	add_filter( "manage_{$taxonomy}_custom_column", 'bptui_admin_list_table_column_content', 10, 3 );
	add_filter( "{$taxonomy}_row_actions", 'bptui_admin_list_table_row_actions', 10, 2 );
	add_filter( "bulk_actions-{$screen_id}", '__return_empty_array', 10, 1 );
}
add_action( 'load-edit-tags.php', 'bptui_admin_screen_types_load' );

/**
 * Create Admin submenus for BuddyPress types.
 *
 * @todo There's no edit-tags.php file into the Network administration.
 * We'll need to think about a place to fallback to eg: a BuddyPress
 * "objects" admin menu in root blog's admin?
 *
 * @since 1.1.0
 */
function bptui_admin_menu() {
	if ( ! bp_is_root_blog() || is_network_admin() ) {
		return;
	}

	add_submenu_page(
		'users.php',
		__( 'Member types', 'bp-types-ui' ),
		__( 'Member types', 'bp-types-ui' ),
		'bp_moderate',
		basename( add_query_arg( 'taxonomy', bp_get_member_type_tax_name(), bp_get_admin_url( 'edit-tags.php' ) ) )
	);

	if ( bp_is_active( 'groups' ) ) {
		add_submenu_page(
			'bp-groups',
			__( 'Group types', 'bp-types-ui' ),
			__( 'Group types', 'bp-types-ui' ),
			'bp_moderate',
			basename( add_query_arg( 'taxonomy', 'bp_group_type', bp_get_admin_url( 'edit-tags.php' ) ) )
		);
	}
}
add_action( 'bp_admin_menu', 'bptui_admin_menu' );
