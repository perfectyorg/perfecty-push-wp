<?php
$perfecty_push_dialog_title        = isset( $options['dialog_title'] ) ? $options['dialog_title'] : 'Do you want to receive notifications?';
$perfecty_push_dialog_submit       = isset( $options['dialog_submit'] ) ? $options['dialog_submit'] : 'Continue';
$perfecty_push_dialog_cancel       = isset( $options['dialog_cancel'] ) ? $options['dialog_cancel'] : 'Not now';
$perfecty_push_settings_title      = isset( $options['settings_title'] ) ? $options['settings_title'] : 'Notifications preferences';
$perfecty_push_settings_subscribed = isset( $options['settings_subscribed'] ) ? $options['settings_subscribed'] : 'I want to receive notifications';
$perfecty_push_nonce               = wp_create_nonce( 'wp_rest' );
$perfecty_push_disabled            = defined( 'PERFECTY_PUSH_DISABLED' ) && PERFECTY_PUSH_DISABLED == true ? 'true' : 'false';
?>
<script language="javascript">
	window.PerfectyPushOptions = {
		path: "<?php echo PERFECTY_PUSH_JS_DIR; ?>",
		dialogControl: {
			title: "<?php echo $perfecty_push_dialog_title; ?>",
			submit: "<?php echo $perfecty_push_dialog_submit; ?>",
			cancel: "<?php echo $perfecty_push_dialog_cancel; ?>"
		},
		settingsControl: {
			title: "<?php echo $perfecty_push_settings_title; ?>",
			subscribed: "<?php echo $perfecty_push_settings_subscribed; ?>"
		},
		siteUrl: "<?php echo get_home_url(); ?>",
		serverUrl: "<?php echo PERFECTY_PUSH_SERVER_URL; ?>",
		vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY; ?>",
		nonce: "<?php echo $perfecty_push_nonce; ?>",
		disabled: <?php echo $perfecty_push_disabled; ?>,
	}
</script>
