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
				<p>
					<label for="stats">Stats: </label>
					<br>
				<div>Total: <?php echo $item->total; ?></div>
				<div>Succeeded: <?php echo $item->succeeded; ?></div>
				<?php
				if ( $item->status != Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING ) {
					echo '<div>Failed: ' . ( $item->total - $item->succeeded ) . '</div>';
				}
				?>
				</p>
				<p>
					<label for="progress">Execution details: </label>
					<br>
				<div>
					<?php
					if ( $item->status == Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_RUNNING ) {
						echo $item->last_cursor . ' sent out of ' . $item->total;
					} else {
						echo 'Finished';
					}
					?>
				</div>
				<div>
					<?php echo 'Batch size: ' . $item->batch_size; ?>
				</div>
				<?php
				try {
					$finished_at = $item->finished_at ? new DateTime( $item->finished_at ) : new DateTime();
					$created_at  = new DateTime( $item->created_at );
					$diff        = $finished_at->diff( $created_at );
					?>
						<div><?php echo 'Duration: ' . $diff->format( '%Hh:%Im:%Ss' ); ?></div>
						<?php
				} catch ( Exception $ex ) {
					error_log( 'Could not calculate the duration: ' . $ex->getMessage() );
				}
				?>
				</p>
			</div>
		</form>
	</div>
	<a href="?page=<?php echo $page; ?>">Back</a>
</div>
