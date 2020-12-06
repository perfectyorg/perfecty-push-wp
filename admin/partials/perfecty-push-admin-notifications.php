<div class="wrap">

	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2>Notifications</h2>
	<a class="add-new-h2 button-primary" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=perfecty-push-send-notification' ); ?>">Send notification</a>

	<?php
	if ( ! empty( $message ) ) {
		echo $message;}
	?>

	<form id="notifications-table" method="POST">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>"/>
		<?php $table->display(); ?>
	</form>

</div>
