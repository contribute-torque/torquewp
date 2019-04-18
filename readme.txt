=== Torque WooCommerce Extension ===
Contributors: Monero Integrations Team, Ryo Currency Project
Donate link: http://monerointegrations.com/donate.html
Tags: torque, woocommerce, integration, payment, merchant, cryptocurrency, accept torque, torque woocommerce
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Torque WooCommerce Extension is a Wordpress plugin that allows to accept bitcoins at WooCommerce-powered online stores.

== Description ==

An extension to WooCommerce for accepting Torque as payment in your store.

= Benefits =

* Accept payment directly into your personal Torque wallet.
* Accept payment in torque for physical and digital downloadable products.
* Add torque payments option to your existing online store with alternative main currency.
* Flexible exchange rate calculations fully managed via administrative settings.
* Zero fees and no commissions for torque payments processing from any third party.
* Automatic conversion to Torque via real time exchange rate feed and calculations.
* Ability to set exchange rate calculation multiplier to compensate for any possible losses due to bank conversions and funds transfer fees.

== Installation ==

1. Install "Torque WooCommerce extension" WordPress plugin just like any other WordPress plugin.
2. Activate
3. Setup your torque-wallet-rpc with a view-only wallet
4. Add your torque-wallet-rpc host address and Torque address in the settings panel
5. Click “Enable this payment gateway”
6. Enjoy it!

== Remove plugin ==

1. Deactivate plugin through the 'Plugins' menu in WordPress
2. Delete plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Torque Payment Box
2. Torque Options

== Changelog ==

= 0.1 =
* First version ! Yay!

= 0.2 =
* Bug fixes

= 0.3 =
* Complete rewrite of how the plugin handles payments

== Upgrade Notice ==

soon

== Frequently Asked Questions ==

* What is Torque ?
Torque is completely private, cryptographically secure, digital cash used across the globe. See https://gettorque.org for more information

* What is a Torque wallet?
A Torque wallet is a piece of software that allows you to store your funds and interact with the Torque network. You can get a Torque wallet from https://gettorque.org/downloads

* What is torque-wallet-rpc ?
The torque-wallet-rpc is an RPC server that will allow this plugin to communicate with the Torque network. You can download it from https://gettorque.org/downloads with the command-line tools.

* Why do I see `[ERROR] Failed to connect to torque-wallet-rpc at localhost port 20188
Syntax error: Invalid response data structure: Request id: 1 is different from Response id: ` ?
This is most likely because this plugin can not reach your torque-wallet-rpc. Make sure that you have supplied the correct host IP and port to the plugin in their fields. If your torque-wallet-rpc is on a different server than your wordpress site, make sure that the appropriate port is open with port forwarding enabled.
