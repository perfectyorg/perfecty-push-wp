<?php

/**
 * Prints the about page
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin/partials
 */
?>
<div class="wrap">
    <h1>About Perfecty Push</h1>
    <p><a href="https://perfecty.co/push-server" target="_blank">Perfecty Push</a>
    is an Open Source project that allows you to send push notifications
    directly from your own server. No hidden fees, no third-party dependencies and you
    own your data.</p>
    <h2>Advantages</h2>
    <p>
    <ul class="perfecty-items-list">
        <li>Open Source and self-hosted</li>
        <li>No third party dependencies when self-hosted </li>
        <li>Offline browser notifications through <a href="https://developer.mozilla.org/en-US/docs/Web/API/Push_API">Push API</a> (Safari is not supported yet)</li>
        <li>You retain and own the user authorization tokens</li>
    </ul>
    </p>
    <h2>Requirements</h2>
    <p>
    <ul class="perfecty-items-list">
        <li>PHP 7.1+</li>
        <li>The gmp extension</li>
    </ul>
    </p>
    <p>
    Note: In the roadmap we plan to support to PHP 7.2+, so the gmp extension will be optional
    as the PHP 7.2+ improvements are sufficient.
    </p>
</div>
