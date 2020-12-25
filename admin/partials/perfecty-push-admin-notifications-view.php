<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1>Notification details</h1>

	<tbody>
	<div class="formdata perfecty-push-view-form">
		<form>
			<div>
				<p>
					<label for="payload">Payload: </label>
					<br>
				<div class="perfecty-push-view-payload">
					<?php $payload = $item->payload; ?>
					<div><span>Title:</span> <?php echo $payload->title; ?></div>
					<div><span>Body:</span> <?php echo $payload->body; ?></div>
					<div>
						<span>Url to open:</span>
						<a href="<?php echo $payload->extra->url_to_open; ?>"
						   target="_blank"><?php echo $payload->extra->url_to_open; ?></a>
					</div>
					<div>
						<span>Icon:</span>
						<?php
						if ( empty( $payload->icon ) ) {
							echo 'No';
						} else {
							echo '<br/><img class="perfecty-push-view-payload-icon" src="' . $payload->icon . '" alt="icon"/>';
						}
						?>
					</div>
					<div>
						<span>Image:</span>
						<?php
						if ( empty( $payload->image ) ) {
							echo 'No';
						} else {
							echo '<br /><img class="perfecty-push-view-payload-image" src="' . $payload->image . '" alt="image"/>';
						}
						?>
					</div>
				</div>
				</p>
				<p>
					<label for="created_at">Date: </label>
					<br>
				<div><?php echo $item->created_at; ?></div>
				</p>
				<p>
					<label for="status">Status: </label>
					<br>
				<div><?php echo $item->status; ?></div>
				</p>
				<?php
				if ( $item->completed_at ) {
					try {
						$completed_at = new DateTime( $item->completed_at );
						$created_at   = new DateTime( $item->created_at );
						$diff         = $completed_at->diff( $created_at );
						?>
						<p>
							<label for="duration">Duration: </label>
							<br/>
						<div><?php echo $diff->format( '%Hh:%Im:%Ss' ); ?></div>
						</p>
						<?php
					} catch ( Exception $ex ) {
						error_log( 'Could not calculate the duration: ' . $ex->getMessage() );
					}
				}
				?>
				<p>
					<label for="stats">Stats: </label>
					<br>
				<div>Total: <?php echo $item->total; ?> - Succeeded: <?php echo $item->succeeded; ?> |
					Failed: <?php echo( $item->total - $item->succeeded ); ?></div>
				</p>
				<p>
					<label for="progress">Execution progress: </label>
					<br>
				<div>
					<?php echo $item->last_cursor; ?> sent out of <?php echo $item->total; ?> (Batches
					of <?php echo $item->batch_size; ?>)
				</div>
				</p>
			</div>
		</form>
	</div>
	<a href="?page=<?php echo $page; ?>">Back</a>
</div>
