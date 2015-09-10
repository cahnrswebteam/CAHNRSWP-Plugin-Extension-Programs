jQuery(document).ready( function($) {

	// Accordion
	$('.program-list dl dd').hide();
	$('.program-list').on( 'click', 'dt', function() {
		$(this).next('dd').toggle().parents('dl').toggleClass('disclosed');
	})

	// Term filter
	$( '.browse-terms' ).on( 'click', 'a', function( event ) {

		event.preventDefault();

		var term = $(this),
				type = term.data( 'type' ),
				slug = term.data( 'slug' );

		if ( ! term.hasClass( 'active' ) ) {

			$( '.browse-terms li a' ).removeClass( 'active' );

			term.addClass( 'active' );

			$.ajax({
				url: programs.ajaxurl,
				type: 'post',
				data: {
					action: 'extension_programs_request',
					type: type,
					term: slug,
				},
				beforeSend: function() {
					term.text( 'Loading...' );
				},
				success: function( html ) {
					$( '.program-list' ).html( html );
					term.text( term.data( 'name' ) );
				}
			})

		} else {

			term.removeClass( 'active' );

			$.ajax({
				url: programs.ajaxurl,
				type: 'post',
				data: {
					action: 'extension_programs_request',
				},
				success: function( html ) {
					$( '.program-list' ).html( html );
				}
			})

		}

	})

});