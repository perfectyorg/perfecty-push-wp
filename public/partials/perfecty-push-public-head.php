<?php
  $fab_title = isset($options['fab_title']) ? $options['fab_title'] : 'Do you want to receive notifications?';
  $fab_submit = isset($options['fab_submit']) ? $options['fab_submit'] : 'Continue';
  $fab_cancel = isset($options['fab_cancel']) ? $options['fab_cancel'] : 'Not now';
  $nonce = wp_create_nonce('wp_rest');
?>
<script language="javascript">
  window.PerfectyPushOptions = {
    path: "<?php echo PERFECTY_PUSH_JS_DIR ?>",
    fabControl: {
      title: "<?php echo $fab_title ?>",
      submit: "<?php echo $fab_submit ?>",
      cancel: "<?php echo $fab_cancel ?>"
    },
    siteUrl: "<?php echo get_home_url() ?>",
    serverUrl: "<?php echo PERFECTY_PUSH_SERVER_URL ?>",
    vapidPublicKey: "<?php echo PERFECTY_PUSH_VAPID_PUBLIC_KEY ?>",
    nonce: "<?php echo $nonce ?>"
  }
</script>