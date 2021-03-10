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
	<img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/logo.png'; ?>"/>
	<p>
		<?php
		printf(
			// translators: %1$s is the opening a tag
			// translators: %2$s is the closing a tag
			esc_html__( '%1$sPerfecty Push WP%2$s is a WordPress plugin that allows you to send push notifications directly from your own server: No hidden fees, no third-party dependencies and you own your data.', 'perfecty-push-notifications' ),
			'<a href="https://perfecty.org/" target="_blank">',
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
			esc_html__( 'You can go to the %1$s wiki page %2$s', 'perfecty-push-notifications' ),
			'<a href="https://github.com/rwngallego/perfecty-push-wp/wiki" target="_blank">',
			'</a>'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Support', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
			// translators: %1$s is the opening a tag
			// translators: %2$s is the string URL
			// translators: %3$s is the closing a tag
			esc_html__( '- You can create an issue in our Github repo: %1$s %2$s %3$s', 'perfecty-push-notifications' ),
			'<a href="https://github.com/rwngallego/perfecty-push-wp/issues" target="_blank">',
			'https://github.com/rwngallego/perfecty-push-wp/issues',
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
			esc_html__( 'Please let me know, give it some stars in the %1$s Github %2$s repo or leave a review in the %3$s WordPress marketplace %4$s.', 'perfecty-push-notifications' ),
			'<a href="https://github.com/rwngallego/perfecty-push-wp">',
			'</a>',
			'<a href="https://wordpress.org/plugins/perfecty-push-notifications/#reviews">',
			'</a>'
		);
		?>
	</p>
	<p>
		<?php
		printf(
			// translators: %1$s is the opening a tag
			// translators: %2$s is the closing a tag
			esc_html__( 'Optionally you can %1$s send me %2$s a message.', 'perfecty-push-notifications' ),
			'<a href="https://rowinson.netlify.app/contact/" target="_blank">',
			'</a>'
		);
		?>
	</p>
</div>
