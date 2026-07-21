<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin Name: WooCommerce Taboola Event Tracker
 * Description: Tracks WooCommerce events and fires corresponding Taboola pixel events.
 * Version: 1.0
 * Author: Your Name
 */
require_once 'common.php';

// Check if WooCommerce is active
if (!tabpx_is_woocommerce_active()) {
    return;
}

/**
 * Enqueue Taboola event JavaScript
 *
 * @param string $event_name The name of the event.
 * @param string $tabpx_id The Taboola account ID.
 * @param array $additional_data An associative array of additional data like revenue, currency, order_id, etc.
 */
function tabpx_enqueue_event_js($event_name, $tabpx_id, $additional_data = [])
{
    $params = array(
        'notify' => 'event',
        'name' => $event_name,
        'it' => TABPX_INTEGRATION_WOOCOMMERCE,
        'id' => $tabpx_id,
        'integration_version' => tabpx_get_plugin_version(),
    );

    // Add additional data (e.g., revenue, currency, orderid) directly from the caller
    foreach ($additional_data as $key => $value) {
        $params[$key] = $value;
    }

    // Register and enqueue a script to add inline tracking code
    $script_handle = 'taboola-event-' . $event_name . '-' . uniqid();
    wp_register_script($script_handle, '', array(), '1.0.0', true);
    wp_enqueue_script($script_handle);
    
    $js_code = '_tfa.push(' . wp_json_encode($params) . ');';
    wp_add_inline_script($script_handle, $js_code);
}

function tabpx_add_to_cart_event_fragment($fragments)
{    
    $product_id = isset($_POST['product_id']) ? (int) sanitize_text_field(wp_unslash($_POST['product_id'])) : 0;
    $quantity = isset($_POST['quantity']) ? (int) sanitize_text_field(wp_unslash($_POST['quantity'])) : 1;
    $product = wc_get_product($product_id);

    if (!$product instanceof \WC_Product || $quantity <= 0) {
        return $fragments; // Return unmodified fragments if product is invalid
    }

    $tabpx_id = tabpx_get_account_id(); // Get Taboola Pixel ID

    $params = array(
        'notify' => 'event',
        'name' => 'add_to_cart',
        'it' => TABPX_INTEGRATION_WOOCOMMERCE,
        'id' => $tabpx_id,
        'revenue' => (float)$product->get_price() * $quantity,
        'currency' => get_woocommerce_currency(),
        'quantity' => $quantity,
        'integration_version' => tabpx_get_plugin_version(),
    );

    // Use WordPress enqueue functions instead of direct script output
    $script_handle = 'taboola-add-to-cart-' . uniqid();
    wp_register_script($script_handle, '', array(), '1.0.0', true);
    wp_enqueue_script($script_handle);
    wp_add_inline_script($script_handle, '_tfa.push(' . wp_json_encode($params) . ');');

    // Inject a placeholder div for the fragment
    $fragments['div.wc-taboola-event-placeholder'] = '<div class="wc-taboola-event-placeholder"></div>';

    return $fragments;
}

function tabpx_filter_add_to_cart_fragments()
{
    if ('no' === get_option('woocommerce_cart_redirect_after_add', 'no')) {
        add_filter('woocommerce_add_to_cart_fragments', 'tabpx_add_to_cart_event_fragment');
    }
}

/**
 * Register WooCommerce event tracking hooks
 */
function tabpx_register_woocommerce_events()
{
    $tabpx_id = tabpx_get_account_id();

    /**
     * Track "Purchase" Event
     */
    add_action('woocommerce_thankyou', function ($order_id) use ($tabpx_id) {
        $order = wc_get_order($order_id);
        $revenue = $order->get_total();
        $currency = $order->get_currency();

        tabpx_enqueue_event_js('make_purchase', $tabpx_id, [
            'revenue' => $revenue,
            'currency' => $currency,
            'orderid' => $order_id
        ]);
    });

    /**
     * Track "Add to Cart" Event
     */
    add_action('woocommerce_add_to_cart', function ($cart_item_key, $product_id, $quantity) use ($tabpx_id) {
        $product = wc_get_product($product_id);
        $revenue = $product->get_price() * $quantity;
        $currency = get_woocommerce_currency(); // Get the currency

        tabpx_enqueue_event_js('add_to_cart', $tabpx_id, [
            'revenue' => $revenue,
            'currency' => $currency,
            'quantity' => $quantity
        ]);
    }, 10, 3);

    /**
     * Track "Start Checkout" Event
     */
    add_action('woocommerce_checkout_init', function () use ($tabpx_id) {
        if (is_checkout() && !is_order_received_page()) {
            tabpx_enqueue_event_js('start_checkout', $tabpx_id, []);
        }
    });

    /**
     * Track "Cart View" Event
     */
    add_action('wp_footer', function () use ($tabpx_id) {
        if (is_cart()) {
            tabpx_enqueue_event_js('cart_view', $tabpx_id, []);
        }
    });

    /**
     * Track "Product View" Event
     */
    add_action('woocommerce_before_single_product', function () use ($tabpx_id) {
        tabpx_enqueue_event_js('product_view', $tabpx_id, []);
    });

    /**
     * Track "Collection View" Event
     */
    add_action('woocommerce_before_shop_loop', function () use ($tabpx_id) {
        tabpx_enqueue_event_js('collection_view', $tabpx_id, []);
    });

    /**
     * Track "Search Submit" Event
     */
    add_action('woocommerce_product_query', function () use ($tabpx_id) {
        $verified = true;
        if (isset($_GET['_wpnonce'])) {
            $verified = wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'woocommerce-search');
        }

        // Only process the search if no nonce is provided (standard search) or if the nonce is valid
        if ($verified && !empty($_GET['s']) && isset($_GET['s'])) {
            $search_term = sanitize_text_field(wp_unslash($_GET['s']));
            if (!empty($search_term)) {
                tabpx_enqueue_event_js('search_submitted', $tabpx_id, []);
            }
        }
    });

    add_action('woocommerce_ajax_added_to_cart', 'tabpx_filter_add_to_cart_fragments', 10);
    add_action('wp_footer', function () {
        echo '<div class="wc-taboola-event-placeholder"></div>';
    });
}

// Initialize the event tracking
add_action('init', 'tabpx_register_woocommerce_events');
