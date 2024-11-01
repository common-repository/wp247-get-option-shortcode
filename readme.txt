=== Plugin Name ===
Contributors: wescleveland
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RM26LBV2K6NAU
Tags: options, shortcode, get_option
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Include WordPress options anywhere shortcodes are accepted.

== Description ==

Include WordPress options anywhere shortcodes are accepted.

= Syntax =

[wp247_get_option option="**desired-option**" default="**desired-default**" scope="**desired-scope**"]

where:

- **desired-option** is the option to be retrieved from the WordPress wp_options table. Default: none

- **desired-default** is the default value to be returned if the desired option does not exist. Default: ""

- **desired-scope** indicates which type of option is to be retrieved. **scope="site"** will retrieve options using the WordPress **get_site_option** function. All other values are ignored and the WordPress **get_option** function will be used to retrieve the desired **option** value. Default: ""

= Examples =

Include the WordPress site URL in some text somewhere:

- This is my site's URL: [wp247_get_option option="siteurl"].

Set up a copyright notice in a footer widget:

- Copyright &copy; <a href="[wp247_get_option option='siteurl']">[wp247_get_option option='blogname']</a>. All rights reserved.

== Installation ==

In the site's WordPress backend:

- Go to Plugins->Add New
- Search for the plugin 'wp247 get option shortcode'
- Click the "Install" button
- Click on "Activate"

That's it. You're now ready to include WordPress options on your site.

== Screenshots ==

== Changelog ==

= 1.2 =
Removed Notifications Plugin corequisite
Fix Settings API for PHP v7
Update Admin Favorites

= 1.1 =
fixed settings API bug
Added WP247 Extension Notification System interface

= 1.0.1 =
Remove namespace use in WP247 Settings API to due errors

= 1.0 =
First release on 2015-April-9