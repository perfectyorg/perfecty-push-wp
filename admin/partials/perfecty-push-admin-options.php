<div class="wrap">
	<h1><?php printf( esc_html__( 'Perfecty Push Settings', 'perfecty-push-notifications' ) ); ?></h1>
	<form method="post" action="options.php">
	<?php
		settings_fields( 'perfecty_group' );
		do_settings_sections( 'perfecty-push-options' );
		submit_button();
	?>
	</form>
</div>
