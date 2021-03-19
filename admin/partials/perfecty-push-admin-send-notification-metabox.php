<div class="formdata">
	<div>
		<p>
			<label for="perfecty-push-send-notification-title"><?php printf( esc_html__( 'Title', 'perfecty-push-notifications' ) ); ?></label>
			<br>
			<input id="perfecty-push-send-notification-title" name="perfecty-push-send-notification-title" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-title'] ); ?>" required>
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-message"><?php printf( esc_html__( 'Message', 'perfecty-push-notifications' ) ); ?></label>
			<br>
			<textarea id="perfecty-push-send-notification-message" name="perfecty-push-send-notification-message" cols="80" rows="4"
					  maxlength="1000"><?php echo esc_attr( stripslashes( $item['perfecty-push-send-notification-message'] ) ); ?></textarea>
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-image-custom"><?php printf( esc_html__( 'Image URL', 'perfecty-push-notifications' ) ); ?> <i><?php printf( esc_html__( '(default: no image)', 'perfecty-push-notifications' ) ); ?></i></label>
			<br />
			<input id="perfecty-push-send-notification-image-custom" name="perfecty-push-send-notification-image-custom" type="checkbox"/>
			<input id="perfecty-push-send-notification-image" name="perfecty-push-send-notification-image" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-image'] ); ?>" disabled="disabled">
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-url-to-open-custom"><?php printf( esc_html__( 'Url to open', 'perfecty-push-notifications' ) ); ?> <i>
			  <?php printf( esc_html__( '(default: %s)', 'perfecty-push-notifications' ), esc_html( site_url() ) ); ?></i></label>
			<br>
			<input id="perfecty-push-send-notification-url-to-open-custom" name="perfecty-push-send-notification-url-to-open-custom" type="checkbox"/>
			<input id="perfecty-push-send-notification-url-to-open" name="perfecty-push-send-notification-url-to-open" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-url-to-open'] ); ?>" disabled="disabled">
		</p>
	</div>
	<div>
		<span><?php printf( esc_html__( 'Icon:', 'perfecty-push-notifications' ) ); ?></span><br />
		<?php
		$icon_url = get_site_icon_url();
		if ( empty( $icon_url ) ) {
			echo '<i>' . esc_html__( 'Add a website icon in Appearance > Customize > Site Identity', 'perfecty-push-notifications' ) . '</i>';
		} else {
			echo '<br/><img class="perfecty-push-send-notification-icon" src="' . esc_html( $icon_url ) . '" alt="icon"/>';
		}
		?>
	</div>
</div>

