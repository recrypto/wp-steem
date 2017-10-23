=== WordPress Steem ===
Contributors: recrypto
Donate link: https://steemit.com/@recrypto/
Tags: wordpress, wp-steem, steem, steemit
Requires at least: 4.1
Tested up to: 4.8.2
Stable tag: 1.0.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WordPress Steem lets you publish your WordPress posts on Steem blockchain.

== Description ==

WordPress Steem lets you publish your WordPress posts on Steem blockchain.

= Features =
- Automatically converts your post content into Markdown format
- Automatically render WordPress shortcodes to Steem post (Assuming you are using the default WordPress editor NOT the Markdown editor)
- Publish your newly created WordPress post to the Steem blockchain
- Publish your old WordPress post to the Steem blockchain
- Update your published Steem post if you have used this plugin to publish that Steem post
- Set post reward options such as Power Up (100%), Default (50% / 50%), and Decline Payout
- Set custom post permalink for your Steem post
- Set post tags for your Steem post
- Easy to use User Interface

= What is Steem? =
[Steem](https://steem.io/) is a blockchain-based social media platform where anyone can earn rewards. An example platform built on top of Steem block chain is [Steemit](steemit.com/).

[youtube https://www.youtube.com/watch?v=xZmpCAqD7hs]

= What is Cryptocurrency? =
A cryptocurrency (or crypto currency) is a digital asset designed to work as a medium of exchange using cryptography to secure the transactions and to control the creation of additional units of the currency. [Wikipedia](https://en.wikipedia.org/wiki/Cryptocurrency)

= Note =
You will require your Steem _PRIVATE POSTING KEY_ for this plugin to work. Your _PRIVATE POSTING KEY_ is <strong>NOT</strong> stored on our servers.

The plugin will automatically get a 15% curation reward from the Steem post created with this plugin for the use of development and maintenance cost with the beneficiary going to @steemful. (This is the brand I created for my projects related to Steem blockchain)

= Limitations =
- No support yet for scheduled posts but it will be supported on upcoming releases.
- Conversion of post content to Markdown may be off sometimes.

= Support =
Please support me by following me on Steem [@recrypto](https://steemit.com/@recrypto) or if you feel like donating, that would really help a lot on my future Steem developments around WordPress ecosystem. :)


== Installation ==

= Minimum Requirements =

* PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of WordPress Steem, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "WordPress Steem" and click Search Plugins. Once you've found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our eCommerce plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Where can I get support or talk to other users? =

If you get stuck, you can ask for help in the [WordPress Steem Plugin Forum](https://wordpress.org/support/plugin/wp-steem).

= Where can I report bugs or contribute to the project? =

Bugs can be reported either in our support forum or preferably on the [WordPress Steem GitHub repository](https://github.com/recrypto/wp-steem/issues).

= How can I contribute? =

Yes you can! Join in on our [GitHub repository](https://github.com/recrypto/wp-steem/) :)


== Screenshots ==

1. Tweaking the Steem Settings (Backend)
2. WordPress - Add New Post - With an option to publish on Steem blockchain (Backend)
3. WordPress - Add New Post - With an option to completely create content for the Steem post to be published on Steem blockchain (Backend)
4. WordPress - Edit Post - With an option to update a Steem post already published on Steem blockchain (Backend)


== Changelog ==

= 1.0.0 - 2017-07-07 =
* Initial version in WordPress Plugin Repository

= 1.0.1 - 2017-07-11 =
* [NEW] Shortcodes should now be automatically rendered when posting to the Steem blockchain
* [NEW] An insightful message if you haven't set the settings for WordPress Steem
* [FIX] "parent_permalink" and "permalink" containing "_" character
* [ENHANCEMENT] Overall plugin performance

= 1.0.2 - 2017-07-21 =
* [FIX] wp_steem_is_setup() function
* [NEW] Default Publish to Steem field in Steem Settings
* [NEW] Default Update to Steem	field in Steem Settings
* [NEW] Default Tags field in Steem Settings
* [NEW] Post Types field in Steem Settings
* [ENHANCEMENT] Intuitive messages that guides the user

= 1.0.3 - 2017-08-04 =
* [ENHANCEMENT] Hide "Update on Steem blockchain" checkbox input if the Steem post reaches the 7 days allowed time frame to be editable.
* [ENHANCEMENT] Doing an update on Steem blockchain action will not trigger if the Steem post reaches its 7 days allowed time frame to be editable.
* [FIX] Corrected the wording for cooldown notice. There is no 5 minute cooldown for doing a Steem post edit.
* [ENHANCEMENT] Preparing all the wordings for foreign translations in the future version of the plugin by wrapping them with WordPress locale functions.
* [ENHANCEMENT] Added an intuitve message if trying to publish a WordPress Post on Steem blockchain when its still in the 5 minute cooldown interval.

= 1.0.4 - 2017-10-06 =
* [FIX] It should not publish post if their post_status is "draft".

= 1.0.5 - 2017-10-23 =
* [FIX] Wrong grammar on message notices.
* [FIX] If the content for Markdown contains "%" symbol
* [NEW] Steemd (steemd.com) service under the "Platforms" list of links to check a Steem post.
* [NEW] Introduce WP_Steem_Helper to encapsulate all helper functions (Older helper functions will be deprecated with it)
* [ENHANCEMENT] Major overhaul on UI/UX
* [ENHANCEMENT] Documented each functionality
* [ENHANCEMENT] Steem tags should not be editable after 7 days
* [ENHANCEMENT] Saving Steem post data such as Rewards, Permalink, Tags, and other fields without being published is possible.
* [ENHANCEMENT] WordPress Steem API
* [ENHANCEMENT] Improve overall functionality

== Upgrade Notice ==

= 1.0.5 - 2017-10-23 =
* [FIX] Wrong grammar on message notices.
* [FIX] If the content for Markdown contains "%" symbol
* [NEW] Steemd (steemd.com) service under the "Platforms" list of links to check a Steem post.
* [NEW] Introduce WP_Steem_Helper to encapsulate all helper functions (Older helper functions will be deprecated with it)
* [ENHANCEMENT] Major overhaul on UI/UX
* [ENHANCEMENT] Documented each functionality
* [ENHANCEMENT] Steem tags should not be editable after 7 days
* [ENHANCEMENT] Saving Steem post data such as Rewards, Permalink, Tags, and other fields without being published is possible.
* [ENHANCEMENT] WordPress Steem API
* [ENHANCEMENT] Improve overall functionality