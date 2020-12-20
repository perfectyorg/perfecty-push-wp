<div class="formdata">
	<div>
		<p>
			<label for="perfecty-push-send-notification-title">Title</label>
			<br>
			<input id="perfecty-push-send-notification-title" name="perfecty-push-send-notification-title" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-title'] ); ?>" required>
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-message">Message</label>
			<br>
			<textarea id="perfecty-push-send-notification-message" name="perfecty-push-send-notification-message" cols="80" rows="4"
					  maxlength="1000"><?php echo esc_attr( stripslashes( $item['perfecty-push-send-notification-message'] ) ); ?></textarea>
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-image-custom">Custom image URL <i>(default: no image)</i></label>
			<br />
			<input id="perfecty-push-send-notification-image-custom" name="perfecty-push-send-notification-image-custom" type="checkbox"/>
			<input id="perfecty-push-send-notification-image" name="perfecty-push-send-notification-image" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-image'] ); ?>" disabled="disabled">
		</p>
	</div>
	<div>
		<p>
			<label for="perfecty-push-send-notification-url-to-open-custom">Url to open <i>(default: <?php echo site_url(); ?>)</i></label>
			<br>
			<input id="perfecty-push-send-notification-url-to-open-custom" name="perfecty-push-send-notification-url-to-open-custom" type="checkbox"/>
			<input id="perfecty-push-send-notification-url-to-open" name="perfecty-push-send-notification-url-to-open" type="text" value="<?php echo esc_attr( $item['perfecty-push-send-notification-url-to-open'] ); ?>" disabled="disabled">
		</p>
	</div>
</div>

