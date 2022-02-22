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
			<span> <?php printf( esc_html__( 'or', 'perfecty-push-notifications' ) ); ?> </span>
			<input type="button" id="perfecty-push-send-notification-image-select" class="button" value="<?php printf( esc_html__( 'Select image', 'perfecty-push-notifications' ) ); ?>" disabled="disabled"/>
			<div class="perfecty-push-send-notification-image-preview-container"><img src="" class="perfecty-push-send-notification-image-preview"/></div>
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
		if ( empty( $icon_url ) ) {
			echo '<i>' . esc_html__( 'Select icon in Perfecty Push > Settings > Default Icon', 'perfecty-push-notifications' ) . '</i>';
		} else {
			echo '<br/><img class="perfecty-push-default-icon-preview" src="' . esc_html( $icon_url ) . '" alt="icon"/>';
		}
		?>
	</div>
	<div>
		<p>
		<label for="perfecty-push-send-notification-schedule-notification"><?php printf( esc_html__( 'Schedule notification', 'perfecty-push-notifications' ) ); ?> <i><?php printf( esc_html__( '(default: now)', 'perfecty-push-notifications' ) ); ?></i></label>
		<br />
		<input id="perfecty-push-send-notification-schedule-notification" name="perfecty-push-send-notification-schedule-notification" type="checkbox"/>
		<input id="perfecty-push-send-notification-scheduled-date" name="perfecty-push-send-notification-scheduled-date" type="date" class="perfecty-push-notification-date" value="" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="2021-05-26" disabled="disabled"/>
		<input id="perfecty-push-send-notification-scheduled-time" name="perfecty-push-send-notification-scheduled-time" type="time" class="perfecty-push-notification-time" value="" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" step=1 disabled="disabled"/>
		<input id="perfecty-push-send-notification-timeoffset" name="perfecty-push-send-notification-timeoffset" type="hidden" value=""/>
		</p>
	</div>
</div>

