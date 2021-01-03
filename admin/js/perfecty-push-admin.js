const { __, _x, _n, _nx } = wp.i18n;
(function( $ ) {
	'use strict';

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
		}
	);
})( jQuery );
