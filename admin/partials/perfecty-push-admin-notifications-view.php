<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1>Notification details</h1>

	<tbody >
  <div class="formdata perfecty-push-view-form">
	<form >
	  <div>
		<p>
		  <label for="payload">Payload: </label>
		  <br>
					<div><?php echo $item->payload; ?></div>
		</p>
		<p>
		  <label for="creation_time">Date: </label>
		  <br>
					<div><?php echo $item->creation_time; ?></div>
		</p>
		<p>
		  <label for="status">Status: </label>
		  <br>
					<div><?php echo $item->status; ?></div>
		</p>
		<p>
		  <label for="stats">Stats: </label>
		  <br>
					<div>Total: <?php echo $item->total; ?> - Succeeded: <?php echo $item->succeeded; ?> | Failed: <?php echo ( $item->total - $item->succeeded ); ?></div>
		</p>
		<p>
		  <label for="progress">Execution progress: </label>
		  <br>
					<div>
						<?php echo $item->last_cursor; ?> sent out of <?php echo $item->total; ?> (Batches of <?php echo $item->batch_size; ?>)
					</div>
		</p>
	  </div>
		</form>
	</div>
	<a href="?page=<?php echo $_REQUEST['page']; ?>">Back</a>
</div>
