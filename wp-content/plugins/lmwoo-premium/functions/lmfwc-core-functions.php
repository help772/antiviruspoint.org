<?php
/**
 * LicenseManager for WooCommerce Core Functions
 *
 * General core functions available on both the front-end and admin.
 */
// phpcs:ignoreFile

use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Settings;

defined('ABSPATH') || exit;

/**
 * Checks if a license key already exists inside the database table.
 *
 * @param string   $licenseKey
 * @param null|int $licenseKeyId
 *
 * @return bool
 */
function lmfwc_duplicate( $licenseKey, $licenseKeyId = null ) {
	$duplicate = false;
	/**
	* Filter lmfwc_hash
	* 
	* @since 1.0
	**/
	$hash  = apply_filters('lmfwc_hash', $licenseKey);

	// Add action
	if ( null === $licenseKeyId ) {
		$query = array( 'hash' => $hash );
		if ( LicenseResourceRepository::instance()->findBy($query) ) {
			$duplicate = true;
		}
	} elseif ( null !== $licenseKeyId && is_numeric($licenseKeyId) ) {
		$table = LicenseResourceRepository::instance()->getTable();
		$query = "
            SELECT
                id
            FROM
                {$table}
            WHERE
                1=1
                AND hash = '{$hash}'
                AND id NOT LIKE {$licenseKeyId}
            ;
        ";

		if (LicenseResourceRepository::instance()->query($query)) {
			$duplicate = true;
		}
	}

	return $duplicate;
}
add_filter('lmfwc_duplicate', 'lmfwc_duplicate', 10, 2);

/**
 * Generates a random hash.
 *
 * @return string
 */
function lmfwc_rand_hash() {
	/**
	* Filter lmfwc_rand_hash
	* 
	* @since 1.0
	**/
	$hash = apply_filters('lmfwc_rand_hash', null);
	if ( $hash ) {
		return $hash;
	}

	if (function_exists('wc_rand_hash')) {
		return wc_rand_hash();
	}

	if (!function_exists('openssl_random_pseudo_bytes')) {
		return sha1(wp_rand());
	}

	return bin2hex(openssl_random_pseudo_bytes(20));
}

/**
 * Converts dashes to camel case with first capital letter.
 *
 * @param string $input
 * @param string $separator
 *
 * @return string|string[]
 */
function lmfwc_camelize( $input, $separator = '_' ) {
	return str_replace($separator, '', ucwords($input, $separator));
}

/**
 * Returns a format string for expiration dates.
 *
 * @return string
 */
function lmfwc_expiration_format() {

	$expiration_format = Settings::get( 'lmfwc_expire_format', Settings::SECTION_GENERAL );
	if ( false === $expiration_format ) {
		$expiration_format = '{{DATE_FORMAT}}, {{TIME_FORMAT}} T';
	}

	if ( strpos( $expiration_format, '{{DATE_FORMAT}}' ) !== false ) {
		$date_format       = get_option( 'date_format', 'F j, Y' );
		$expiration_format = str_replace( '{{DATE_FORMAT}}', $date_format, $expiration_format );
	}

	if ( strpos( $expiration_format, '{{TIME_FORMAT}}' ) !== false ) {
		$time_format       = get_option( 'time_format', 'g:i a' );
		$expiration_format = str_replace( '{{TIME_FORMAT}}', $time_format, $expiration_format );
	}

	return $expiration_format;
}

function lmfwc_array_key_first( $array ) {
	if ( function_exists( 'array_key_first' ) ) {
		return array_key_first( $array );
	}

	reset( $array );

	return key( $array );
}

function lmfwc_convert_valid_for_to_expires_at( $valid_for, $format = 'Y-m-d H:i:s' ) {
	if ( ! empty( $valid_for ) ) {
		try {
			$date          = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
			$date_interval = new DateInterval( 'P' . $valid_for . 'D' );
		} catch ( Exception $e ) {
			return null;
		}

		return $date->add( $date_interval )->format( $format );
	}

	return null;
}


function lmfwc_update_order_downloads_expiration( $expires_at, $order_id ) {
	if ( ! empty( $expires_at ) && ! empty( $order_id ) && Settings::get( 'lmfwc_download_expires', Settings::SECTION_GENERAL ) ) {
		try {
			$data_store           = WC_Data_Store::load( 'customer-download' );
			$download_permissions = $data_store->get_downloads(
				array(
					'order_id' => $order_id,
				)
			);
		} catch ( Exception $e ) {
			return;
		}

		// Validate expiresAt is given in the right format (time check) - otherwise add current GMT time
		if ( ! false !== DateTime::createFromFormat( 'Y-m-d H:i:s', $expires_at ) ) {
			try {
				$date  = new DateTime( $expires_at, new DateTimeZone( 'GMT' ) );
				$now   = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
				$today = new DateTime( gmdate( 'Y-m-d' ), new DateTimeZone( 'GMT' ) );
				$time  = $today->diff( $now );

				$date->add( $time );

				$expires_at = $date->format( 'Y-m-d H:i:s' );
			} catch ( Exception $e ) {
				return;
			}
		}

		if ( $download_permissions && count( $download_permissions ) > 0 ) {
			foreach ( $download_permissions as $download ) {
				$download = new WC_Customer_Download( $download->get_id() );
				$download->set_access_expires( $expires_at );
				$download->save();
			}
		}
	}
}


function lmfwc_is_licensed_product( $product_id ) {
	$product = wc_get_product($product_id);
	if ( $product->get_meta( 'lmfwc_licensed_product', true ) ) {
		return true;
	}

	return false;
}


function lmfwc_is_license_expiration_extendable_for_subscriptions( $product_id ) {
	$product = wc_get_product($product_id);
	if ( $product->get_meta( 'lmfwc_license_expiration_extendable_for_subscriptions', true ) ) {
		return true;
	}

	return false;
}


function lmfwc_is_order_complete( $order_id ) {
	$order = wc_get_order($order_id);
	if ( ! $order->get_meta( 'lmfwc_order_complete' ) ) {
		return false;
	}

	return true;
}


function lmfwc_get_subscription_renewal_action( $product_id ) {
	$product = wc_get_product($product_id);
	$action = $product->get_meta( 'lmfwc_subscription_renewal_action', true );

	if ( $action && is_string( $action ) ) {
		return $action;
	}

	return 'issue_new_license';
}


function lmfwc_get_subscription_renewal_interval_type( $product_id ) {
	$product = wc_get_product($product_id);
	$interval_type = $product->get_meta( 'lmfwc_subscription_renewal_interval_type', true );

	if ( $interval_type && is_string( $interval_type ) ) {
		return $interval_type;
	}

	return 'subscription';
}


function lmfwc_get_subscription_renewal_custom_interval( $product_id ) {
	$product = wc_get_product($product_id);
	$customer_interval = $product->get_meta( 'lmfwc_subscription_renewal_custom_interval', true );

	if ( $customer_interval && is_numeric( $customer_interval ) ) {
		return intval( $customer_interval );
	}

	return 1;
}


function lmfwc_get_subscription_renewal_custom_period( $product_id ) {
	$product = wc_get_product($product_id);
	$interval_type          = $product->get_meta( 'lmfwc_subscription_renewal_custom_period', true );
	$allowed_interval_types = array( 'hour', 'day', 'week', 'month', 'year' );

	if ( $interval_type && is_string( $interval_type ) && in_array( $interval_type, $allowed_interval_types ) ) {
		return sanitize_text_field( $interval_type );
	}

	return 'day';
}


function lmfwc_array_insert_after( $needle, $haystack, $new_key, $new_value ) {
	if ( array_key_exists( $needle, $haystack ) ) {
		$new_array = array();

		foreach ( $haystack as $key => $value ) {
			$new_array[ $key ] = $value;

			if ( $key === $needle ) {
				$new_array[ $new_key ] = $new_value;
			}
		}

		return $new_array;
	}

	return $haystack;
}

function lmfwc_allowed_html() {

	$allowed_atts = array(
		'role'          => array(),
		'align'      => array(),
		'class'      => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'required'   => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'title'      => array(),
		'selected'   => array(),
		'enctype'    => array(),
		'disable'    => array(),
		'disabled'   => array(),
		'aria-label' => array(),
		'data-label' => array(),
		'data-attribute' => array(),
		'data-src'      => array(),
		'data-large_image' => array(),
		'data-variation_ids' => array(),
		'data-h_label' => array(),
		'data-h_taxonomy' => array(),
		'data-h_attribute' => array(),
		'data-v_taxonomy' => array(),
		'data-v_attribute' => array(),
		'data-attribute_count' => array(),
		'data-product_variations' => array(),
		'data-price' => array(),
		'data-product_id' => array(),
		'data-v_label' => array(),
		'min' => array(),
		'max' => array(),
		'srcset' => array(),
		'data-id' => array(),
		'data-order-id' => array()
	);
	$allowedposttags['form']     = $allowed_atts;
	$allowedposttags['label']    = $allowed_atts;
	$allowedposttags['select']   = $allowed_atts;
	$allowedposttags['option']   = $allowed_atts;
	$allowedposttags['input']    = $allowed_atts;
	$allowedposttags['textarea'] = $allowed_atts;
	$allowedposttags['link'] = $allowed_atts;
	$allowedposttags['button'] = $allowed_atts;
	$allowedposttags['iframe']   = $allowed_atts;
	$allowedposttags['script']   = $allowed_atts;
	$allowedposttags['style']    = $allowed_atts;
	$allowedposttags['strong']   = $allowed_atts;
	$allowedposttags['small']    = $allowed_atts;
	$allowedposttags['table']    = $allowed_atts;
	$allowedposttags['bdi']    = $allowed_atts;
	$allowedposttags['span']     = $allowed_atts;
	$allowedposttags['abbr']     = $allowed_atts;
	$allowedposttags['code']     = $allowed_atts;
	$allowedposttags['pre']      = $allowed_atts;
	$allowedposttags['div']      = $allowed_atts;
	$allowedposttags['img']      = $allowed_atts;
	$allowedposttags['h1']       = $allowed_atts;
	$allowedposttags['h2']       = $allowed_atts;
	$allowedposttags['h3']       = $allowed_atts;
	$allowedposttags['h4']       = $allowed_atts;
	$allowedposttags['h5']       = $allowed_atts;
	$allowedposttags['h6']       = $allowed_atts;
	$allowedposttags['ol']       = $allowed_atts;
	$allowedposttags['ul']       = $allowed_atts;
	$allowedposttags['li']       = $allowed_atts;
	$allowedposttags['em']       = $allowed_atts;
	$allowedposttags['hr']       = $allowed_atts;
	$allowedposttags['br']       = $allowed_atts;
	$allowedposttags['tr']       = $allowed_atts;
	$allowedposttags['td']       = $allowed_atts;
	$allowedposttags['p']        = $allowed_atts;
	$allowedposttags['a']        = $allowed_atts;
	$allowedposttags['b']        = $allowed_atts;
	$allowedposttags['i']        = $allowed_atts;
	return $allowedposttags;
}

add_action('rest_api_init', 'register_routes' );
function register_routes() {
	register_rest_route('wc-analytic-data/reports', '/licenses', [
		'methods'             => 'GET',
		'callback'            => 'get_license_table_data',
		'permission_callback' => function () {
				return current_user_can( 'read' );
			},
	]);
}

function get_license_table_data(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lmfwc_licenses';
    $current_date = current_time('mysql');

    // Get parameters from the request
    $after = isset($_REQUEST['after']) ? sanitize_text_field($_REQUEST['after']) : null;
    $before = isset($_REQUEST['before']) ? sanitize_text_field($_REQUEST['before']) : null;

    // Initialize date filter and params
    $date_filter = '';
    $params = [];

    // Validate and set date filter
    if ($after && $before) {
        $date_filter = "DATE(l.created_at) BETWEEN %s AND %s";
        $params[] = $after;
        $params[] = $before;
    } else {
        return new WP_Error('invalid_date_range', __('Both "after" and "before" dates are required.', 'text-domain'), ['status' => 400]);
    }

    /////////////////////////////////////////////////////////	
    // Total Licenses Sold
	$query = $wpdb->prepare(
		"SELECT COUNT(l.order_id) as total_licenses_sold
		FROM {$table_name} l
		INNER JOIN {$wpdb->prefix}posts o ON o.ID = l.order_id
		WHERE l.order_id != ''
		  AND o.post_type = 'shop_order'
		  " . ($date_filter ? " AND {$date_filter}" : ''),
		$params
	);

	//var_dump($query); die;
    $total_licenses_sold = $wpdb->get_var($query);
    $response['total_licenses_sold'] = (int) $total_licenses_sold;
    ////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////////
    // Total Expired Licenses
    $query = $wpdb->prepare(
        "SELECT count(expires_at) as total_expired_licenses FROM $table_name l WHERE l.expires_at IS NOT NULL " . ($date_filter ? " AND {$date_filter}" : ''),
        $params
    );
	
    $query = str_replace("created_at", "expires_at", $query);
	//var_dump($query); die;
    $total_expired_licenses = $wpdb->get_var($query);
    $response['total_expired_licenses'] = (int) $total_expired_licenses;
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////	
    // Total Activations
    $query = $wpdb->prepare(
        "SELECT SUM(times_activated) as total_activations FROM $table_name l WHERE l.times_activated IS NOT NULL " . ($date_filter ? " AND {$date_filter}" : ''),
        $params
    );
	//var_dump($query); die;
    $total_activations = $wpdb->get_var($query);
    $response['total_activations'] = (int) $total_activations;
    ////////////////////////////////////////////////////////////////////////	

    //////////////////////////////////////////////////////////////////////////////
    // Total Application sold
	$query = "SELECT COUNT(*) FROM {$wpdb->prefix}lmfwc_application l" . ($date_filter ? " WHERE {$date_filter}" : '');
	$product_query = $wpdb->prepare($query, $params);
	$row_count = (int) $wpdb->get_var($product_query);
	//var_dump($product_query); die;
    $response['total_application_sold'] = $row_count;
    ///////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    // Total Orders
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta pm INNER JOIN {$wpdb->prefix}posts p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'lmfwc_order_complete'  
        AND pm.meta_value = 1 AND p.post_type = 'shop_order'
        AND p.post_status = 'wc-completed' " . ($date_filter ? " AND {$date_filter}" : ''),
        $params
    );
	
    $query = str_replace("l.created_at", "p.post_date", $query);
    $total_orders = $wpdb->get_var($query);
	//var_dump($total_orders);
    $response['total_orders'] = (int) $total_orders;
    ////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////
    // Total Generators Assigned
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta pm
        INNER JOIN {$wpdb->prefix}posts p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'lmfwc_licensed_product_use_generator'
        AND pm.meta_value = 1 AND p.post_type = 'product'
        AND p.post_status = 'publish' " . ($date_filter ? " AND {$date_filter}" : ''),
        $params 
    );
    $query = str_replace("l.created_at", "p.post_date", $query);
    $total_generators_assigned = $wpdb->get_var($query);
    $response['total_generators_assigned'] = (int) $total_generators_assigned;
    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////
    // Total Licenses
    $query = $wpdb->prepare(
        "SELECT count(*) as all_licenses FROM $table_name l WHERE id IS NOT NULL " . ($date_filter ? " AND {$date_filter}" : ''),
        $params 
    );
    $all_licenses = $wpdb->get_var($query);
    $response['all_licenses'] = (int) $all_licenses;
    /////////////////////////////////////////////////////////////////////////

    // License Certificate Counter
    $license_certificate_counter = (int) get_option('license_certificate_counter', 0);
    $response['total_certificates_downloaded'] = $license_certificate_counter;

	$query = "
		SELECT 
			l.product_id,
			p.post_title,
			COUNT(DISTINCT l.order_id) AS total_order,
			COUNT(*) AS total_licenses,
			SUM(l.times_activated) AS times_activated,
			SUM(l.order_id_price) AS total_order_sum
				FROM (
					SELECT DISTINCT 
					l.product_id,
					l.order_id,
					CAST(pm.meta_value AS DECIMAL(10,2)) AS order_id_price,
					l.times_activated
					FROM 
					{$wpdb->prefix}lmfwc_licenses l
					INNER JOIN 
					{$wpdb->prefix}posts o ON l.order_id = o.ID AND o.post_type = 'shop_order'
					LEFT JOIN 
					{$wpdb->prefix}postmeta pm ON l.product_id = pm.post_id AND pm.meta_key = '_price'
					WHERE 
					l.order_id IS NOT NULL 
					AND l.product_id IS NOT NULL 
					AND l.user_id IS NOT NULL
					" . ($date_filter ? " AND {$date_filter}" : '') . "
				) AS l
		LEFT JOIN 
			{$wpdb->prefix}posts p ON l.product_id = p.ID
		GROUP BY 
		l.product_id
		ORDER BY 
		total_order_sum DESC;
	";

    // Prepare and execute the query
    $product_query = $wpdb->prepare($query, $params);
	//var_dump($product_query); die;
    $results = $wpdb->get_results($product_query, ARRAY_A);
    $response['sales_license_by_products'] = $results;

    return new WP_REST_Response($response, 200);
}

add_action('init', function () {
    add_filter('woocommerce_rest_prepare_shop_order_object', 'add_lmfwc_meta_to_line_items', 999, 3);
});

function add_lmfwc_meta_to_line_items($response, $object, $request)
{
    // Retrieve the line items from the order object
    $line_items = $object->get_items('line_item');

    // Initialize an array to store updated line items
    $updated_line_items = [];

    foreach ($line_items as $item_id => $item) {
        // Get the existing line item data
        $line_item_data = $item->get_data();
		
		$licenses = LicenseResourceRepository::instance()->findAllBy(
			array(
				'order_id' => $line_item_data["order_id"],
				'product_id' => $line_item_data["product_id"],
			)
		);
	 
		foreach ($licenses as $license) {
			$line_item_data['lmfwc_licensed_product'] = $license->getDecryptedLicenseKey();
		}
        $updated_line_items[] = $line_item_data;
    }

    // Add the updated line items to the API response
    $response->data['line_items'] = $updated_line_items;

    return $response;
}

function add_column_if_not_exists($table, $column, $column_definition) {
	global $wpdb;
	$column_exists = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM $table LIKE %s", $column));
	if (empty($column_exists)) {
		$wpdb->query("ALTER TABLE $table ADD COLUMN $column_definition");
	}
}