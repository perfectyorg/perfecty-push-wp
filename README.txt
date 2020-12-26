=== Perfecty Push Notifications ===
Contributors: rwngallego
Donate link: https://github.com/rwngallego
Tags: push notifications, push, notifications, user engage
Requires at least: 5.2
Tested up to: 5.6
Stable tag: 1.0.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Self-hosted Push Notifications from your server for free!

== Description ==
Send web Push Notifications directly from your own server: No hidden fees, no third-party
dependencies and you own your data. It has a self-hosted and built-in Push Server in PHP.

Features:

- Send push notifications when publishing posts
- Migrate users from other Push Services like OneSignal
- Open Source and self-hosted
- Custom texts for the public widget
- No third-party dependencies when self-hosted
- Offline browser notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API) (Safari is not supported yet)
- You retain and own the user authorization tokens
- Built-in PHP Push Server based on [web-push-php](https://github.com/web-push-libs/web-push-php)

Requirements:
- PHP 7.2
- The gmp extension.

**Note**: The `gmp` extension is optional and recommended only for better performance.

Github: [https://github.com/rwngallego/perfecty-push-wp](https://github.com/rwngallego/perfecty-push-wp)

This plugin uses the [Chart.js](https://www.chartjs.org/) library for the admin stats charts.

== Installation ==

## Perfecty Push installation

1. Download the plugin

2. Install it and activate it

3. Go to the `Perfecty Push > Settings` section and enable the public widget

4. You're ready to start sending notifications!

== Frequently Asked Questions ==

= Is this tested in production? =

This plugin has been deployed in a real site with more than 800.000 monthly visits,
however we recommend you to test it before deploying it to a production environment.

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