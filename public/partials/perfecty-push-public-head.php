<?php
$path = plugin_dir_url(__DIR__) . "js/";
?>
<script language="javascript">
  window.PerfectyPushOptions = {
    path: "<?php echo $path; ?>",
    fabControl: {
      title: "¿Deseas recibir notificaciones?",
      submit: "Continuar",
      cancel: "Ahora no"
    }
  }
</script>