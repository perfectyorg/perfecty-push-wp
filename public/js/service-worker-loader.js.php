<?php
/**
 * This loader is needed in order to set the scope of the service worker as "/",
 * otherwise we would be limited to the plugin url location
 */
header( 'Service-Worker-Allowed: /' );
header( 'Content-Type: application/javascript' );
header( 'X-Robots-Tag: none' );
?>
importScripts('service-worker.js');
