(function( $ ) {
	'use strict';
	const { __ } = wp.i18n;
	$( window ).on(
		'load',
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

			// Send Notification > Select image
			$( "#perfecty-push-send-notification-image-custom" ).change(
				function(e){
					if (this.checked) {
						$( "#perfecty-push-send-notification-image" ).removeAttr( "disabled" );
						$( "#perfecty-push-send-notification-image-select" ).removeAttr( "disabled" );
					} else {
						$( "#perfecty-push-send-notification-image" ).attr( "disabled", "disabled" );
						$( "#perfecty-push-send-notification-image" ).val( "" );
						$( "#perfecty-push-send-notification-image-select" ).attr( "disabled", "disabled" );
						$( '.perfecty-push-send-notification-image-preview' ).attr( 'src', '' );
						$( '.perfecty-push-send-notification-image-preview-container' ).hide();
					}
				}
			);

			// Send Notification > Select image > Choose
			var perfecty_send_notification_choose;
			$( '#perfecty-push-send-notification-image-select' ).on(
				'click',
				function(event) {
					event.preventDefault();
					perfecty_send_notification_choose = wp.media(
						{
							title: 'Select image to upload',
							button: {
								text: 'Use this image',
							},
							multiple: false
						}
					);
					perfecty_send_notification_choose.on(
						'select',
						function() {
							let attachment = perfecty_send_notification_choose.state().get( 'selection' ).first().toJSON();
							$( '.perfecty-push-send-notification-image-preview-container' ).show();
							$( '.perfecty-push-send-notification-image-preview' ).attr( 'src', attachment.url );
							$( "#perfecty-push-send-notification-image" ).val( attachment.url );
						}
					);
					perfecty_send_notification_choose.open();
				}
			);
			if ($( '.perfecty-push-send-notification-image-preview' ).attr( 'src' ) !== "") {
				$( '.perfecty-push-send-notification-image-preview-container' ).show();
			}

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
						var scheduledTime      = new Date( $( "#perfecty-push-send-notification-scheduled-date" ).val() + 'T' + $( "#perfecty-push-send-notification-scheduled-time" ).val() );
						var scheduledTimeEpoch = Math.round( scheduledTime.getTime() / 1000 );
						var currentTimeEpoch   = Math.round( Date.now() / 1000 );
						var timeOffset         = scheduledTimeEpoch - currentTimeEpoch;
						$( "#perfecty-push-send-notification-timeoffset" ).val( timeOffset );
					}
				}
			);

			$( "#perfecty-push-send-notification-schedule-notification" ).change(
				function(e){
					if (this.checked) {
						$( "#perfecty-push-send-notification-scheduled-date" ).removeAttr( "disabled" );
						$( "#perfecty-push-send-notification-scheduled-time" ).removeAttr( "disabled" );

						var now        = new Date();
						var dateString = now.getFullYear().toString()
							+ '-' + ( now.getMonth() + 1 ).toString().padStart( 2, '0' )
							+ '-' + now.getDate().toString().padStart( 2, '0' );
						$( "#perfecty-push-send-notification-scheduled-date" ).attr( "min", dateString );
						$( "#perfecty-push-send-notification-scheduled-date" ).val( dateString );
						var timeString = now.getHours().toString().padStart( 2, '0' )
							+ ":" + now.getMinutes().toString().padStart( 2, '0' )
							+ ":" + now.getSeconds().toString().padStart( 2, '0' );
							$( "#perfecty-push-send-notification-scheduled-time" ).val( timeString );
					} else {
						$( "#perfecty-push-send-notification-scheduled-date" ).val( '' );
						$( "#perfecty-push-send-notification-scheduled-time" ).val( '' );
						$( "#perfecty-push-send-notification-scheduled-date" ).attr( "disabled", "disabled" );
						$( "#perfecty-push-send-notification-scheduled-time" ).attr( "disabled", "disabled" );
					}
				}
			);

			$( "#perfecty_push\\[widget_ask_permissions_directly\\]" ).change(
				function(e){
					if (this.checked) {
						$( ".perfecty-push-options-dialog-group" ).closest( 'tr' ).hide();
					} else {
						$( ".perfecty-push-options-dialog-group" ).closest( 'tr' ).show();
					}
				}
			);
			if ($( "#perfecty_push\\[widget_ask_permissions_directly\\]" ).is( ':checked' )) {
				$( ".perfecty-push-options-dialog-group" ).closest( 'tr' ).hide();
			}

			$( "#perfecty_push\\[unregister_conflicts\\]" ).change(
				function(e){
					if (this.checked) {
						$( ".perfecty-push-options-unregister-conflicts-group" ).closest( 'tr' ).show();
					} else {
						$( ".perfecty-push-options-unregister-conflicts-group" ).closest( 'tr' ).hide();
					}
				}
			);
			if ( ! $( "#perfecty_push\\[unregister_conflicts\\]" ).is( ':checked' )) {
				$( ".perfecty-push-options-unregister-conflicts-group" ).closest( 'tr' ).hide();
			}

			// Settings > Select default icon
			var perfecty_icon_choose;
			$( '#perfecty-push-default-icon-select' ).on(
				'click',
				function(event) {
					event.preventDefault();
					perfecty_icon_choose = wp.media(
						{
							title: 'Select image to upload',
							button: {
								text: 'Use this image',
							},
							multiple: false
						}
					);
					perfecty_icon_choose.on(
						'select',
						function() {
							let attachment = perfecty_icon_choose.state().get( 'selection' ).first().toJSON();
							$( '.perfecty-push-default-icon-preview-container' ).show();
							$( '.perfecty-push-default-icon-preview' ).attr( 'src', attachment.url );
							$( "#perfecty_push\\[notifications_default_icon\\]" ).val( attachment.id );
						}
					);
					perfecty_icon_choose.open();
				}
			);
			if ($( '.perfecty-push-default-icon-preview' ).attr( 'src' ) !== "") {
				$( '.perfecty-push-default-icon-preview-container' ).show();
			}
			
			// Send on publish metabox
			var hide_custom_fields = function() {
				$("#perfecty_push_notification_custom_title").prop("disabled",true);
				$("#perfecty_push_notification_custom_title").hide();
				$("#perfecty_push_notification_custom_title_label").hide();
				$("#perfecty_push_notification_custom_body").prop("disabled",true);
				$("#perfecty_push_notification_custom_body").hide();
				$("#perfecty_push_notification_custom_body_label").hide();
			}
			
			var show_custom_fields = function() {
				$("#perfecty_push_notification_custom_title").show();
				$("#perfecty_push_notification_custom_title_label").show();
				$("#perfecty_push_notification_custom_title").prop("disabled",false);
				$("#perfecty_push_notification_custom_body").show();
				$("#perfecty_push_notification_custom_body_label").show();
				$("#perfecty_push_notification_custom_body").prop("disabled",false);
				
			}
			
			if ( $( "#perfecty_push_customize_notification" ).is(':checked') ){
				show_custom_fields();
			} else {
				hide_custom_fields();
				}
			$( "#perfecty_push_customize_notification" ).change(
				function(e){
					if (this.checked) {
						show_custom_fields();
					} else {
						hide_custom_fields();
					}
				}
			);
			
		}
	);
})( jQuery );
