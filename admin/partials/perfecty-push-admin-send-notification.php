<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1><?php printf( esc_html__( 'Send notification', 'perfecty-push-notifications' ) ); ?></h1>

	<div class="notice notice-notice"><p><?php printf( esc_html__( 'You can also send notifications when publishing a post, edit the post > Perfecty Push > Send notifications on publish', 'perfecty-push-notifications' ) ); ?></p></div>
	<?php if ( ! empty( $notice ) ) : ?>
	<div id="notice" class="notice notice-warning"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $message ) ) : ?>
	<div id="message" class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<form id="form" method="POST">
		<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'perfecty_push_send_notification' ) ); ?>"/>

		<div class="metabox-holder" id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<?php do_meta_boxes( 'perfecty-push-send-notification', 'normal', $item ); ?>
					<input type="submit" value="<?php printf( esc_html__( 'Send notification', 'perfecty-push-notifications' ) ); ?>" id="submit" class="button-primary" name="submit">
				</div>
			</div>
		</div>
	</form>
</div>
