<?php

/**
 * Prints the settings page
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin/partials
 */
?>
<div class="wrap">
	<h1>Perfecty Push Settings</h1>
	<form method="post" action="options.php">
	<?php
		settings_fields( 'perfecty_group' );
		do_settings_sections( 'perfecty-push-options' );
		submit_button();
	?>
	</form>
</div>
