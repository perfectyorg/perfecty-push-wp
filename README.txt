=== Plugin Name ===
Contributors: rwngallego
Donate link: https://github.com/rwngallego
Tags: web push, push api, push notifications
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sef-hosted, open source and powerful Web Push Notifications engine to send thousands of notifications from your own server for free!

== Description ==

Perfecty Push is an open source project that allows you to send thousands of push notifications
directly from your own server. It also scales well, you can have a dedicated instance to serve the notifications
or you can use a third party provider.

Advantages:

- For free
- Open source and self hosted
- No third party dependencies when self hosted
- Supports [Web Push Notifications](https://developers.google.com/web/fundamentals/push-notifications)
  using [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API)
- You retain the user authorization tokens, you own them
- Send thousands of notifications concurrently
- Support offline browser notifications
- Multiple installation options: self-hosted or dedicated instance

== Installation ==

## Perfecty Push Server installation

You have multiple choices, feel free to use the one that best suite you:

### Self hosted in the same server

You need to install perfecty-push server in the same server as your wordpress site runs:

1. Download the Push Server executable

2. Make it executable

3. Run it

The plugin will automatically detect the push server.

### Self hosted as a separate server

You can have a dedicated instance of the Perfecty Push server and connect it using the plugin.

1. Install the Push server in a separate machine

2. Ensure you have internal connectivity

3. Configure the plugin

The main advantage of this setup is that your website and push server don't share the same
computer resources. When your website receives high traffic you would prefer to
have a consistent page load even when you send thousands of notifications at the same time.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* First version of the plugin with basic functionality

== Upgrade Notice ==

= 1.0 =
No comments