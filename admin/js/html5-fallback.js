( function( $ ) {
	$( function() {
		var supportHtml5 = ( function() {
			var features = {};
			var input = document.createElement( 'input' );
			var inputTypes = [ 'date', 'time' ];
			$.each( inputTypes, function( index, value ) {
				input.setAttribute( 'type', value );
				features[ value ] = input.type !== 'text';
			} );

			return features;
		} )();


		if ( ! supportHtml5.date ) {
			console.log ("here");
			$( 'input.perfecty-push-notification-date[type="date"]' ).each( function() {
				$( this ).datepicker( {
					dateFormat: 'yy-mm-dd',
					minDate: new Date( $( this ).attr( 'min' ) ),
					showButtonPanel: true,
					changeMonth: true,
					changeYear: true,
					inline: true
				} );
			} );
		}
		
		if ( ! supportHtml5.time ) {
			$( 'input.perfecty-push-notification-time[type="time"]' ).each( function() {
				$( this ).timepicker( {
					timeFormat: 'HH:mm:ss',
					defaultTime: 'now',
					interval: 15,
					dynamic: false,
					dropdown: true,
					scrollbar: true,
					startTime: new Date()
				} );
			} );
		}

	} );
} )( jQuery );
