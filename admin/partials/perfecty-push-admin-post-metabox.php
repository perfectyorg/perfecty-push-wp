<input type="checkbox"
	   id="perfecty_push_send_on_publish"
	   name="perfecty_push_send_on_publish"
	   <?php if ( $send_notification === true ) {
		   echo 'checked="checked"';} ?>"/>
<label for="perfecty_push_send_on_publish"><?php printf( esc_html__( 'Send notification on publish', 'perfecty-push-notifications' ) ); ?></label>
