jQuery(document).ready( function($) {

	var resized = function() {

		( $(window).width() > 693 ) ? desktop() : mobile();

	}

	// Desktop functionality.
	var desktop = function() {

		// Remove any small-screen description.
		$( '#ext-programs' ).find( '.ext-program-description' ).remove();

		// Add the target div for the large-screen description if there isn't one.
		if ( $( '#ext-program-preview' ).length == 0 ) {
  		$( '.ext-program-preview-wrapper' ).append( '<article id="ext-program-preview"></article>' );
		}

		// Show program description on hover.
		$( '#ext-programs a' ).hover( function() {

			var program = $(this);

			$( '#ext-program-preview' ).html( function() {
  			var title     = '<header class="article-title"><h4><a title="Go to the ' + program.html() + ' website" href="' + program.attr( 'href' ) + '">' + program.html() + ' <span class="dashicons dashicons-external"></span></a></h4></header>',
						descOpen  = '<div class="article-summary">',
						desc      = ( program.data( 'desc' ).length !== 0 ) ? '<p>' + program.data( 'desc' ) + '</p>' : '',
						descImage = ( program.data( 'img' ).length !== 0 ) ? '<img src="' + program.data( 'img' ) + '" />' : '',
						descClose = '</div>';
  			return title + descOpen + desc + descImage + descClose;
			});

		});

		// Make the description fixed.
		$(function() {

			var $preview   = $( '#ext-program-preview' ),
					offset     = $preview.offset().top,
					topPadding = $( '.cahnrs-header-group' ).position().top + $( '.cahnrs-header-group' ).outerHeight(true),
					stopper    = $( '.ext-preview-stopper' ).position().top;

			$(window).scroll( function() {

				var scrollPos = $(window).scrollTop() + topPadding,
						scrollMax = ( scrollPos - offset ) + $preview.height();

        if ( scrollPos > offset ) {
					if ( scrollMax < stopper ) {
						$preview.css( 'margin-top', scrollPos - offset )
					}
        } else {
					$preview.css( 'margin-top', 0 );
        }

			});

		});

	}

	// Mobile functionality.
	var mobile = function() {

		// Remove the large screen preview.
		//$( '#ext-program-preview' ).remove();

		// Toggle program descriptions.
		$( '#ext-programs' ).on( 'click', 'a', function(event) {

			event.preventDefault();

			// Remove any open description.
			$( '#ext-programs' ).find( '.ext-program-description' ).remove();

			var program = $(this),
					descOpen  = '<span class="ext-program-description">',
					progLink  = '<strong><a title="Go to the ' + program.html() + ' website" href="' + program.attr( 'href' ) + '">Visit site <span class="dashicons dashicons-external"></span></a></strong><br />',
					desc      = ( program.data( 'desc' ).length !== 0 ) ? program.data( 'desc' ) + '<br />' : '',
					descImage = ( program.data( 'img' ).length !== 0 ) ? '<img src="' + program.data( 'img' ) + '" />' : '',
					descClose = '</span>';

			// Display a description for the clicked program. 
			program.parent('li').append( descOpen + progLink + desc + descImage + descClose );

		});

	}

	resized();

	$(window).resize(resized);

});