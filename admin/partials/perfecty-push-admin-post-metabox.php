<p><input type="checkbox"
	   id="perfecty_push_send_on_publish"
	   name="perfecty_push_send_on_publish"
	   <?php if ( $send_notification === true ) {
		   echo 'checked="checked"';} ?>"/>
<label for="perfecty_push_send_on_publish"><?php printf( esc_html__( 'Send notification on publish', 'perfecty-push-notifications' ) ); ?></label></p>
<p><input type="checkbox"
	   id="perfecty_push_send_featured_img"
	   name="perfecty_push_send_featured_img"
	   <?php if ( $send_featured_img === true ) {
		   echo 'checked="checked"';} ?>"/>
<label for="perfecty_push_send_featured_img"><?php printf( esc_html__( 'Send featured img in payload', 'perfecty-push-notifications' ) ); ?></label></p>
<p>&nbsp;</p>
<p><label for="perfecty_push_notification_custom_title"><?php printf( esc_html__( 'Custom notification title (leave blank for blogname)', 'perfecty-push-notifications' ) ); ?></label>
<input type="text" id="perfecty_push_notification_custom_title" name="perfecty_push_notification_custom_title" value=" <?php
if ( $notification_title === '' ) {
	printf( esc_html__( 'New post on %s', 'perfecty-push-notifications' ), get_bloginfo( 'name' ) );
} else {
	printf( esc_html( $notification_title ) );
}?>"></p>
