<?php
$perfecty_push_dialog_title    = ! empty( $options['dialog_title'] ) ? $options['dialog_title'] : 'Do you want to receive notifications?';
$perfecty_push_dialog_submit   = ! empty( $options['dialog_submit'] ) ? $options['dialog_submit'] : 'Continue';
$perfecty_push_dialog_cancel   = ! empty( $options['dialog_cancel'] ) ? $options['dialog_cancel'] : 'Not now';
$perfecty_push_settings_title  = ! empty( $options['settings_title'] ) ? $options['settings_title'] : 'Notifications preferences';
$perfecty_push_settings_opt_in = ! empty( $options['settings_opt_in'] ) ? $options['settings_opt_in'] : 'I want to receive notifications';
$perfecty_push_nonce           = wp_create_nonce( 'wp_rest' );

if ( Class_Perfecty_Push_Lib_Utils::is_disabled() ||
	( ! isset( $options['widget_enabled'] ) || $options['widget_enabled'] == 0 ) ) {
	$perfecty_push_disabled = 'true';
} else {
	$perfecty_push_disabled = 'false';
}

if ( isset( $options['unregister_conflicts'] ) && $options['unregister_conflicts'] == 1 ) {
	$perfecty_push_unregister_conflicts = 'true';
} else {
	$perfecty_push_unregister_conflicts = 'false';
}

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
			opt_in: "<?php echo $perfecty_push_settings_opt_in; ?>"
		},
		siteUrl: "<?php echo get_home_url(); ?>",
		serverUrl: "<?php echo PERFECTY_PUSH_SERVER_URL; ?>",
		vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY; ?>",
		nonce: "<?php echo $perfecty_push_nonce; ?>",
		disabled: <?php echo $perfecty_push_disabled; ?>,
		unregisterConflicts: <?php echo $perfecty_push_unregister_conflicts; ?>,
	}
</script>
