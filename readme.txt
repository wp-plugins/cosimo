=== Cosimo - Change Of Scene Image Many Often ===
Contributors: grobator
Tags: background, backgrounds, images, CSS
Donate link: http://donate.grobator.de/
Requires at least: 2.7
Tested up to: 3.0.3
Stable tag: 0.3

Change the background image of the BODY-Tag. A pool of images from Media Library and / or a NextGEN gallery can be used.

== Description ==
Cosimo is the acronym for "Change Of Scene Image Many Often". Static background images are boring very quickly. Cosimo wants to avoid this.
Users of [NextGEN gallery](http://wordpress.org/extend/plugins/nextgen-gallery/) can prepare a gallery of wallpapers and use them on Cosimo settings.
A string in the glob-style format, for example, &#42;69&#42;Summer, Spring?-Break&#42;, etc. can also select images from the media library.
The change interval may following a number of page views or time (minutes, hours, days, weeks, months, years done).
The background image is used on the site about inline CSS, as in this example:
>body {background-image:url(http://...../wp-content/uploads/bg-superduper.jpg) !important;}

== Installation ==
1. Upload cosimo folder into /wp-content/plugins/ directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==
1. Cosimo settings

== Changelog ==
Explanation:

* FEA = Implemented feature
* BUG = Resolved bug
* OPT = Optimization
* CLN = Cleanup/Refactoring
* OTH = Other

= 0.3 =
* OPT: Code maintenance for WP 3.x

= 0.2 =
* OPT: Image Filter apply NextGENs exclude attribute, also

= 0.1 =
* OTH: Initial version

== Upgrade notice ==
The initial version need no upgrade

== Other notes ==
Cache tools such as [WP Super Cache](http://wordpress.org/extend/plugins/wp-super-cache/) can cause additional delays, especially in the range of hours. If necessary, the plugin could be expanded to also change the Header image. User feedback is welcome.

== Frequently Asked Questions ==
No questions.
