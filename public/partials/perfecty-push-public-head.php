<?php
$perfecty_push_dialog_title                    = ! empty( $options['dialog_title'] ) ? $options['dialog_title'] : PERFECTY_PUSH_OPTIONS_DIALOG_TITLE;
$perfecty_push_dialog_submit                   = ! empty( $options['dialog_submit'] ) ? $options['dialog_submit'] : PERFECTY_PUSH_OPTIONS_DIALOG_CONTINUE;
$perfecty_push_dialog_cancel                   = ! empty( $options['dialog_cancel'] ) ? $options['dialog_cancel'] : PERFECTY_PUSH_OPTIONS_DIALOG_CANCEL;
$perfecty_push_settings_title                  = ! empty( $options['settings_title'] ) ? $options['settings_title'] : PERFECTY_PUSH_OPTIONS_SETTINGS_TITLE;
$perfecty_push_settings_opt_in                 = ! empty( $options['settings_opt_in'] ) ? $options['settings_opt_in'] : PERFECTY_PUSH_OPTIONS_SETTINGS_OPT_IN;
$perfecty_push_settings_update_error           = ! empty( $options['settings_update_error'] ) ? $options['settings_update_error'] : PERFECTY_PUSH_OPTIONS_SETTINGS_UPDATE_ERROR;
$perfecty_push_nonce                           = wp_create_nonce( 'wp_rest' );
$perfecty_push_server_url                      = ! empty( $options['server_url'] ) ? $options['server_url'] : get_rest_url( null, 'perfecty-push' );
$perfecty_push_unregister_conflicts_expression = ! empty( $options['unregister_conflicts_expression'] ) ? $options['unregister_conflicts_expression'] : PERFECTY_PUSH_UNREGISTER_CONFLICTS_EXPRESSION;
$perfecty_push_prompt_icon_url                 = isset( $options['notifications_default_icon'] ) && ! empty( $options['notifications_default_icon'] ) ? wp_get_attachment_url( $options['notifications_default_icon'] ) : '';
$perfecty_push_visits_to_display_prompt        = isset( $options['visits_to_display_prompt'] ) && $options['visits_to_display_prompt'] ? $options['visits_to_display_prompt'] : 0;

if ( Perfecty_Push_Lib_Utils::is_disabled() ||
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
if ( isset( $options['widget_hide_bell_after_subscribe'] ) && $options['widget_hide_bell_after_subscribe'] == 1 ) {
	$perfecty_push_widget_hide_bell_after_subscribe = true;
} else {
	$perfecty_push_widget_hide_bell_after_subscribe = false;
}
if ( isset( $options['widget_ask_permissions_directly'] ) && $options['widget_ask_permissions_directly'] == 1 ) {
	$perfecty_push_widget_ask_permissions_directly = true;
} else {
	$perfecty_push_widget_ask_permissions_directly = false;
}

?>
<script>
	window.PerfectyPushOptions = {
		path: "<?php echo PERFECTY_PUSH_JS_DIR; ?>",
		dialogTitle: "<?php echo $perfecty_push_dialog_title; ?>",
		dialogSubmit: "<?php echo $perfecty_push_dialog_submit; ?>",
		dialogCancel: "<?php echo $perfecty_push_dialog_cancel; ?>",
		settingsTitle: "<?php echo $perfecty_push_settings_title; ?>",
		settingsOptIn: "<?php echo $perfecty_push_settings_opt_in; ?>",
		settingsUpdateError: "<?php echo $perfecty_push_settings_update_error; ?>",
		serverUrl: "<?php echo $perfecty_push_server_url; ?>",
		vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY; ?>",
		token: "<?php echo $perfecty_push_nonce; ?>",
		tokenHeader: "X-WP-Nonce",
		enabled: <?php echo $perfecty_push_enabled ? 'true' : 'false'; ?>,
		unregisterConflicts: <?php echo $perfecty_push_unregister_conflicts ? 'true' : 'false'; ?>,
		serviceWorkerScope: "<?php echo PERFECTY_PUSH_SERVICE_WORKER_SCOPE; ?>",
		loggerLevel: "<?php echo $perfecty_push_widget_debugging_enabled ? 'debug' : 'error'; ?>",
		loggerVerbose: <?php echo $perfecty_push_widget_debugging_enabled ? 'true' : 'false'; ?>,
		hideBellAfterSubscribe: <?php echo $perfecty_push_widget_hide_bell_after_subscribe ? 'true' : 'false'; ?>,
		askPermissionsDirectly: <?php echo $perfecty_push_widget_ask_permissions_directly ? 'true' : 'false'; ?>,
		unregisterConflictsExpression: "<?php echo $perfecty_push_unregister_conflicts_expression; ?>",
		promptIconUrl: "<?php echo $perfecty_push_prompt_icon_url; ?>",
		visitsToDisplayPrompt: <?php echo $perfecty_push_visits_to_display_prompt; ?>
	}
</script>
