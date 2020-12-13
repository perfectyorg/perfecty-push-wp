<div class="formdata">
	<div>
		<p>
			<label for="title">Title</label>
			<br>
			<input id="title" name="title" type="text" value="<?php echo esc_attr( $item['title'] ); ?>" required>
		</p>
	</div>
	<div>
		<p>
			<label for="message">Message</label>
			<br>
			<textarea id="message" name="message" cols="80" rows="4"
					  maxlength="1000"><?php echo esc_attr( stripslashes( $item['message'] ) ); ?></textarea>
		</p>
	</div>
	<div>
		<p>
			<label for="image">Image <i>(default: no image)</i></label>
			<br>
			<input id="image" name="image" type="text" value="<?php echo esc_attr( $item['image'] ); ?>">
		</p>
	</div>
	<div>
		<p>
			<label for="url_to_open">Url to open <i>(default: <?php echo site_url(); ?>)</i></label>
			<br>
			<input id="url_to_open" name="url_to_open" type="text" value="<?php echo esc_attr( $item['url_to_open'] ); ?>">
		</p>
	</div>
</div>

