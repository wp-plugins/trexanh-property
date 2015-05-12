=== TreXanh Property ===
Contributors: trexanhlab
Donate link: http://trexanhlab.com/wp/trexanh-property
Tags:  property, real estate, property portal, real estate portal, listings, property listings, property management, realtor, wp-property, wordpress property, wp property, wp-realestate, wordpress real estate, wp real estate, submit property, paid listing, payment, paypal
Requires at least: 4.1.0
Tested up to: 4.2
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

TreXanh Property plugin - a clean, neat and easy to use real estate solution for wordpress

== Description ==

TreXanh Property is a clean, neat and easy to use real estate solution. It help you quickly add property to your site or allow user to submit property to your site for free or with a fee. 

>[Plugin Homepage](http://trexanhlab.com/wp/trexanh-property/ "TreXanh Property Homepage") | [Demo](http://trexanhlab.com/wp/trexanh-property/demo/twentyfifteen/ "Demo")

* It's quick to add property from wordpress admin or using wordpress import feature. Each property will have a lot of custom fields, map, gallery image.
* Allow user to submit property for free or with a fee. User will pay through paypal. More payment gateways are being added. Submitted properties can display on site right away or need admin's approval
* Compatible with almost every theme. Please check list of demo themes here at http://trexanhlab.com/wp/trexanh-property
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

* Please wait our 0.2 version.

== Frequently Asked Questions ==

* Please wait our 0.2 version.

== Upgrade Notice ==

* Please wait our 0.2 version.