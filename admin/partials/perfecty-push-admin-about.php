<?php
/**
 * Prints the about page
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin/partials
 */

?>
<div class="wrap">
	<h1><?php printf( esc_html__( 'About Perfecty Push', 'perfecty-push-notifications' ) ); ?></h1>
	<a href="https://perfecty.org/" target="_blank"><img src="<?php echo esc_html( plugin_dir_url( __DIR__ ) ) . 'img/logo.png'; ?>"/></a>
	<p>
		<?php
		printf(
			// translators: %1$s is the opening a tag
			// translators: %2$s is the closing a tag
			esc_html__( 'Send push notifications directly from your own server: No hidden fees, no third-party dependencies and you own your data.', 'perfecty-push-notifications' )
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Donations', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
		// translators: %1$s is the opening a tag
		// translators: %2$s is the closing a tag
			esc_html__( 'If you want to support the development of the project, you can %1$sdonate here%2$s.', 'perfecty-push-notifications' ),
			'<a href="https://paypal.me/tatalata777" target="_blank">',
			'</a>'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Documentation', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
		// translators: %1$s is the opening a tag
		// translators: %2$s is the closing a tag
			esc_html__( 'Check the documentation site at %1$shttps://docs.perfecty.org/%2$s.', 'perfecty-push-notifications' ),
			'<a href="https://docs.perfecty.org/" target="_blank">',
			'</a>'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Did you like it?', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
		// translators: %1$s is the opening a tag
		// translators: %2$s is the closing a tag
		// translators: %3$s is the opening a tag
		// translators: %4$s is the closing a tag
			esc_html__( 'Please let us know and give a review in the %3$s WordPress marketplace%4$s.', 'perfecty-push-notifications' ),
			'<a href="https://github.com/perfectyorg/perfecty-push-wp">',
			'</a>',
			'<a href="https://wordpress.org/plugins/perfecty-push-notifications/#reviews">',
			'</a>'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Contact us', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
		// translators: %1$s is the opening a tag
		// translators: %2$s is the closing a tag
			esc_html__( '%1$sSend us%2$s a message directly.', 'perfecty-push-notifications' ),
			'<a href="https://perfecty.org/contact/" target="_blank">',
			'</a>'
		);
		?>
	</p>
	<p>
		<?php
		printf(
		// translators: %1$s is the opening a tag
		// translators: %2$s is the closing a tag
			esc_html__( 'Follow us on %1$s Facebook%2$s.', 'perfecty-push-notifications' ),
			'<a href="https://www.facebook.com/perfectypush" target="_blank">',
			'</a>'
		);
		?>
	</p>
</div>
