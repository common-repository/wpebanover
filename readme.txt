=== WPeBanOver ===
Contributors: etruel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7267TH4PT3GSW
Tags: banner over, ads, ads over, hover, mouseover, scroll, wordpress plugin, plugin, development, template
Requires at least: 2.9
Tested up to: 3.6
Stable tag: 1.1
License: GPLv3

Show a small banner and on mouse event (over, out, click, dblclick) show another big or 2nd banner anywhere in your template, post, page or widget.

== Description ==

Show a HTML code when you place the [WPeBanOver] shortcode, on mouse (over, out, click, dblclick) show another HTML code until mouseout, anywhere in your template, post, page or widget.

You can read, follow, donate and comment about this plugin at author page: [netmdp.com](http://www.netmdp.com)

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip "WPeBanOver" archive and put the folder into your plugins folder (/wp-content/plugins/).
1. Activate the plugin through the 'Plugins' menu in WordPress
1. For print place `<?php if ((function_exists('WPeBanOver'))) WPeBanOver(); ?>` in your templates or use shortcode [WPeBanOver] in widgets, post or pages
1. For get the html string use `<?php get_WPeBanOver(); ?>`
1. Also have the css class .WPeBanOver then you can add your style 

== Frequently Asked Questions ==

= Comming Soon =

== Screenshots ==

1. Admin settings page.
1. Showing on template a custom demo image.

== Changelog ==

= 1.1 =
Added plus change banner methods,click, MouseOut, Double Click.
Now second banner can remains until reload page.  
Added Fade IN/OUT option to change.
Added .pot file for localization
Fixed and upgraded spanish language.

= 1.0 =
First Release

== Upgrade Notice ==
1.1 Added plus change banner methods.  Added Fade option. 