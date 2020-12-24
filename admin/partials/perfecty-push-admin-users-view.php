<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1>User details</h1>

	<tbody>
	<div class="formdata perfecty-push-view-form">
		<form>
			<div>
				<p>
					<label for="uuid">UUID: </label>
					<br>
				<div><?php echo $item->uuid; ?></div>
				</p>
				<p>
					<label for="ip">IP Address: </label>
					<br>
				<div><?php echo $item->remote_ip; ?></div>
				</p>
				<p>
					<label for="endpoint">Registered endpoint: </label>
					<br>
				<div><?php echo $item->endpoint; ?></div>
				</p>
				<p>
					<label for="creation_time">Registered at: </label>
					<br>
				<div><?php echo $item->creation_time; ?></div>
				</p>
				<p>
					<label for="key_auth">Key auth: </label>
					<br>
				<div><?php echo $item->key_auth; ?></div>
				</p>
				<p>
					<label for="key_p256dh">Key p256dh: </label>
					<br>
				<div><?php echo $item->key_p256dh; ?></div>
				</p>
				<p>
					<label for="is_active">Is active: </label>
					<br>
				<div><?php echo $item->is_active == 1 ? 'Yes' : 'No'; ?></div>
				</p>
				<p>
					<label for="disabled">Disabled: </label>
					<br>
				<div><?php echo $item->disabled == 1 ? 'Yes' : 'No'; ?></div>
				</p>
			</div>
		</form>
	</div>
	<a href="?page=<?php echo $page; ?>">Back</a>
</div>
