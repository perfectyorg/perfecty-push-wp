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
   * Schedules an async notification to all the users
   * 
   * @param $payload array|string Payload to be sent, json encoded or array
   */
  public static function schedule_broadcast_async($payload) {
    if (!is_string($payload)) {
      $payload = json_encode($payload);
    }

    $options = get_option('perfecty_push');
    $use_action_scheduler = isset($options['use_action_scheduler']) ? esc_attr($options['use_action_scheduler']) : false;

    if (!is_plugin_active('action-scheduler') || $use_action_scheduler !== true) {
      // Schedule it using wp-cron
      $notification_id = Perfecty_Push_Lib_Db::create_notification($payload, Perfecty_Push_Lib_Db::NOTIFICATIONS_STATUS_SCHEDULED);
      if (!$notification_id) {
        return "Could not schedule the notification";
      }
      wp_schedule_single_event(time(), 'perfecty_push_broadcast_notification_event', array($notification_id, $payload, 0));
      return true;
    } else {
      // Execute using action scheduler: https://actionscheduler.org/usage/
      return true;
    }
  }

  /**
   * Execute one broadcast batch
   * 
   * @param $notification_id Notification id
   * 
   * @return bool Succeeded or failed
   */
  public static function execute_broadcast_batch($notification_id) {
    // if it has been taken and was not released, that means a wrong state
    $notification = Perfecty_Push_Lib_Db::get_notification($id);
    if ($notification->taken) {
      error_log('Halted, notification taken but not released. ' . print_r($notification, true));
      Perfecty_Push_Lib_Db::mark_failed($notification_id);
      return false;
    }

    // take the notification
    Perfecty_Push_Lib_Db::take($notification_id);

    // we process the next batch, starting from $cursor we take $batch_size elements
    $subscriptions = Perfecty_Push_Lib_Db::get_subscriptions($notification->cursor, $notification->batch_size);

    if (count($subscriptions) == 0) {
      // we are done
      $notification['status'] = 'completed';
      Perfecty_Push_Lib_Db::update_notification($notification);
      return true;
    }

    // we send one batch
    $result = self::send_notification($subscriptions, $notification->payload);

    if (is_array($result)) {
      [$total_batch, $succeded] = $result;
      $notification['cursor'] += $total_batch;
      $notification['succeded'] += $succeded;
      $notification['taken'] = 0;
      Perfecty_Push_Lib_Db::update_notification($notification);
    } else {
      error_log("Error executing one batch. $result. " . print_r($notification, true));
    }

    // execute the next batch
    wp_schedule_single_event(time(), 'perfecty_push_broadcast_notification_event', array($notification_id));
    return true;
  }

  /**
   * Send the notification to all the subscribers
   * 
   * @param $subscriptions array List of subscriptions
   * @param $payload array|string Payload to be sent, json encoded or array
   * @return [$total, $succeded] | string Total/succeded messages or Error message
   */
  public static function send_notification($subscriptions, $payload) {
    if (!PERFECTY_PUSH_VAPID_PUBLIC_KEY or !PERFECTY_PUSH_VAPID_PRIVATE_KEY) {
      error_log("No VAPID Keys were configured");
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
