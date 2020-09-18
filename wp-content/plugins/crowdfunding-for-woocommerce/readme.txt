=== Crowdfunding for WooCommerce ===
Contributors: wpwham
Tags: woocommerce, crowdfunding
Requires at least: 4.4
Tested up to: 5.5
Stable tag: 3.1.6
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Crowdfunding products for WooCommerce.

== Description ==

**Crowdfunding for WooCommerce** plugin adds full crowdfunding support to WooCommerce.

When adding or editing a product, you will have the possibility to set for each product individually:

* Goal (i.e. pledged) amount.
* Start and end dates.
* Custom "Back This Project" (i.e. "Add to Cart") button labels.
* Enable "Open Pricing" (i.e. "Name Your Price") functionality.

Also you will be able to:

* Add a form, so your customers/users could add their custom campaigns directly from frontend.
* Set custom HTML to show when project *not yet started* and/or *ended*.
* Modify and choose where to display crowdfunding info, that is: goal remaining, time remaining, already pledged etc.
* Choose when and if to end the campaign (goal reached, time ended).
* Choose which order statuses to count in pledged calculations.
* Style progress bars for time remaining, already pledged etc.
* Enable/disable emails on crowdfunding campaign ended, added and/or edited.

= Shortcodes =

When displaying crowdfunding data for the product, you should use plugin's shortcodes:

= Backers & Money Shortcodes =
* `[product_crowdfunding_total_sum]` - total sum (i.e. funded to date) for current product (formatted as price).
* `[product_crowdfunding_total_backers]` - total number of orders (i.e. backers) for current product.
* `[product_crowdfunding_total_items]` - total number of ordered items for current product.
* `[product_crowdfunding_list_backers]` - list of backers for current product.
* `[product_crowdfunding_goal]` - end goal for current product (formatted as price).
* `[product_crowdfunding_goal_remaining]` - sum remaining to reach the end goal for current product (formatted as price).
* `[product_crowdfunding_goal_remaining_progress_bar]` - goal remaining as graphical progress bar.
* `[product_crowdfunding_goal_backers]` - end goal (backers) for current product.
* `[product_crowdfunding_goal_backers_remaining]` - backers remaining to reach the end goal for current product.
* `[product_crowdfunding_goal_backers_remaining_progress_bar]` - goal (backers) remaining as graphical progress bar.
* `[product_crowdfunding_goal_items]` - end goal (items) for current product.
* `[product_crowdfunding_goal_items_remaining]` - items remaining to reach the end goal for current product.
* `[product_crowdfunding_goal_items_remaining_progress_bar]` - goal (items) remaining as graphical progress bar.

= Time Shortcodes =
* `[product_crowdfunding_startdate]` - starting date for current product.
* `[product_crowdfunding_starttime]` - starting time for current product.
* `[product_crowdfunding_startdatetime]` - starting date and time for current product.
* `[product_crowdfunding_deadline]` - ending date for current product.
* `[product_crowdfunding_deadline_time]` - ending time for current product.
* `[product_crowdfunding_deadline_datetime]` - ending date and time for current product.
* `[product_crowdfunding_time_remaining]` - time remaining till deadline.
* `[product_crowdfunding_time_remaining_progress_bar]` - time remaining as graphical progress bar.

= More Shortcodes =
* `[product_crowdfunding_add_new_campaign]` - campaigns by users.
* `[crowdfunding_totals]` - all crowdfunding campaigns (i.e. products) totals.
* `[product_crowdfunding_add_to_cart_form]` - backers (add to cart) HTML form.

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Visit the [Crowdfunding for WooCommerce plugin page](https://wpwham.com/products/crowdfunding-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Crowdfunding". Then try adding or editing a product.

== Changelog ==

= 3.1.6 - 2020-08-26 =
* UPDATE: display our settings in WC status report.
* UPDATE: updated .pot file for translations.

= 3.1.5 - 2020-06-08 =
* UPDATE: bump tested versions

= 3.1.4 - 2020-03-23 =
* UPDATE: bump tested versions

= 3.1.3 - 2020-02-23 =
* UPDATE: bump tested versions

= 3.1.2 - 2019-12-17 =
* UPDATE: bump tested versions

= 3.1.1 - 2019-11-15 =
* UPDATE: bump tested versions

= 3.1.0 - 2019-09-11 =
* UPDATE: updated .pot file for translations

= 3.0.2 - 2019-06-11 =
* Plugin author changed.
* Dev - Open Pricing - "Hide Original Price" option added.

= 3.0.1 - 2019-05-22 =
* Dev - Open Pricing - "Override Button Label on Archive Pages" options added.
* Tested up to: 5.4.

= 3.0.0 - 2019-05-07 =
* Fix - Open Pricing - Fixed for variable products (including when "Radio Buttons for Variable Products" option is enabled).
* Fix - Shortcodes - `[product_crowdfunding_time_to_start]` - "Illegal string offset..." notice fixed.
* Dev - Shortcodes - `[product_crowdfunding_startdatetime]` and `[product_crowdfunding_deadline_datetime]` - `date_format` and `time_format` attributes added.
* Dev - Shortcodes - `[product_crowdfunding_startdate]` and `[product_crowdfunding_deadline]` - `date_format` attribute added.
* Dev - Shortcodes - `[crowdfunding_translate]` shortcode added (for WPML/Polylang).
* Dev - Open Pricing - "too Small" and "too Big" messages replaced with "too Low" and "too High".
* Dev - Product Info - "Raw" input is now allowed in all "Info" options.
* Dev - Product Info - Deprecated shortcodes replaced in default values.
* Dev - Shortcodes are now processed in "add to cart" button text.
* Dev - Major code refactoring.
* Dev - Admin settings descriptions updated.
* Dev - Code clean up.

= 2.9.3 - 2019-05-05 =
* Dev - Shortcodes - `[crowdfunding_totals]` - `return_value` - `total_campaigns` option added.
* Dev - Shortcodes - `[crowdfunding_totals]` - `product_ids` attribute added.
* Dev - "WC tested up to" updated.

= 2.9.2 - 2019-03-20 =
* Dev - Open Pricing - "Hide Quantity Input Field" option added.

= 2.9.1 - 2019-03-14 =
* Fix - Radio Buttons for Variable Products - Variations images, descriptions, prices and availability are now properly displayed on variation switch.

= 2.9.0 - 2019-02-04 =
* Dev - Ending Options - "Admin Email: Campaign Ended" option added.
* Dev - User Campaigns - "Admin Email: Campaign Added/Edited" options added.
* Dev - `[crowdfunding_totals]` shortcode added.
* Dev - `alg_wc_crowdfunding_campaign_orders_data_calculated`, `alg_wc_crowdfunding_campaign_not_active` and `alg_wc_crowdfunding_campaign_ended` actions added.
* Dev - User Campaigns - Message: Campaign Successfully Added/Edited - Options IDs shortened.
* Dev - Code refactoring.

= 2.8.0 - 2018-12-15 =
* Fix - Crowdfunding Report - Possible "... date() expects parameter 2 to be integer, string given..." PHP warning fixed.
* Dev - Saving backers' data (first name, last name, sum, quantity, order ID, order (created) date, order currency) on product data update now.
* Dev - Shortcodes - `[product_crowdfunding_list_backers]` shortcode added.

= 2.7.0 - 2018-10-22 =
* Fix - Shortcodes - Empty attributes notice fixed.
* Fix - Shortcodes - Non-numeric value notice fixed.
* Dev - "Crowdfunding Report" submenu page added (to "WooCommerce > Crowdfunding Report").
* Dev - Advanced Options - "Add Crowdfunding Data Column" option added.
* Dev - Functions - `alg_wc_crdfnd_calculate_product_orders_data()` - Code refactoring.
* Dev - Admin settings minor restyling.
* Dev - Code refactoring and clean up.

= 2.6.2 - 2018-08-07 =
* Fix - User Campaigns - User Visibility - "Leave empty to show to all users" option fixed.
* Dev - Plugin link updated.

= 2.6.1 - 2018-05-07 =
* Dev - Product Data Update - Optimized.

= 2.6.0 - 2017-11-26 =
* Fix - Product Data Update - Fixed for new products.
* Dev - WooCommerce v3.2.0 compatibility - Admin settings - `select` type display fixed.
* Dev - "Update Data Now" button for single product added (meta box).
* Dev - Admin settings - Minor restyling.
* Dev - Filter - Fix.
* Dev - Functions - `alg_wc_crdfnd_get_product_id_or_variation_parent_id()` and `alg_wc_crdfnd_get_product_post_status()` - Checking for valid `$_product`.
* Dev - Code refactoring and clean up.

= 2.5.0 - 2017-10-11 =
* Dev - Product Data Update - Ordering products by data updated time (instead of title).
* Dev - General - Products Data Update Options - "Update data now" button added.
* Dev - General - Products Data Update Options - "Previous update triggered at ..." info added.
* Dev - General - Advanced Options - "Log" option added.
* Dev - General - Settings restyled.
* Dev - Shortcodes - `get_progress_bar()` - `text_position`, `text_position_variable_max_left`, `text_top` attributes added. Code refactoring.
* Dev - "Reset settings" option added.
* Dev - Settings sections array stored as main class property.

= 2.4.0 - 2017-05-13 =
* Dev - WooCommerce v3.x.x compatibility - Product post status.
* Dev - WooCommerce v3.x.x compatibility - Product ID.
* Dev - WooCommerce v3.x.x compatibility - Price hook (`woocommerce_get_price` and `woocommerce_product_get_price`).
* Fix - `alg_wc_crdfnd_calculate_product_orders_data()` - Additional check for product added.
* Tweak - Plugin link changed from `http://coder.fm` to `https://wpcodefactory.com`.
* Tweak - Minor code refactoring.

= 2.3.6 - 2017-03-22 =
* Dev - Language (POT) file updated.
* Dev - Code refactoring - Shortcodes divided into separate files.
* Dev - Percent from total in shortcodes: `percent` value for `type` attribute (and `round_precision` attribute) added to `[product_crowdfunding_total_sum]`, `[product_crowdfunding_total_backers]`, `[product_crowdfunding_total_items]`, `[product_crowdfunding_goal_remaining]`, `[product_crowdfunding_goal_backers_remaining]`, `[product_crowdfunding_goal_items_remaining]` shortcodes.

= 2.3.5 - 2017-03-10 =
* Dev - `alg_crowdfunding_output_shortcode_price` filter added.

= 2.3.4 - 2016-12-21 =
* Dev - `load_plugin_textdomain` moved from `init` hook to constructor.
* Tweak - readme.txt updated.
* Tweak - basename check added.

= 2.3.3 - 2016-12-16 =
* Fix - `load_plugin_textdomain` moved from `init` hook to constructor.
* Dev - jQuery dependency and loading in footer added to `wp_enqueue_script( 'alg-datepicker' )`.
* Dev - `select` type added to user campaign and admin fields. Can be used in `alg_crowdfunding_admin_fields`, `alg_crowdfunding_user_campaign_fields`, `alg_crowdfunding_user_campaign_save_fields` hooks.
* Dev - Brazilian Portuguese (`pt_BR`) translation updated.
* Tweak - Donate link changed.
* Tweak - Typo (to and too) fixed.

= 2.3.2 - 2016-12-01 =
* Fix - Radio Buttons for Variable Products - Disable add to cart button on campaign not started or not active.
* Fix - `current_time` result converted to `int`.
* Fix - User Campaigns - User Visibility - Option not functioning correctly, fixed.
* Dev - Open Price - "Number of Decimals (Price Step)" admin option added.
* Dev - User Campaigns - "Campaigns" Tab - "Add Edit Campaign Button" and "Add Delete Campaign Button" options added.
* Dev - Admin fields meta box in product edit - `required` option added.
* Dev - `alg_crowdfunding_admin_fields` and `alg_crowdfunding_user_campaign_fields` filters added. `alg_crowdfunding_user_campaign_save_fields` action added.
* Dev - Plugin version added to all `wp_enqueue_style`, `wp_enqueue_script`, `wp_register_script`.
* Dev - `display="date"` changed to `display="alg_crowdfunding_date"`; `display="time"` changed to `display="alg_crowdfunding_time"`.
* Dev - Language (POT) file updated.
* Dev - `do_shortcode()` added to `is_purchasable_html()`.
* Dev - `[product_crowdfunding_time_to_start]` shortcode added (with `campaign_will_start`, `campaign_started` and `precision` attributes).
* Dev - `[product_crowdfunding_time_remaining]` - Full "time left" returned. `campaign_will_end`, `campaign_ended` and `precision` attributes added.
* Dev - Brazilian Portuguese (`pt_BR`) translation files added.
* Tweak - User Campaigns - User Visibility - Description tip added.
* Tweak - Typo in functions names fixed.
* Tweak - User Campaigns - "Campaigns" Tab - Admin option title fixed.

= 2.3.1 - 2016-11-10 =
* Fix - "Enable Open Pricing" checkbox not saving in admin product edit, fixed.
* Fix - "My Account > Campaigns" fixed (endpoint added).
* Dev - "... seconds since last update" message added to "General" settings section.
* Dev - Language (POT) file updated.
* Dev - `WP_Query` optimized in `alg_wc_crdfnd_calculate_product_orders_data()` to loop in blocks.
* Dev - `WP_Query` optimized in `alg_wc_crdfnd_calculate_product_orders_data()`, `alg_wc_crdfnd_count_crowdfunding_products()`, `add_my_products_content_my_account_page()`, `add_my_products_tab_my_account_page()` and `update_products_data()` to return `ids` only.

= 2.3.0 - 2016-08-20 =
* Fix - "Crowdfunding enabled" checkbox not saving when adding new product, fixed.
* Fix - Variable radio buttons - Variation image fixed.
* Dev - "Crowdfunding Orders Data" metabox added.
* Dev - "User Campaigns" section added.
* Dev - "Products Data Update Options" section (and crons) added.
* Dev - "Ending Options" section added (including new "End On Goal Reached").
* Dev - Progress bar styling options added.
* Dev - "Order Statuses to Include in Calculations" option added (`order_status` shortcode attribute removed).
* Dev - Version system added.
* Dev - Time and date pickers loading moved to frontend (for "User Campaigns" section).
* Dev - Functions moved to separate functions file.
* Dev - Shortcodes loading moved to frontend file.
* Tweak - "General" section link (bold) fixed.
* Tweak - Contributors changed.

= 2.2.4 - 2016-07-26 =
* Fix - `get_product_orders_data()` global post fix.

= 2.2.3 - 2016-05-24 =
* Fix - Text domain renamed in plugin header.

= 2.2.2 - 2016-05-24 =
* Dev - Multisite enabled. `is_super_admin` call replaced.
* Dev - Translation text domain renamed. `lt_LT` translation added.

= 2.2.1 - 2016-05-13 =
* Fix - Titles in per product admin options table - caused PHP notice when saving product.
* Dev - Text domain added to the plugin header.
* Dev - `[product_crowdfunding_time_progress_bar]` renamed to `[product_crowdfunding_time_remaining_progress_bar]`.
* Dev - POT file updated.

= 2.2.0 - 2016-05-10 =
* Fix - `total_orders` in `get_product_orders_data`.
* Fix - Custom links fixed.
* Dev - "Open Pricing (Name Your Price)" functionality added.
* Dev - `starting_offset` shortcodes attribute added to `get_product_orders_data`.
* Dev - `show_if_zero` attribute added to `output_shortcode` function.
* Dev - `[product_crowdfunding_goal_backers]` shortcode added.
* Dev - `[product_crowdfunding_goal_items]` shortcode added.
* Dev - `[product_crowdfunding_goal_backers_remaining]` shortcode added.
* Dev - `[product_crowdfunding_goal_items_remaining]` shortcode added.
* Dev - `[product_crowdfunding_goal_backers_remaining_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_items_remaining_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_progress_bar]` renamed to `[product_crowdfunding_goal_remaining_progress_bar]`.
* Dev - `[product_total_orders_sum]` renamed to `[product_crowdfunding_total_sum]`.
* Dev - `[product_total_orders]` renamed to `[product_crowdfunding_total_backers]`.
* Dev - `[product_crowdfunding_total_items]` shortcode added.
* Dev - Formating date and time according to local format.
* Dev - `post__not_in` added to `save_meta_box`.
* Dev - POT file added.
* Tweak - Titles added in per product admin options table.

= 2.1.0 - 2015-11-26 =
* Dev - WooCommerce Grouped products support added.
* Dev - `product_id` attribute added in shortcodes.
* Dev - `order_status` attribute added in orders shortcodes: `product_crowdfunding_goal_progress_bar`, `product_crowdfunding_goal_remaining`, `product_total_orders`, `product_total_orders_sum`.
* Dev - "Crowdfunding" column added to admin products list.
* Fix - Counting fix.
* Fix - Additional check in `is_crowdfunding_product()`. Caused PHP notice.
* Fix - Global `product` reset in `get_product_orders_data()` added.

= 2.0.0 - 2015-10-27 =
* Dev - Crowdfunding type product removed - now any product type (e.g. simple, variable) can be used as crowdfunding product.
* Fix - Shortcodes - `[product_crowdfunding_time_remaining]` singular form bug fixed.

= 1.2.0 - 2015-10-18 =
* Dev - Product Info - *Custom Product Info - Category View* option added.
* Dev - `[product_crowdfunding_time_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_add_to_cart_form]` shortcode added.

= 1.1.1 - 2015-10-02 =
* Fix - "Remove Last Variation" bug when saving on product's admin edit page, fixed.

= 1.1.0 - 2015-09-30 =
* Dev - `[product_crowdfunding_starttime]`, `[product_crowdfunding_startdatetime]`, `[product_crowdfunding_deadline_time]`, `[product_crowdfunding_deadline_datetime]` shortcodes added.
* Dev - Start/end time added.

= 1.0.1 - 2015-08-21 =
* Fix - Validation on frontend only affects `crowdfunding` type products now.

= 1.0.0 - 2015-08-20 =
* Initial Release.
