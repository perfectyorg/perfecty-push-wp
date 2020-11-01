<?php

use Minishlink\WebPush\VAPID;

/***
 * Perfecty Push server
 */
class Perfecty_Push_Lib_Push_Server {
  
  public static function create_vapid_keys() {
    return VAPID::createVapidKeys();
  }
}
