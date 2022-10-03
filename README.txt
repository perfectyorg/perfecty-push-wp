=== Perfecty Push Notifications ===
Contributors: rwngallego, mociofiletto
Donate link: https://paypal.me/tatalata777
Tags: Push Notifications, Web Push Notifications, Notifications, User engagement
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.6.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Push Notifications that are self-hosted, you don't need API keys to integrate with external Push Notifications providers that will charge you later. It's Open Source and the information is stored in WordPress, so that you can send Push Notifications directly from your server for free!

In a rush looking for Push Notifications alternatives now that you've exceeded the free-tier of your current provider? Loosing your users every time you change your Push Notifications provider? Worried about where the Push Notifications information is stored? **With this plugin you don't worry about that**.

Easily migrate your users from other providers, send custom Push Notifications, or automatically when publishing a post, and see the stats in your Dashboard.

Features:

- Self-hosted: total control of your information, and no need of third-party integrations.
- Migrate users from other Push Notifications providers like OneSignal.
- PWA Friendly (Tested with Super PWA) and AMP plugins (Transitional mode).
- Send Push Notifications on posts publishing. Also supports custom posts. You can use the feature image of the post or customize the title.
- Send custom Push Notifications: you can easily change the icon, the image or the URL to open.
- See the stats in the Dashboard.
- Customizable public widget.
- The user authorization tokens stay in your server when they subscribe to receive your Push Notifications.
- Easily comply with GDPR: all the Push Notifications information is processed and stored in your server.
- Open Source: no hidden fees, and open transparency.
- Offline browser Push Notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API). iOS/Safari are not supported yet.

Requirements:
- `gmp` extension for message encryption (optional)

## Documentation

[https://docs.perfecty.org/](https://docs.perfecty.org/)

## Code

Want to check the code? [https://github.com/perfectyorg/perfecty-push-wp](https://github.com/perfectyorg/perfecty-push-wp)

This plugin uses the [Chart.js](https://www.chartjs.org/) library for the admin stats.

== Installation ==

## Perfecty Push installation

1. Download the plugin

2. Install it and activate it

3. Go to the `Perfecty Push Notifications > Dashboard` section and start sending Push Notifications

Note: Check the FAQ if you miss the `gmp` extension.

== Frequently Asked Questions ==

= I am using a third-party Push Notifications provider, can I migrate my users to my server? =

Absolutely, Perfecty Push can override the previous service worker from your user's browser. Once they visit your site, the worker is automatically replaced and you can start sending Push Notifications directly. For that you need to first remove your provider's JS SDK, and then enable the `Remove conflicting workers` option in Perfecty Push. Go to Perfecty Push > Settings > Public Widget.

After v1.0.7 Perfecty Push uses the `/perfecty/push` scope (you can also customize it), so it's friendly with any PWA/AMP plugin that uses the root scope (e.g. Super PWA plugin). However, if you use the `Remove conflicting workers` option, it will deregister any existing worker, so be careful with this option.

= Why do I need the `gmp` extension? =

Sending push notifications involves encryption and `gmp` (GNU Multiple Precision) brings the best performance for such operations.

The `gmp` extension is optional in PHP >= 7.3. In PHP 7.2 you can't generate the VAPID keys without it, however you can still generate them with `openssl`. In any case, it's recommended to use `gmp` for better performance.

= How do I install the `gmp` extension? =

It depends on the operating system, but in theory you install it as a regular PHP extension. More information: [Install the gmp extension](https://github.com/perfectyorg/perfecty-push-wp/wiki/Troubleshooting#install-the-gmp-extension)

= Is this working in production? =

This plugin has been deployed in a real site with more than 800.000 monthly visits and around 8.000 Push Notifications subscribers (~240.000 notifications/month), however we recommend you to test it before deploying it to a production environment.

= How do I report a bug? =

You can create an issue in our Github repo:
[https://github.com/perfectyorg/perfecty-push-wp/issues](https://github.com/perfectyorg/perfecty-push-wp/issues)

== Screenshots ==

1. Dashboard and Push Notifications stats
2. Send a new Push Notification
3. Public widget (subscribe prompt)
4. Public widget (opt-out)
5. Send Push Notifications on post publishing
6. Notifications admin
7. Users admin
8. Settings

== Changelog ==

= 1.6.2 =
* Tested up to WordPress 6.0

= 1.6.1 =
* Send Notification: Select image from gallery. Related [#150](https://github.com/perfectyorg/perfecty-push-wp/issues/150)

= 1.6.0 =
* Important: Default styles for the prompt are provided. If necessary, please override the corresponding CSS classes in your theme. Fixes [#135](https://github.com/perfectyorg/perfecty-push-wp/issues/135)
* Added support for WP 5.9 and beta support to PHP 8. Fixes [#136](https://github.com/perfectyorg/perfecty-push-wp/issues/136)
* Two new Settings added: Log level and Log driver. Fixes [#137](https://github.com/perfectyorg/perfecty-push-wp/issues/137)
* Logs auto-cleanup for the Database driver. Deletes the entries older than 10 days in chunks of 1000 rows. Fixes [#94](https://github.com/perfectyorg/perfecty-push-wp/issues/94)
* Improve multilingual support, use default values as placeholders in the Widget texts. Fixes [143](https://github.com/perfectyorg/perfecty-push-wp/issues/143)
* Upgraded the `web-push` lib to `6.0.7`

= 1.5.1 =
* Remove unnecessary autoload, related to [#111](https://github.com/perfectyorg/perfecty-push-wp/issues/111).

= 1.5.0 =
* Send automatic notifications for Custom Post Types (public). Related [#103](https://github.com/perfectyorg/perfecty-push-wp/issues/103)
* Use the external composer libraries only when necessary to avoid potential conflicts. Related [#111](https://github.com/perfectyorg/perfecty-push-wp/issues/111)
* Fix compatibility issues with the Otter Blocks plugin. Related [#125](https://github.com/perfectyorg/perfecty-push-wp/issues/125)
* Use WordPress defined timezone in Notifications and Users view. Related [#127](https://github.com/perfectyorg/perfecty-push-wp/issues/127)

= 1.4.2 =
* Set the default log level to ERROR. Related [#117](https://github.com/perfectyorg/perfecty-push-wp/issues/117)
* Move the cron job check warning to the logger. Related [#110](https://github.com/perfectyorg/perfecty-push-wp/issues/110)

= 1.4.1 =
* Improvements to the job management section and the job stats. Related [#104](https://github.com/perfectyorg/perfecty-push-wp/issues/104), [#102](https://github.com/perfectyorg/perfecty-push-wp/issues/102)

= 1.4.0 =
* Performance improvement, send ~10.000 notifications/minute in a 2 GB RAM/1vCPU server. Related: [#81](https://github.com/perfectyorg/perfecty-push-wp/issues/81) and [#86](https://github.com/perfectyorg/perfecty-push-wp/issues/86)
* Parallel flushing size parameter was added with a default value of 50 notifications.
* Custom welcome message that can be enabled/disabled. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/). Related [#91](https://github.com/perfectyorg/perfecty-push-wp/issues/91)
* Added the 'perfecty-push' suffix to the `server_url` option.

= 1.3.3 =
* Unleashing mechanism for stalled notification jobs. Fixes [#86](https://github.com/perfectyorg/perfecty-push-wp/issues/86)
* Send logs to error_log() by default when logging is not even enabled. Fixes [#85](https://github.com/perfectyorg/perfecty-push-wp/issues/85)
* Tested up to WordPress 5.8

= 1.3.2 =
* Add the plugin links shown in the WordPress Plugin installer
* Icon max width in the Notification details.

= 1.3.1 =
* Use the already defined site icon before `v1.3.0`.

= 1.3.0 =
* Option to always send a Push Notification on Post publishing. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/). Fixes [#64](https://github.com/perfectyorg/perfecty-push-wp/issues/64)
* Google Analytics UTM suffix for Url to open. Fixes [#49](https://github.com/perfectyorg/perfecty-push-wp/issues/49)
* Send notification after subscribing. Fixes [#63](https://github.com/perfectyorg/perfecty-push-wp/issues/63)
* Remove conflicting Service Workers for known providers, and custom expression. Fixes [#76](https://github.com/perfectyorg/perfecty-push-wp/issues/76)
* Option to enable fixed notifications that don't fade out. Fixes [#66](https://github.com/perfectyorg/perfecty-push-wp/issues/66)
* Display prompt after a number of visits. Default: Immediately. Fixes [#70](https://github.com/perfectyorg/perfecty-push-wp/issues/70)
* Added hooks and filters for external integrations: `perfecty_push_broadcast_scheduled($payload)`, `perfecty_push_wp_user_notified($payload, $wp_user_id)` hooks and the `perfecty_push_custom_payload($payload)` filter.
* Default Icon from the Media Library [#68](https://github.com/perfectyorg/perfecty-push-wp/issues/68)
* Show icon in the Notifications prompt. Fixes [#71](https://github.com/perfectyorg/perfecty-push-wp/issues/71)
* Default dialog texts in Settings. Fixes [#69](https://github.com/perfectyorg/perfecty-push-wp/issues/69).
* Always send featured image on Post publishing. Fixes [#65](https://github.com/perfectyorg/perfecty-push-wp/issues/65)

= 1.2.2 =
* Point to the correct JS SDK commit hash

= 1.2.1 =
* Fixes conflict with TimePicker and ChartJS [#62](https://github.com/perfectyorg/perfecty-push-wp/issues/62)
* Jquery `.on()` instead of `.load()` [67](https://github.com/perfectyorg/perfecty-push-wp/issues/67)

= 1.2.0 =
* Schedule notifications is now possible thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/). Fixes [#29](https://github.com/perfectyorg/perfecty-push-wp/issues/29)
* Support external plugin integrations. Fixes [#5](https://github.com/perfectyorg/perfecty-push-wp/issues/5)
* Push Subscribers are linked with their WordPress User Id if they're logged in users
* Push Server logs (DB Driver initially). Fixes [#30](https://github.com/perfectyorg/perfecty-push-wp/issues/30) and [#31](https://github.com/perfectyorg/perfecty-push-wp/issues/31)
* Remove the users that have opted-out. Fixes [#37](https://github.com/perfectyorg/perfecty-push-wp/issues/37)
* Add a cron monitor to check the notification jobs execution. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/). Fixes [#33](https://github.com/perfectyorg/perfecty-push-wp/issues/33)
* Option to don't show the Bell/Widgets when asking permissions. Solves [#48](https://github.com/perfectyorg/perfecty-push-wp/issues/48)

= 1.1.6 =
* Increased payload size to 2.000 characters to support arabic characters. Issue [#46](https://github.com/perfectyorg/perfecty-push-wp/issues/46)

= 1.1.5 =
* Upgrade url-parse to avoid CVE-2021-27515
* Styling fixes in the Bell control

= 1.1.4 =
* CSS style changes to the bell.
* Move icon to an svg tag in the HTML as suggested by @stkuhn.
* Open subscription dialog if the bell is clicked and the user is not subscribed.
* Fix bug in the Perfecty Push Service Worker detection. Issue [#42](https://github.com/perfectyorg/perfecty-push-wp/issues/42)

= 1.1.3 =
* IP address collection is disabled by default. It can be enabled using the "Enable and collect data from users" option in the Segmentation settings.
* Added option to hide the bell after the users have subscribed.

= 1.1.2 =
* Remove jQuery dependency from the public area
* Detect duplicate endpoint auth/private keys when subscribing

= 1.1.1 =
* Integrate the Perfecty Push JS SDK: This is backwards compatible but upgrade to this version with caution.
* Upgrade the PHP Push Server Lib to the latest version
* Performance improvements
* Debugging mode
* The server_url now defaults to get_rest_url(), so the preference value is reset to an empty value in order to use it
* Fix various issues

= 1.0.8 =
* Add options to send featured image and customize notification title in notifications sent on post publishing. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/)
* Removing wp-i18n variable definitions in JS from the global scope. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/)

= 1.0.7 =
* PWA and AMP Friendly (Tested with Super PWA and AMP for WP plugins)
* Support MySQL < 5.6 (max index key=767)
* Improving Internationalization
* Apache mod_security, Nginx default configuration and WAF friendly

= 1.0.6 =
* Added WordPress 5.7 support

= 1.0.5 =
* Implement internationalization. Thanks to [@mociofiletto](https://profiles.wordpress.org/mociofiletto/)
* Support older Wordpress versions

= 1.0 =
* First version of the plugin with basic functionality

== Upgrade Notice ==

= 1.0 =
First version
