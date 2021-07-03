<p><input type="checkbox"
	id="perfecty_push_send_on_publish"
	name="perfecty_push_send_on_publish"
	<?php if ( true === $send_notification || true === $check_send_on_publish ) {
		echo 'checked="checked"';} ?>/>
<label for="perfecty_push_send_on_publish" id="perfecty_push_send_on_publish_label"><?php printf( esc_html__( 'Send notification on publish', 'perfecty-push-notifications' ) ); ?></label></p>

<p><input type="checkbox"
		id="perfecty_push_customize_notification"
		name="perfecty_push_customize_notification"
		<?php
		if ( true === $is_customized ) {
			echo 'checked="checked"'; }
		?>
		/>
<label for="perfecty_push_customize_notification" id="perfecty_push_customize_notification_label"><?php printf( esc_html__( 'Custom options', 'perfecty-push-notifications' ) ); ?></label></p>
</p>
<p style="padding-top: 10px"><label for="perfecty_push_notification_custom_title" id="perfecty_push_notification_custom_title_label"><?php printf( esc_html__( 'Notification title', 'perfecty-push-notifications' ) ); ?></label>
<input type="text" id="perfecty_push_notification_custom_title" name="perfecty_push_notification_custom_title" placeholder="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" value="<?php echo esc_attr( $notification_title ); ?>"></p>
<p style="padding-top: 10px"><label for="perfecty_push_notification_custom_body" id="perfecty_push_notification_custom_body_label"><?php printf( esc_html__( 'Notification text', 'perfecty-push-notifications' ) ); ?></label>
<input type="text" id="perfecty_push_notification_custom_body" name="perfecty_push_notification_custom_body" placeholder="<?php printf( esc_html__( 'The Post\'s current title', 'perfecty-push-notifications' ) ); ?>" value="<?php echo esc_attr( $notification_body ); ?>"></p>
