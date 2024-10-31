=== Multisite Latest Posts Widget ===
Contributors: tristanmin
Donate link: http://www.wpclue.com/
Tags: widgets, latest, posts, sitewide, sidebar, plugin
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.4

Show the latest posts from all blogs in multisite Wordpress.

== Description ==

A widget plugin to show the latest posts from all blog in sidebar and content area in multisite enabled Wordpress sites.

Features

1. able to show latest posts in sidebar and content areas
2. choose list or div style (content area only)
3. support short code (content area only)
4. User can change the title of the widget as usual
5. User can limit the number of posts to show
6. Posts' stripped down content will be shown if posts' excerpt are empty

Note: Kindly please rate this plugin or vote the compatibility.  So I could review it and improve the quality of this plugin. Appreciate your help!

== Installation ==

1. Upload `multisite-latest-posts-widget` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Assign this widget to your theme's sidebar under Widgets menu
4. Set the title and number of posts (default limit is 5)

If you would like the latest posts to be shown in content area you could use below short code.

[mslp limit=10 style=list]

You can assign two styles
1. style=list
2. style=div

Note: default limit = 5
      default style = list

== Frequently Asked Questions ==

== Screenshots ==

1. Widget settings
2. Sample posts layout

== Changelog ==

= 1.4 =
* Checked on WordPress 3.2 multisite
* Removed check public blog or not from the query (In the WordPress privacy settings, there is a option called "I would like to block search engines, but allow normal visitors". It means no matter blog is public or not still showing the posts to the normal users. So time to remove the check public blog or not from the query)

= 1.3 =
* Added short code support and recent posts can be shown in content area

= 1.2 =
* Fixed wrong links to the posts

= 1.1 =
* Fixed not creating view table when the plugin is activated.

= 1.0 =
* The first release of the plugin.