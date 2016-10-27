( function( $ ) {
	$( document ).ready( function() {
		// Hide contingent options if not needed
		refresh_contingent_input_visibility();
		// Change visibility as needed.
		$( ".prerequisite" ).on( "change", function() {
			refresh_contingent_input_visibility();
		} );

		$( "#type_id" ).on( "blur", function() {
			check_type_id_is_unique();
		} );

	} );

	/**
	 * Show or hide extra questions that are contingent on other responses.
	 */
	function refresh_contingent_input_visibility() {
		$( ".prerequisite" ).each( function() {
			if ( $( this ).is(':checked') ) {
				$( this ).parents( "fieldset" ).find( ".contingent-container" ).show();
				$( this ).parents( "fieldset" ).find( ".contingent" ).prop( "disabled", false );
			} else {
				$( this ).parents( "fieldset" ).find( ".contingent-container" ).hide();
				$( this ).parents( "fieldset" ).find( ".contingent" ).prop( "disabled", true );
			}
		});
	}

	/**
	 * Give user feedback about overlapping types.
	 */
	function check_type_id_is_unique() {
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'check-bp-type-id',
				'pagenow': pagenow,
				'type': $( "#type_id" ).val(),
				'singular_name': $( "#singular_name" ).val(),
			},
			success: function (response) {
				console.log( response );
			},
			error: function (response) {
				console.log( response );
			}
		});
	}
} )( jQuery )
