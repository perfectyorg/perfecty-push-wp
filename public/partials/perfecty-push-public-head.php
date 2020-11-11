<?php
  $dialog_title = isset($options['dialog_title']) ? $options['dialog_title'] : 'Do you want to receive notifications?';
  $dialog_submit = isset($options['dialog_submit']) ? $options['dialog_submit'] : 'Continue';
  $dialog_cancel = isset($options['dialog_cancel']) ? $options['dialog_cancel'] : 'Not now';
  $settings_title = isset($options['settings_title']) ? $options['settings_title'] : 'Notifications preferences';
  $settings_subscribed = isset($options['settings_subscribed']) ? $options['settings_subscribed'] : 'I want to receive notifications';
  $nonce = wp_create_nonce('wp_rest');
?>
<script language="javascript">
  window.PerfectyPushOptions = {
    path: "<?php echo PERFECTY_PUSH_JS_DIR ?>",
    dialogControl: {
      title: "<?php echo $dialog_title ?>",
      submit: "<?php echo $dialog_submit ?>",
      cancel: "<?php echo $dialog_cancel ?>"
    },
    settingsControl: {
      title: "<?php echo $settings_title ?>",
      subscribed: "<?php echo $settings_subscribed ?>"
    },
    siteUrl: "<?php echo get_home_url() ?>",
    serverUrl: "<?php echo PERFECTY_PUSH_SERVER_URL ?>",
    vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY ?>",
    nonce: "<?php echo $nonce ?>"
  }
</script>