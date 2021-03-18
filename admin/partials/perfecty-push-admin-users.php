<div class="wrap">

	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php printf( esc_html__( 'Users', 'perfecty-push-notifications' ) ); ?></h2>

	<?php
	if ( ! empty( $message ) ) {
		echo esc_html( $message );}
	?>

	<form method="POST">
		<input type="hidden" name="page" value="<?php echo esc_html( $page ); ?>"/>
		<?php $table->display(); ?>
	</form>

</div>
