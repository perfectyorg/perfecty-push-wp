<div class="wrap">

	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php printf( esc_html__( 'Logs', 'perfecty-push-notifications' ) ); ?></h2>

	<?php if ( ! empty( $message ) ) : ?>
		<div id="message" class="notice"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<?php if ( $enabled_logs ) : ?>
		<form method="POST" class="perfecty-push-logs-table">
			<input type="hidden" name="page" value="<?php echo esc_html( $page ); ?>"/>
			<?php $table->display(); ?>
		</form>
	<?php endif; ?>

</div>
