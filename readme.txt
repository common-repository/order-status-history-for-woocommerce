=== Order Status History for WooCommerce ===

Contributors: alx359
Donate link: https://www.paypal.com/donate/?hosted_button_id=Z44PBY7ARUPRY
Tags: woocommerce history, past orders, woocommerce order history, woocommerce orders, order status 
Tested up to: 6.4.2
Requires at least: 5.0
Requires PHP: 7.0
Stable tag: 2.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Speed up your daily processing of orders by getting to know more about who's ordering. Themed order status color swatches, Reports, CSV, free.

== Description ==

*Order Status History for WooCommerce* (OSHWOO) speeds up your daily analysis and processing of orders. Unobtrusive visual cues in all the orders screens show when someone has ordered from your shop before, your (most) repeating customers, and spurts of unusual activity (like  customers with due payments and cancellations).

= Main features =

* A graphical add-on for WooCommerce. Simply install and you're ready to go
* For each Order in the *Orders* table, get additional data displayed, like: has a customer ordered before, repeatedness, and unusual behavior (e.g. due payments and cancellations)
* The newly added *Order history column* is also sortable. The Shop Manager now can easily discern their most repeating customers, or those with the most issues, for example
* Shows Order history statuses as color swatches in 3 different places: the *Orders* page, the *Edit Order* page, and the *Users* page 
* Status colors are fully customizable, including the default WooCommerce statuses. Many color themes also available, inspired from major shopping carts
* Can work in conjunction with, or replace altogether, the default WooCommerce status swatches
* Fully supports Guest, registered Customer, and mixed Guest / Customer Orders
* Detailed reports of past Orders,  purchased Products, and all notes sent to Customer or private, for any registered or Guest Customer 
* CSV export of Orders, Products, and Notes reports
* Support of composite products within Reports
* Multi-currency support, with some extra usability features
* Translation-ready
* Completely free, with no limitations. Donations welcome.

== Installation ==

Installing *Order Status History for WooCommerce* is straightforward. As with any other WP plugin, installation can be done either by searching the WP plugin repository, or via the "Plugins > Add New" screen in your WP Dashboard using the following steps:

1. Download the plugin from wordpress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress Dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. New sortable Orders History column, inside the WooCommerce > Orders page. The history of statuses is displayed via pictorial color swatches for each Order row.
2. Details of the new Order History meta-tab, inside the Edit Order page. The aggregate color swatches for the Customer are displayed top-right.
3. Customer Orders History page tab. Displays a detailed view of each placed Order, including aggregated totals for the status of all Orders. Export to CSV also available.
4. Detail of the Customer Products History page tab. Displays a consolidated view of all products purchased, and aggregated totals for each Order status. Export to CSV also possible.
5. Customer Notes History page tab. Displays a consolidated view of all order notes, including the note from customer, and all manually added by the operator notes to customer or in private.  
6. New sortable Orders History column, inside the Users > All Users page.  Can help recognize the most active Customers by the number of their purchases.
7. New Order Status Colors page, under the main WooCommerce menu, is the settings page of the plugin. Colors and other behavior can be easily customized from that page.
8. Themed statuses, with many color schemes inspired from other well-known shopping carts.

== Frequently Asked Questions ==

= Just had a sale, but its status isn't showing up, and others seem randomly inaccurate. What's going on? =

Since version 1.8, the history queries are cached via transients, which dramatically improves speed and lowers DB calls during the display of the Orders and Users screens. The trade-off though is that the Orders history column information might be at times inaccurate, as is now rebuilt with some delay of up to 1 hour. If that's an issue to you, the `Bulk action -> Rebuild history cache` allows updating the history cache manually. 

Additionally, filter `oshwoo_cache_ttl`, (goes into `functions.php`), can help control the baseline of how long the cache stays fresh before going stale and rebuilding itself (default is 1h). Example usage: `add_filter( 'oshwoo_cache_ttl', function(){ return 0.5; });`

= What is this plugin and for whom is it for? =

*Order Status History for WooCommerce* enhances the Orders & Users screens. It can bring some useful customer historical data in a graphically intuitive way. Targeted toward Shop Managers in their daily decision-making and order fulfillment tasks.

= Does everything work? Is it there a 'Pro' version? =

There's no 'Pro' version, nor a planned one. Everything works with no limitations, completely free of charge. No strings attached.

= Is this plugin supported? =

Yes, it's supported in the WP forums, when time permits.

= Acknowledgments =

Wish to thank the *Woocommerce Customers Order History* plugin for becoming a motivational point to write this one from the ground up, with the hope of offering a better alternative in various aspects that felt lacking, according to this author.

== Changelog ==

= 2.0 - 2024-01-28 =
* NEW: finally, compatibility with HPOS (Major codebase refactoring. Consider this version as still in Beta, so please report of any issues)
* FIX: aggregate orders inconsistency in the 'All Users' table. Now registered customers purchasing as guests are also counted against their total orders made 
* Tested compatibility with latest WP/WC

= 1.8.6.2 - 2022-08-13 =
* FIX: Customer Products History has tweaked component support to properly handle some edge cases that may produce fatal errors
* FIX: the donation notice box wasn't showing up. Thanks alex28
* Check compatibility with latest WP and WC

= 1.8.6.1 - 2022-02-26 =
* FIX: with the cached queries code forgot to properly tweak the case of orders entered manually with still incomplete information (thanks joshgem)

= 1.8.6 - 2022-02-24 =
* NEW: filter `oshwoo_cache_ttl`, (goes into `functions.php`), can help control the baseline of how long the cache stays fresh before going stale and rebuilding itself (default is 1h)
* FIX: updated language strings for translations

= 1.8.5 - 2022-02-23 =
* NEW: 'Bulk action -> Rebuild history cache' menu entry. Now shop admin can update the history cache manually, when the automatic default (1 hour) isn't good enough for the given usage scenario
* FIX: reintroduced the speed optimization regressed in 1.8.1.1 after refactoring the code
* FIX: date and names not showing up in Customer Notes History

= 1.8.1.2 - 2022-02-22 =
* FIX: typo in the transient cache interval that was probably disabling the effects of the new cache functionality

= 1.8.1.1 - 2022-02-22 =
* FIX: regression of one of the lesser speed optimizations (the main one remains), as it was messing the proper sorting of the history columns

= 1.8.1 - 2022-02-22 =
* FIX: issue with redundant states in the Customer Orders History details page (thanks maikelj1978)

= 1.8 - 2022-02-22 =
* NEW: transient-level cache implementation for all history-gathering queries. *Massive* speed gains. Recommended update
* TWEAK: PHP Notice in 1.7.5.x got an effective fix this time (thanks KoolPal)
* Tested compatibility to latest versions of WP and WC

= 1.7.5.1 - 2021-11-13 =
* TWEAK: revise the code once again to address the PHP Notice in 1.7.5 (thanks KoolPal)

= 1.7.5 - 2021-11-09 =
* FIX: address a PHP Notice by better checking for component support in Customer Products History (thanks KoolPal)

= 1.7.4 - 2021-10-30 =
* PHP8 compatibility
* FIX: typo in order-history-notes.php that gives fatal error in PHP8
* Tested compatibility to latest versions of WP and WC

= 1.7.3 - 2021-10-27 =
* TWEAK: add an extra style to status buttons to make them easier to hide via CSS (thanks supervreni)
* TWEAK: Rewrite some references that may produce issues with older/incompatible versions of WC (thanks touchscreendoc)

= 1.7.2 - 2021-09-13 =
* TWEAK: now SKU differentiates product variations from the main product, in the Customer Products History page and CSV Generator

= 1.7.1 - 2021-09-02 =
* FIX: better adherence to WP plugin coding standards, guidelines, and best practices

= 1.7.0 - 2021-08-29 =
* DB access layer has been rewritten with greater flexibility in mind
* Improvements over the entire codebase should make the plugin a bit faster and some less resource-intensive in the most critical areas
* Better support for manually-entered and still-draft orders
* FIX: better support of registered customer purchases with a different email than their profile (thanks khalil43)
* FIX: Customer Notes History: the correct amounts are displayed now in the summary box
* FIX: CSV generator: the note date is correctly calculated for orders w/o notes
* TWEAK: Customer Notes History: orders with no notes are now displayed 
* TWEAK: Customer Products History: orders with no items yet (presumably manually created by shop admin) are now displayed
* i18n files updated

= 1.6.3 - 2021-08-24 =
* FIX: better handling for missing core functions that may not be available by default 

= 1.6.2 - 2021-08-21 =
* TWEAK: hard-assign the scope of a number of WP core functions, to address a fatal error happening in some specific configurations (thanks mayboroda) 
* Tested compatibility to latest versions of WP and WC

= 1.6.1 - 2021-07-16 =
* FIX: CSV Notes Exporter bug
* i18n updates
* misc. improvements

= 1.6 - 2021-07-15 =
* NEW: Customer Notes History (thanks izquierdocreativo)

= 1.5.5.1 - 2021-07-15 =
* FIX: better handling of case when order_id isn't an order object

= 1.5.5 - 2021-07-14 =
* FIX: handling of case when order_id isn't an order object (thanks webdevloper21)

= 1.5.4 - 2021-07-08 =
* TWEAK: Include the Shop manager role as user of the plugin
* Tested compatibility to latest versions of WP and WC

= 1.5.3 - 2021-04-27 =
* TWEAK: No more redirecting to the 'settings' page upon plugin activation, as some users may find it confusing or annoying 
* Tested compatibility to latest versions of WP and WC
* i18n adjustments

= 1.5.2 - 2021-04-02 =
* TWEAK: Improved compatibility with other plugins that allow generation of custom order numbers, like booster.io (thanks calicogirl) 

= 1.5.1 - 2021-03-29 =
* FIX: a stronger object check to handle a reported issue with order-history-product.php (thanks buddsg)
* Tested compatibility to latest versions of WP and WC

= 1.5 - 2021-02-08 =
* NEW: Partial support for custom (non-core) statuses. A new status 'other' has been added, which consolidates in a single color swatch all those custom WC statuses configured by other plugins that an Order may traverse. (Please re-save your theme from the WooCommerce > Status History Settings page)
* FIX: Manually created (and partially populated) orders by admin/managers are now properly displaying their respective statuses
* FIX: Improved overall handling of non-core WC statuses
* FIX: Updated i18n files

= 1.4.2 - 2021-02-06 =
* FIX: Non-core WC statuses aren't yet supported, but now are handled in a way to avoid PHP warnings and other errors
* Tested compatibility to latest versions of WP and WC  

= 1.4.1 - 2020-06-11 =
* FIX: 'postboxes' undefined error (JS) that may happen in some configurations 

= 1.4 - 2020-05-18 =
* FIX: global vs local post object issue, which may affect other plugins in the loop of the Orders page
* FIX: issue with admin page call with passed order_id param not being that of an order
* FIX: pass by reference not allowed 
* Tested compatibility to latest versions of WP and WC

= 1.3.6.1 - 2020-04-26 =
* FIX: Crash, because some files didn't get committed. Sorry!

= 1.3.6 - 2020-04-24 =
* FIX: default image link broken in Customer Products History report

= 1.3.5 - 2020-04-23 =
* Finishing namespace code reorganization. Some other minor tweaks, fixes, cleanup

= 1.3 - 2020-04-21 =
* NEW: all metaboxes are now collapsible and can be reordered 
* Codebase has been refactored by using namespaces

= 1.2.1 - 2020-04-20 =
* FIX: Missing style include in order meta-tab, so swatches were not appearing, since 1.1 (sorry)
* FIX: Contextual highlights not appearing in Customer Orders History in some cases

= 1.2 - 2020-04-19 =
* NEW: Settings Export/Import
* Better naming conventions and code organization  
* Minor adjustments

= 1.1.1 - 2020-04-16 =
* CSS adjustments

= 1.1 - 2020-04-16 =
* NEW: color themes support
* Misc. code tweaks and optimizations

= 1.0.2 - 2020-04-15 =
* Some code optimization

= 1.0.1 - 2020-04-15 =
* FIX: HTML encoding
* readme.txt updates

= 1.0 - 2020-04-11 =
* Initial version

== Upgrade Notice ==

= 1.0.0 =
Initial release
