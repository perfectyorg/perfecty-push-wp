<?php
header("Service-Worker-Allowed: /");
header("Content-Type: application/javascript");
header("X-Robots-Tag: none");
?>

console.log("Hello from the service worker!");