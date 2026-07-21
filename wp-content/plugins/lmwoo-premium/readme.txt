=== License Manager for WooCommerce ===
Tags: license key, license, key, software license, serial key, manager, woocommerce, wordpress
Requires at least: 4.7
Tested up to: 6.8.1
Stable tag: 1.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easily sell and manage software license keys through your WooCommerce shop

== Description ==
The **License Manager for WooCommerce** allows you to easily sell and manage all of your digital license keys. With features like the bulk importer, automatic delivery, automatic stock management, and database encryption, your shop will now run easier than ever.

[Plugin & API Documentation](https://www.licensemanager.at/docs)

#### Key plugin features

* Automatically sell and deliver license keys through WooCommerce
* Automatically manage the stock for licensed products
* Activate, deactivate, and check your licenses through the REST API
* Manually resend license keys
* Add and import license keys and assign them to WooCommerce products
* Import license keys by file upload
* Export license keys as PDF or CSV
* Manage the status of your license keys
* Create license key generators with custom parameters
* Assign a generator to one (or more!) WooCommerce product(s), these products then automatically create a license key whenever they are sold.
* Extend Validation Endpoint
* License by customer endpoint
* Product used on page

#### API

The plugin also offers additional endpoints for manipulating licenses and generator resources. These routes are authorized via API keys (generated through the plugin settings) and accessed via the WordPress API. You can also check out the [documentation pages](https://www.licensemanager.at/documentation/), as they contain the most essential information on what the plugin can do for you.

#### Important

The plugin will create two files inside the `wp-content/uploads/lmfwc-files` folder. These files (`defuse.txt` and `secret.txt`) contain cryptographic secrets which are automatically generated if they don't exist. These cryptographic secrets are used to encrypt, decrypt and hash your license keys. Once they are generated please **back them up somewhere safe**. In case you lose these two files your encrypted license keys inside the database will remain forever lost!

== Installation ==

#### Manual installation

1. Upload the plugin files to the `/wp-content/plugins/license-manager-for-woocommerce` directory, or install the plugin through the WordPress *Plugins* page directly.
1. Activate the plugin through the *Plugins* page in WordPress.
1. Use the *License Manager* → *Settings* page to configure the plugin.

#### Installation through WordPress

1. Open up your WordPress Dashboard and navigate to the *Plugins* page.
1. Click on *Add new*
1. In the search bar type "License Manager for WooCommerce"
1. Select this plugin and click on *Install now*

#### Important

The plugin will create two files inside the `wp-content/uploads/lmfwc-files` folder. These files (`defuse.txt` and `secret.txt`) contain cryptographic secrets which are automatically generated if they don't exist. These cryptographic secrets are used to encrypt, decrypt and hash your license keys. Once they are generated please **back them up somewhere safe**. In case you lose these two files your encrypted license keys inside the database will remain forever lost!

== Changelog ==
2025-06-02 - version 1.3.0
* Added - Dashboard for analytics and licenses reports.
* Added - Traceback option, so that I can run API testing and check in logs accordingly.
* Added - Termination option for the License Key which has been discarded because of refunded, canceled or failed somehow.

2025-04-15 - version 1.2.1
* Freemius SDK Update

2024-11-12 - version 1.2
* Minor Bug Fixes & Improvements

2023-12-11 - version 1.1
* Improved - WooCommerce Subscriptions support. Now you can extend existing license or generate new one upon subscription renewal
* Added - Application support for plugins, themes or other Applications
* Added - Application management. Sell Application setup a gallery, support information, FAQs that appear on the product page
* Added - Application release management. Downloadable Application can be distributed as a releases.
* Added - Application details REST Endpoint to get information about specific Application.
* Added - Application download REST Endpoint to download the latest release for specific Application.
* Added - License Certificates.
* Added - WooCommerce HPOS support.
* Fixed - OrderBy query Vulnerability 

2022-08-25 - version 1.0.0
* Initial release.

