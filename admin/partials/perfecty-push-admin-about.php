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
		__(
			'<a href="%s" target="_blank">Perfecty Push WP</a>
		is a WordPress plugin that allows you to send push notifications
		directly from your own server: No hidden fees, no third-party dependencies and you
		own your data.',
			'perfecty-push-notifications'
		),
		'https://github.com/rwngallego/perfecty-push-wp'
	);
	?>
	</p>
	<h2><?php printf( esc_html__( 'Documentation', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
			__(
				'You can go to the
		<a href="%s" target="_blank">wiki page</a>',
				'perfecty-push-notifications'
			),
			'https://github.com/rwngallego/perfecty-push-wp/wiki'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Support', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
			__(
				'- You can create an issue in our Github repo:
		<a href="%1$s" target="_blank">%2$s</a>',
				'perfecty-push-notifications'
			),
			'https://github.com/rwngallego/perfecty-push-wp/issues',
			'https://github.com/rwngallego/perfecty-push-wp/issues'
		);
		?>
	</p>
	<h2><?php printf( esc_html__( 'Did you like it?', 'perfecty-push-notifications' ) ); ?></h2>
	<p>
		<?php
		printf(
			__(
				'Please let me know, give it some stars in the <a href="%1$s">Github</a> repo
		or leave a review in the <a href="%2$s">WordPress marketplace</a>.',
				'perfecty-push-notifications'
			),
			'https://github.com/rwngallego/perfecty-push-wp',
			'https://wordpress.org/plugins/perfecty-push-notifications/#reviews'
		);
		?>
	</p>
	<p>
		<?php printf( __( 'Optionally you can <a href="%s" target="_blank">send me</a> a message.', 'perfecty-push-notifications' ), 'https://rowinson.netlify.app/contact/' ); ?>
	</p>
</div>
