=== Perfecty Push Notifications ===
Contributors: rwngallego
Donate link: https://github.com/rwngallego
Tags: push notifications, push, notifications, user engage
Requires at least: 5.2
Tested up to: 5.6
Stable tag: 1.0.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Self-hosted Push Notifications from your server for free!

== Description ==
Send web Push Notifications directly from your server: No hidden fees, no third-party dependencies and you own your data.

Requirements:
- PHP >= 7.2
- `gmp` extension for message encryption (optional)

In a rush looking for alternatives now that you've exceeded the free-tier of your current provider? Loosing your users every time you change provider? Worried about where the information of your users is stored? **With this plugin you don't worry about that**.

Features:

- **Open Source**, send Push Notifications **from your server for free!**
- No third-party dependencies
- Migrate users from other push services like OneSignal
- Send push notifications on posts publishing
- Customize the public widget
- The user authorization tokens stay in your server
- Offline browser notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API) (Safari is not supported yet)

Want to check the code? [https://github.com/rwngallego/perfecty-push-wp](https://github.com/rwngallego/perfecty-push-wp)

This plugin uses the [Chart.js](https://www.chartjs.org/) library for the admin stats.

== Installation ==

## Perfecty Push installation

1. Download the plugin

2. Install it and activate it

3. Go to the `Perfecty Push > Settings` section and enable the public widget

4. You're ready to start sending notifications!

Note: Check the FAQ if you miss the `gmp` extension.

== Frequently Asked Questions ==

= I am using a third-party Push provider, can I migrate my users to my server? =

Absolutely, Perfecty Push can override the previous service worker from your user's browser. Once they visit your site, the worker is automatically replaced. For that you need to first remove your provider's JS SDK, and then enable the `Remove conflicting workers` option in Perfecty Push. Go to Perfecty Push > Settings > Public Widget.

Use this option carefully (specially if you have a PWA website). It will deregister all the existing service workers from the root scope '/'.

= Why do I need the `gmp` extension? =

Sending push notifications involves encryption and `gmp` (GNU Multiple Precision) brings the best performance for such operations.

The `gmp` extension is optional in PHP >= 7.3. In PHP 7.2 you can't generate the VAPID keys without it, however you can still generate them with `openssl`. In any case, it's recommended to use `gmp` for better performance.

= How do I install the `gmp` extension? =

It depends on the operating system, but in theory you install it as a regular PHP extension. More information: [Install the gmp extension](https://github.com/rwngallego/perfecty-push-wp/wiki/Troubleshooting#install-the-gmp-extension)

= The VAPID keys are missing in Perfecty Push, you need to generate them. =

This is due to two reasons:
- You use PHP 7.2 and you didn't have the `gmp` extension enabled, and now you have it.
- You have deleted the VAPID keys from the Settings.

In any case, you can generate them by deactivating/activating your plugin from the WordPress plugin UI.

= Is this tested in production? =

This plugin has been deployed in a real site with more than 800.000 monthly visits, however we recommend you to test it before deploying it to a production environment.

= How do I report a bug? =

You can create an issue in our Github repo:
[https://github.com/rwngallego/perfecty-push-wp/issues](https://github.com/rwngallego/perfecty-push-wp/issues)

== Screenshots ==

1. Dashboard and stats
2. Send notification
3. Public widget (subscribe prompt)
4. Public widget (opt-out)
5. Send notification on post publishing
6. Notifications admin
7. Users admin
8. Settings

== Changelog ==

= 1.0 =
* First version of the plugin with basic functionality

== Upgrade Notice ==

= 1.0 =
First version
