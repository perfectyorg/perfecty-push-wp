<div class="wrap">
	<h1><?php printf( esc_html__( 'Perfecty Push Settings', 'perfecty-push-notifications' ) ); ?></h1>
	<div class="notice notice-info">
		<p>
			<?php
			printf(
			// translators: %1$s is the opening a tag
			// translators: %2$s is the closing a tag
				esc_html__( 'Customize how Perfecty Push works, all the Settings are described in the %1$sdocumentation%2$s.', 'perfecty-push-notifications' ),
				'<a target="_blank" href="https://docs.perfecty.org/wp/configuration/">',
				'</a>'
			);
			?>
		</p>
	</div>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'perfecty_group' );
		do_settings_sections( 'perfecty-push-options' );
		submit_button();
		?>
	</form>
</div>
