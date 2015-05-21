=== TreXanh Property ===
Contributors: trexanhlab
Donate link: http://trexanhproperty.com
Tags:  property, real estate, property portal, real estate portal, listings, property listings, property management, realtor, wp-property, wordpress property, wp property, wp-realestate, wordpress real estate, wp real estate, submit property, paid listing, payment, paypal
Requires at least: 4.1.0
Tested up to: 4.2.2
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

TreXanh Property plugin - A clean, powerful and easy to use real estate solution on Wordpress

== Description ==

TreXanh Property is a clean, powerful and easy to use real estate solution on Wordpress. It help you quickly add property to your site or allow user to submit property to your site for free or with a fee. 

>[Demo 1](http://trexanhproperty.com/demo/twentyfifteen/ "Demo with Twenty Fifteen theme") | [Demo 2](http://trexanhproperty.com/demo/estato/ "Demo with Custom theme") | [Docs](http://trexanhproperty.com/doc/ "Documentation, User guide") | [Plugin Homepage](http://trexanhproperty.com/ "TreXanh Property Homepage")

* It's quick to add property from wordpress admin or using wordpress import feature. Each property will have a lot of custom fields, map, gallery image.
* Allow user to submit property for free or with a fee. User will pay through paypal, stripe. More payment gateways are being added. Submitted properties can display on site right away or need admin's approval
* Compatible with almost every theme. Please check list of demo themes here at [trexanhproperty.com](http://trexanhproperty.com/ "trexanhproperty.com")
* Also included: Search property widget, property listing shortcode with filter

= Shortcodes =

Shortcodes allow to insert your properties into posts and pages. The [txp_properties_listing] shortcode quickly outputs properties.

Example of usage:

* Featured properties: [txp_properties_listing featured="yes" limit=4]
* Latest properties: [txp_properties_listing orderby="time" order="descending" limit=4] or just [txp_properties_listing limit=4]
* Specific properties by id: [txp_properties_listing ids="1,10,100"]

== Installation ==

= Minimum Requirements =

* PHP version 5.3.28 or greater
* MySQL version 5.0 or greater

= Automatic installation =

* Try steps at https://codex.wordpress.org/Managing_Plugins#Automatic_Plugin_Installation
* During search plugin, try "TreXanh-Property"

= Common setup task =

* Add search widget to sidebar. Add property listing shortcode to pages.
* At activation, plugin will create pages for property submission process at urls bellow, make sure you create menu items for them:
    * http://yourwordpress.com/submit-property : for user to submit property
    * http://yourwordpress.com/my-properties : for user to view submitted property
* Go to Admin > TreXanh Property > Setting : to enable paid listing, paid listing fee, also paypal config

= Dummy data =

1. Locate sample-data\sample-property.xml in plugin directory
1. Use wordpress's importer ( Wordpress admin > Tool > Import) to import file above


== Screenshots ==

1. **Frontend** > Search property, Property listing
2. **Frontend** > Single property
3. **Frontend** > Property submit
4. **Frontend** > Property submit payment
5. **Backend** > Property add form
6. **Backend** > Submit property setting

== Changelog ==

= 0.2 =

* Feature - Allowed admin disabled property submission.
* Feature - Support Stripe Payment Gateway
* Fix - Some bugs.

== Frequently Asked Questions ==

* Not yet

== Upgrade Notice ==

= 0.2 - 20 May 2015 =
* Feature - Add stripe payment gateway.
* Fix - Empty search criteria summary in search result
* Fix - Migrate jquery.prettyPhoto.js library to 3.1.6 TO fix bug XSS vulnerability http://www.perucrack.net/2014/07/haciendo-un-xss-en-plugin-prettyphoto.html