<?php
$perfecty_push_dialog_title          = ! empty( $options['dialog_title'] ) ? $options['dialog_title'] : esc_html__( 'Do you want to receive notifications?', 'perfecty-push-notifications' );
$perfecty_push_dialog_submit         = ! empty( $options['dialog_submit'] ) ? $options['dialog_submit'] : esc_html__( 'Continue', 'perfecty-push-notifications' );
$perfecty_push_dialog_cancel         = ! empty( $options['dialog_cancel'] ) ? $options['dialog_cancel'] : esc_html__( 'Not now', 'perfecty-push-notifications' );
$perfecty_push_settings_title        = ! empty( $options['settings_title'] ) ? $options['settings_title'] : esc_html__( 'Notifications preferences', 'perfecty-push-notifications' );
$perfecty_push_settings_opt_in       = ! empty( $options['settings_opt_in'] ) ? $options['settings_opt_in'] : esc_html__( 'I want to receive notifications', 'perfecty-push-notifications' );
$perfecty_push_settings_update_error = ! empty( $options['settings_update_error'] ) ? $options['settings_update_error'] : esc_html__( 'Could not change the preference, try again', 'perfecty-push-notifications' );
$perfecty_push_nonce                 = wp_create_nonce( 'wp_rest' );
$perfecty_push_server_url            = ! empty( $options['server_url'] ) ? $options['server_url'] : get_rest_url();

if ( Class_Perfecty_Push_Lib_Utils::is_disabled() ||
	( ! isset( $options['widget_enabled'] ) || $options['widget_enabled'] == 0 ) ) {
	$perfecty_push_enabled = false;
} else {
	$perfecty_push_enabled = true;
}
if ( isset( $options['unregister_conflicts'] ) && $options['unregister_conflicts'] == 1 ) {
	$perfecty_push_unregister_conflicts = true;
} else {
	$perfecty_push_unregister_conflicts = false;
}
if ( isset( $options['widget_debugging_enabled'] ) && $options['widget_debugging_enabled'] == 1 ) {
	$perfecty_push_widget_debugging_enabled = true;
} else {
	$perfecty_push_widget_debugging_enabled = false;
}

?>
<script language="javascript">
	window.PerfectyPushOptions = {
		path: "<?php echo PERFECTY_PUSH_JS_DIR; ?>",
		dialogTitle: "<?php echo $perfecty_push_dialog_title; ?>",
		dialogSubmit: "<?php echo $perfecty_push_dialog_submit; ?>",
		dialogCancel: "<?php echo $perfecty_push_dialog_cancel; ?>",
		settingsTitle: "<?php echo $perfecty_push_settings_title; ?>",
		settingsOptIn: "<?php echo $perfecty_push_settings_opt_in; ?>",
		settingsUpdateError: "<?php echo $perfecty_push_settings_update_error; ?>",
		serverUrl: "<?php echo $perfecty_push_server_url; ?>perfecty-push",
		vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY; ?>",
		token: "<?php echo $perfecty_push_nonce; ?>",
		tokenHeader: "X-WP-Nonce",
		enabled: <?php echo $perfecty_push_enabled ? 'true' : 'false'; ?>,
		unregisterConflicts: <?php echo $perfecty_push_unregister_conflicts ? 'true' : 'false'; ?>,
		serviceWorkerScope: "<?php echo PERFECTY_PUSH_SERVICE_WORKER_SCOPE; ?>",
		loggerLevel: "<?php echo $perfecty_push_widget_debugging_enabled ? 'debug' : 'error'; ?>",
		loggerVerbose: <?php echo $perfecty_push_widget_debugging_enabled ? 'true' : 'false'; ?>
	}
</script>
