=== Perfecty Push Notifications ===
Contributors: rwngallego, mociofiletto
Donate link: https://github.com/rwngallego
Tags: Push Notifications, Web Push Notifications, Notifications, User engagement
Requires at least: 5.0
Tested up to: 5.7
Stable tag: 1.0.7
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
- PWA & AMP Friendly.
- Free and Easy.
- Send Push Notifications on posts publishing.
- Send custom Push Notifications: you can easily change the icon, the image or the URL to open.
- See the stats in the Dashboard.
- Customizable public widget.
- The user authorization tokens stay in your server when they subscribe to receive your Push Notifications.
- Easily comply with GDPR: all the Push Notifications information is processed and stored in your server.
- Open Source Push Notifications: no hidden fees and you own your data.
- Offline browser Push Notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API).

Requirements:
- `gmp` extension for message encryption (optional)

## Documentation

[https://github.com/rwngallego/perfecty-push-wp/wiki](https://github.com/rwngallego/perfecty-push-wp/wiki)

## Code

Want to check the code? [https://github.com/rwngallego/perfecty-push-wp](https://github.com/rwngallego/perfecty-push-wp)

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

Use this option carefully (specially if you have a PWA website). It will deregister all the existing service workers from the root scope '/'.

= Why do I need the `gmp` extension? =

Sending push notifications involves encryption and `gmp` (GNU Multiple Precision) brings the best performance for such operations.

The `gmp` extension is optional in PHP >= 7.3. In PHP 7.2 you can't generate the VAPID keys without it, however you can still generate them with `openssl`. In any case, it's recommended to use `gmp` for better performance.

= How do I install the `gmp` extension? =

It depends on the operating system, but in theory you install it as a regular PHP extension. More information: [Install the gmp extension](https://github.com/rwngallego/perfecty-push-wp/wiki/Troubleshooting#install-the-gmp-extension)

= Is this working in production? =

This plugin has been deployed in a real site with more than 800.000 monthly visits and around 3.000 Push Notifications subscribers (~90.000 notifications/month), however we recommend you to test it before deploying it to a production environment.

= How do I report a bug? =

You can create an issue in our Github repo:
[https://github.com/rwngallego/perfecty-push-wp/issues](https://github.com/rwngallego/perfecty-push-wp/issues)

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
