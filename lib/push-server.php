<?php

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;

/***
 * Perfecty Push server
 */
class Perfecty_Push_Lib_Push_Server {
  
  /**
   * Creates the VAPI keys
   * 
   * @return array an array with the VAPID keys
   */
  public static function create_vapid_keys() {
    return VAPID::createVapidKeys();
  }

  /**
   * Send the notification to all the subscribers
   * 
   * @param $payload array|string Payload to be sent, json encoded or array
   * @return [$total, $succeded] | string Total/succeded messages or Error message
   */
  public static function send_notification($payload) {
    if (!PERFECTY_PUSH_VAPID_PUBLIC_KEY or !PERFECTY_PUSH_VAPID_PRIVATE_KEY) {
      return "No VAPID keys are configured";
    }
    if (!is_string($payload)) {
      $payload = json_encode($payload);
    }

    $auth = [
      'VAPID' => [
        'subject' => site_url(),
        'publicKey' => PERFECTY_PUSH_VAPID_PUBLIC_KEY,
        'privateKey' => PERFECTY_PUSH_VAPID_PRIVATE_KEY
      ]
    ];
    $webPush = new WebPush($auth);
    $webPush->setReuseVAPIDHeaders(true);

    $subscriptions = Perfecty_Push_Lib_Db::get_subscriptions();
    foreach ($subscriptions as $item){
      $push_subscription = new Subscription(
        $item->endpoint,
        $item->key_p256dh,
        $item->key_auth
      );

      $webPush->sendNotification($push_subscription, $payload);
    }

    $total = count($subscriptions);
    $succeded = 0;
    foreach ($webPush->flush() as $result) {
      if ($result->isSuccess()) {
        $succeded++;
      } else {
        error_log("Failed to send one notification, error: " . $result->getReason());
      }
    }
    return [$total, $succeded];
  }
}
