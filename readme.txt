=== Phone Lookup ===
Contributors: hexio
Tags: WooCommerce, phoneLookup, checkout auto fill,
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 5.2.2
Stable tag: 1.0.3
License: GPLv2 or later

Phone Lookup, enables auto fill of checkout forms, with only entering phone number at checkout page.

== Description ==

WooCommerce extension for Danish webshops.

Phone Lookup, enables auto fill of checkout forms, with only entering phone number at checkout page.

The plugin is tested with 30+ different themes, and is build to adapt the themes styling to fit into the design of the theme.

=== Requirement ===

The plugin requires WooCommerce installed.

The plugin requires a token, which can be granted free for trails.

Contact [support@hexio.dk](mailto:support@hexio.dk) to receive your token or here more about Phone Lookup or our pricing.

=== External service ===

The plugin uses an external service provided by Hexio IVS, to look up information by phone number.

This plugin also uses a cookie called "plu_visitor_id", to track unique users visiting your shop.
The cookie is set on first checkout load of the user. It is used to track the number of visitors reaching your checkout.
When a user completes and order, the plugin will send their "plu_visitor_id", the order number and the phone number of the order to the external service.
This is done to provide statistics in the settings view of Phone Lookup, for example conversion rate from checkout to order, number of visitors and number of orders.

The main functionality of this plugin, also uses the external service, to look up address information by Phone number.
The phone number used in the lookup is stored, for statistics and billing purposes.

The service can be found here: [https://services.hexio.dk/phonelookup/](https://services.hexio.dk/phonelookup/)

== Screenshots ==
1. How PhoneLookUp works

== Installation ==

1. Activate plugin (requires WooCommerce)
2. If you don't have a token, contact [support@hexio.dk](mailto:support@hexio.dk) to start a trial or get pricing information
3. Insert token at plugin page

Now your checkout page has a Phone Lookup field, as the first field.

== Changelog ==

= 1.0.3 =
*Release Date - 03 September 2019*

* Fix no data error *
* Get time spent on checkout page *

= 1.0.2 =
*Release Date - 25 June 2019*

* Fix date error *
* Send version header to LookUp service *
* Don't trigger AB test if admin *
* Plugin clean up *
* Delete options *
* Update readme *

= 1.0.1 =
*Release Date - 17 June 2019*

* Approved by WordPress Review plugin team *
* Added A/B test *
* Fixed scroll issue *

= 1.0.0 =
*Release Date - 05 May 2019*

* Init - base of plugin
