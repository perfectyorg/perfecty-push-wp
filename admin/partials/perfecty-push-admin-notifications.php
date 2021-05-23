<div class="wrap">

	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php printf( esc_html__( 'Notification jobs', 'perfecty-push-notifications' ) ); ?></h2>
	<a class="add-new-h2 button-primary" href="<?php echo esc_html( get_admin_url( get_current_blog_id(), 'admin.php?page=perfecty-push-send-notification' ) ); ?>"><?php printf( esc_html__( 'Send notification', 'perfecty-push-notifications' ) ); ?></a>

	<?php
	if ( ! empty( $message ) ) {
		echo esc_html( $message );}
	?>

	<form method="POST">
		<input type="hidden" name="page" value="<?php echo esc_html( $page ); ?>"/>
		<?php $table->display(); ?>
	</form>

</div>
