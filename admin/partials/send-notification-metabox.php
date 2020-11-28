<tbody >
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
		<textarea id="message" name="message" cols="80" rows="4" maxlength="1000"><?php echo esc_attr( stripslashes( $item['message'] ) ); ?></textarea>
	  </p>
	</div>
  </div>
</tbody>

