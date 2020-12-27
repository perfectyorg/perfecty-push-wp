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
- PHP 7.2
- `gmp` extension for message encryption

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

This plugin uses the [Chart.js](https://www.chartjs.org/) library for the admin stats charts.

== Installation ==

## Perfecty Push installation

1. Download the plugin

2. Install it and activate it

3. Go to the `Perfecty Push > Settings` section and enable the public widget

4. You're ready to start sending notifications!

Note: Check the FAQ if you miss the `gmp` extension.

== Frequently Asked Questions ==

= I was using a third-party Push provider, can I migrate my users to my server? =

Absolutely, Perfecty Push can override the previous service worker from your users browser. Once they visit your site, they it get automatically replaced. For that you need to the remove your provider's SDK and then enable the `Remove conflicting workers` option, in Perfecty Push > Settings > Public Widget.

Use this option carefully, otherwise it will deregister other existing service workers from the root scope `/` (specially if you have a PWA site).

= Why do I need the `gmp` extension? =

Sending push notifications involves encryption and `gmp` (GNU Multiple Precision) brings the best performance for such operations.

= How do I install the `gmp` extension? =

It depends on the operating system, but in theory you install it as a usual PHP extension:

In Ubuntu you would do:

```
sudo apt-get install php7.2-gmp

# reload your PHP server:
sudo service php-fpm restart # for nginx
sudo service apache2 restart # for apache
```

= The VAPID keys are missing in Perfecty Push, you need to regenerate them. =

If this is the first time you use the plugin, you regenerate them by deactivating/activating your plugin from WordPress.

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