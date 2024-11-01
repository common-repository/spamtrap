=== SpamTrap ===
Contributors: mythemes
Tags: spam, anti-spam, antispam, blacklist, bot, spam-bot, signin, signup, comment, spamtrap, trap
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 0.3.3
License: GPLv2 or later

This plugin will make your blog more secure by adding rules which spambots will not pass.

== Description ==

Spamtrap adds simple rules that help catch and block spambots on:

*    wp-login
*    wp-register
*    wp-comments

Rules that the client must pass for a successfull action:

*    test if the client performed a GET on resource page
*    test if the client run the javascript code


= Note =

This plugin scatters invisible links to spam traps email addresses throughout your wordpress blog to help collect and catch spam.

Install this plugin to help contribute to the project and catch spammers by hiding links to honey pots (spam traps) in your blog. The links are never visible to human visitors, but the spambots and crawlers follow them straight into the traps.

Details about spamtraps can be found at http://en.wikipedia.org/wiki/Spamtrap


== Screenshots ==

1. screenshot.jpg


== Installation ==

1. Upload the entire `spamtrap` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

You will find 'Spamtrap' menu in your WordPress admin panel.


== Frequently Asked Questions ==

waiting for questions


== Changelog ==

= 0.3.2 : 25.03.2014 =
* wp-register : check if client run javascript code

= 0.3.1 =
* wp-login : check if client run javascript code

= 0.2.3 =
* more random in email addresses
* changed main domain 

= 0.2.2 =
* Generated email addresses contain host's hash and a timestamp

= 0.2.0 =
* Posibility to link to spamtrap

= 0.1.1 =
* Changed format of generated email addresses

= 0.1.0 =
* First release


== Upgrade Notice ==

Install and test this plugin


