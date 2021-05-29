(function( $ ) {
	'use strict';
	const { __ } = wp.i18n;
	$( window ).load(
		function(){
			$( ".perfecty-push-confirm-action" ).click(
				function(e){
					var page    = $( this ).data( "page" );
					var action  = $( this ).data( "action" );
					var id      = $( this ).data( "id" );
					var wpnonce = $( this ).data( "nonce" );
					if (confirm( __( 'Are you sure?', 'perfecty-push-notifications' ) )) {
						var url              = "?page=" + page + "&action=" + action + "&id=" + id + "&_wpnonce=" + wpnonce;
						window.location.href = url;
					}
				}
			);

			$( "#perfecty-push-send-notification-image-custom" ).change(
				function(e){
					if (this.checked) {
						$( "#perfecty-push-send-notification-image" ).removeAttr( "disabled" );
					} else {
						$( "#perfecty-push-send-notification-image" ).attr( "disabled", "disabled" );
					}
				}
			);

			$( "#perfecty-push-send-notification-url-to-open-custom" ).change(
				function(e){
					if (this.checked) {
						$( "#perfecty-push-send-notification-url-to-open" ).removeAttr( "disabled" );
					} else {
						$( "#perfecty-push-send-notification-url-to-open" ).attr( "disabled", "disabled" );
					}
				}
			);
			
			$( "#form" ).submit(
				function(e){
					if ( $( "#perfecty-push-send-notification-schedule-notification" ).is( ":checked" ) ) {
						var scheduledTime = new Date( $( "#perfecty-push-send-notification-scheduled-date" ).val() + 'T' + $( "#perfecty-push-send-notification-scheduled-time" ).val() );
						var scheduledTimeEpoch = Math.round(scheduledTime.getTime() / 1000);
						var currentTimeEpoch = Math.round(Date.now() / 1000);
						var timeOffset = scheduledTimeEpoch - currentTimeEpoch;
						$( "#perfecty-push-send-notification-timeoffset").val( timeOffset );					
					}
				}
			);
			
			$( "#perfecty-push-send-notification-schedule-notification" ).change(
				function(e){
					if (this.checked) {
						$( "#perfecty-push-send-notification-scheduled-date" ).removeAttr( "disabled" );
						$( "#perfecty-push-send-notification-scheduled-time" ).removeAttr( "disabled" );

						var now = new Date();
						var dateString = now.getFullYear().toString()
							+ '-' + ( now.getMonth()+1 ).toString().padStart(2, '0') 
							+ '-' + now.getDate().toString().padStart(2, '0');
						$( "#perfecty-push-send-notification-scheduled-date" ).attr("min", dateString );
						$( "#perfecty-push-send-notification-scheduled-date" ).val( dateString );
						var timeString = now.getHours().toString().padStart(2, '0') 
							+ ":" + now.getMinutes().toString().padStart(2, '0')
							+ ":" + now.getSeconds().toString().padStart(2, '0');
							$( "#perfecty-push-send-notification-scheduled-time" ).val( timeString );
					} else {
						$( "#perfecty-push-send-notification-scheduled-date" ).val( '' );
						$( "#perfecty-push-send-notification-scheduled-time" ).val( '' );
						$( "#perfecty-push-send-notification-scheduled-date" ).attr( "disabled", "disabled" );
						$( "#perfecty-push-send-notification-scheduled-time" ).attr( "disabled", "disabled" );
					}
				}
			);
		}
	);
})( jQuery );
