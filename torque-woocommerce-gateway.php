<?php
/*
Plugin Name: Torque Woocommerce Gateway
Plugin URI: https://github.com/contribute-torque/torquewp
Description: Extends WooCommerce by adding a Torque Gateway
Version: 3.0.0
Tested up to: 4.9.8
Author: mosu-forge, SerHack
Author URI: https://monerointegrations.com/
*/
// This code isn't for Dark Net Markets, please report them to Authority!

defined( 'ABSPATH' ) || exit;

// Constants, you can edit these if you fork this repo
define('TORQUE_GATEWAY_MAINNET_EXPLORER_URL', 'https://explorer.torque.cash/');
define('TORQUE_GATEWAY_TESTNET_EXPLORER_URL', 'https://explorer.torque.cash/');
define('TORQUE_GATEWAY_ADDRESS_PREFIX', 0x18);
define('TORQUE_GATEWAY_ADDRESS_PREFIX_INTEGRATED', 0x19);
define('TORQUE_GATEWAY_ATOMIC_UNITS', 2);
define('TORQUE_GATEWAY_ATOMIC_UNIT_THRESHOLD', 2); // Amount under in atomic units payment is valid
define('TORQUE_GATEWAY_DIFFICULTY_TARGET', 300);

// Do not edit these constants
define('TORQUE_GATEWAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TORQUE_GATEWAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TORQUE_GATEWAY_ATOMIC_UNITS_POW', pow(10, TORQUE_GATEWAY_ATOMIC_UNITS));
define('TORQUE_GATEWAY_ATOMIC_UNITS_SPRINTF', '%.'.TORQUE_GATEWAY_ATOMIC_UNITS.'f');

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'torque_init', 1);
function torque_init() {

    // If the class doesn't exist (== WooCommerce isn't installed), return NULL
    if (!class_exists('WC_Payment_Gateway')) return;

    // If we made it this far, then include our Gateway Class
    require_once('include/class-torque-gateway.php');

    // Create a new instance of the gateway so we have static variables set up
    new Torque_Gateway($add_action=false);

    // Include our Admin interface class
    require_once('include/admin/class-torque-admin-interface.php');

    add_filter('woocommerce_payment_gateways', 'torque_gateway');
    function torque_gateway($methods) {
        $methods[] = 'Torque_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'torque_payment');
    function torque_payment($links) {
        $plugin_links = array(
            '<a href="'.admin_url('admin.php?page=torque_gateway_settings').'">'.__('Settings', 'torque_gateway').'</a>'
        );
        return array_merge($plugin_links, $links);
    }

    add_filter('cron_schedules', 'torque_cron_add_one_minute');
    function torque_cron_add_one_minute($schedules) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => __('Once every minute', 'torque_gateway')
        );
        return $schedules;
    }

    add_action('wp', 'torque_activate_cron');
    function torque_activate_cron() {
        if(!wp_next_scheduled('torque_update_event')) {
            wp_schedule_event(time(), 'one_minute', 'torque_update_event');
        }
    }

    add_action('torque_update_event', 'torque_update_event');
    function torque_update_event() {
        Torque_Gateway::do_update_event();
    }

    add_action('woocommerce_thankyou_'.Torque_Gateway::get_id(), 'torque_order_confirm_page');
    add_action('woocommerce_order_details_after_order_table', 'torque_order_page');
    add_action('woocommerce_email_after_order_table', 'torque_order_email');

    function torque_order_confirm_page($order_id) {
        Torque_Gateway::customer_order_page($order_id);
    }
    function torque_order_page($order) {
        if(!is_wc_endpoint_url('order-received'))
            Torque_Gateway::customer_order_page($order);
    }
    function torque_order_email($order) {
        Torque_Gateway::customer_order_email($order);
    }

    add_action('wc_ajax_torque_gateway_payment_details', 'torque_get_payment_details_ajax');
    function torque_get_payment_details_ajax() {
        Torque_Gateway::get_payment_details_ajax();
    }

    add_filter('woocommerce_currencies', 'torque_add_currency');
    function torque_add_currency($currencies) {
        $currencies['Torque'] = __('Torque', 'torque_gateway');
        return $currencies;
    }

    add_filter('woocommerce_currency_symbol', 'torque_add_currency_symbol', 10, 2);
    function torque_add_currency_symbol($currency_symbol, $currency) {
        switch ($currency) {
        case 'Torque':
            $currency_symbol = 'XTC';
            break;
        }
        return $currency_symbol;
    }

    if(Torque_Gateway::use_torque_price()) {

        // This filter will replace all prices with amount in Torque (live rates)
        add_filter('wc_price', 'torque_live_price_format', 10, 3);
        function torque_live_price_format($price_html, $price_float, $args) {
            if(!isset($args['currency']) || !$args['currency']) {
                global $woocommerce;
                $currency = strtoupper(get_woocommerce_currency());
            } else {
                $currency = strtoupper($args['currency']);
            }
            return Torque_Gateway::convert_wc_price($price_float, $currency);
        }

        // These filters will replace the live rate with the exchange rate locked in for the order
        // We must be careful to hit all the hooks for price displays associated with an order,
        // else the exchange rate can change dynamically (which it should for an order)
        add_filter('woocommerce_order_formatted_line_subtotal', 'torque_order_item_price_format', 10, 3);
        function torque_order_item_price_format($price_html, $item, $order) {
            return Torque_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_formatted_order_total', 'torque_order_total_price_format', 10, 2);
        function torque_order_total_price_format($price_html, $order) {
            return Torque_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_order_item_totals', 'torque_order_totals_price_format', 10, 3);
        function torque_order_totals_price_format($total_rows, $order, $tax_display) {
            foreach($total_rows as &$row) {
                $price_html = $row['value'];
                $row['value'] = Torque_Gateway::convert_wc_price_order($price_html, $order);
            }
            return $total_rows;
        }

    }

    add_action('wp_enqueue_scripts', 'torque_enqueue_scripts');
    function torque_enqueue_scripts() {
        if(Torque_Gateway::use_torque_price())
            wp_dequeue_script('wc-cart-fragments');
        if(Torque_Gateway::use_qr_code())
            wp_enqueue_script('torque-qr-code', TORQUE_GATEWAY_PLUGIN_URL.'assets/js/qrcode.min.js');

        wp_enqueue_script('torque-clipboard-js', TORQUE_GATEWAY_PLUGIN_URL.'assets/js/clipboard.min.js');
        wp_enqueue_script('torque-gateway', TORQUE_GATEWAY_PLUGIN_URL.'assets/js/torque-gateway-order-page.js');
        wp_enqueue_style('torque-gateway', TORQUE_GATEWAY_PLUGIN_URL.'assets/css/torque-gateway-order-page.css');
    }

    // [torque-price currency="USD"]
    // currency: BTC, GBP, etc
    // if no none, then default store currency
    function torque_price_func( $atts ) {
        global  $woocommerce;
        $a = shortcode_atts( array(
            'currency' => get_woocommerce_currency()
        ), $atts );

        $currency = strtoupper($a['currency']);
        $rate = Torque_Gateway::get_live_rate($currency);
        if($currency == 'BTC')
            $rate_formatted = sprintf('%.8f', $rate / 1e8);
        else
            $rate_formatted = sprintf('%.5f', $rate / 1e8);

        return "<span class=\"torque-price\">1 XTC = $rate_formatted $currency</span>";
    }
    add_shortcode('torque-price', 'torque_price_func');


    // [torque-accepted-here]
    function torque_accepted_func() {
        return '<img src="'.TORQUE_GATEWAY_PLUGIN_URL.'assets/images/torque-accepted-here.png" />';
    }
    add_shortcode('torque-accepted-here', 'torque_accepted_func');

}

register_deactivation_hook(__FILE__, 'torque_deactivate');
function torque_deactivate() {
    $timestamp = wp_next_scheduled('torque_update_event');
    wp_unschedule_event($timestamp, 'torque_update_event');
}

register_activation_hook(__FILE__, 'torque_install');
function torque_install() {
    global $wpdb;
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "torque_gateway_quotes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               order_id BIGINT(20) UNSIGNED NOT NULL,
               payment_id VARCHAR(94) DEFAULT '' NOT NULL,
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               paid TINYINT NOT NULL DEFAULT 0,
               confirmed TINYINT NOT NULL DEFAULT 0,
               pending TINYINT NOT NULL DEFAULT 1,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (order_id)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "torque_gateway_quotes_txids";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
               payment_id VARCHAR(94) DEFAULT '' NOT NULL,
               txid VARCHAR(64) DEFAULT '' NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               height MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (id),
               UNIQUE KEY (payment_id, txid, amount)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "torque_gateway_live_rates";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (currency)
               ) $charset_collate;";
        dbDelta($sql);
    }
}
