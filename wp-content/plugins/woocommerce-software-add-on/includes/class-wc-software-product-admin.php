<?php

/**
 * WC_Software_Product_admin class.
 */
class WC_Software_Product_Admin {

	var $product_fields;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Hooks
		add_action( 'woocommerce_product_options_product_type', array( $this, 'is_software' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_write_panel' ) );
		add_filter( 'woocommerce_process_product_meta', array( $this, 'product_save_data' ) );

		// New product type option - 1.6.2
		add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
	}

	public function product_type_options( $options ) {
		$options['is_software'] = array(
			'id'            => '_is_software',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Software', 'woocommerce' ),
			'description'   => __( 'Enable this option if this is software (and you want to manage license keys)', 'woocommerce' ),
		);
		return $options;
	}

	public function define_fields() {

		if ( $this->product_fields ) {
			return;
		}

		// Fields
		$this->product_fields = array(
			'start_group',
			array(
				'id'          => '_software_product_id',
				'label'       => __( 'Product ID', 'woocommerce-software-add-on' ),
				'description' => __( 'This ID is used for the license key API.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'e.g. SOFTWARE1', 'woocommerce-software-add-on' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_license_key_prefix',
				'label'       => __( 'License key prefix', 'woocommerce-software-add-on' ),
				'description' => __( 'Optional prefix for generated license keys.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'N/A', 'woocommerce-software-add-on' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_secret_product_key',
				'label'       => __( 'Secret key', 'woocommerce-software-add-on' ),
				'description' => __( 'Secret Product Key to use  for API.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'any random string', 'woocommerce-software-add-on' ),
				'type'        => 'text',
				'default'     => substr( str_shuffle( MD5( microtime() ) ), 0, 32 ),
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_version',
				'label'       => __( 'Version', 'woocommerce-software-add-on' ),
				'description' => __( 'Version number for the software.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'e.g. 1.0', 'woocommerce-software-add-on' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_activations',
				'label'       => __( 'Activation limit', 'woocommerce-software-add-on' ),
				'description' => __( 'Amount of activations possible per license key.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'Unlimited', 'woocommerce-software-add-on' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
			'end_group',
			'start_group',
			array(
				'id'          => '_software_upgradable_product',
				'label'       => __( 'Upgradable product', 'woocommerce-software-add-on' ),
				'description' => __( 'Name of the product which can be upgraded.', 'woocommerce-software-add-on' ),
				'placeholder' => '',
				'type'        => 'text',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_upgrade_price',
				'label'       => __( 'Upgrade Price', 'woocommerce-software-add-on' ) . ' ( ' . get_woocommerce_currency_symbol() . ' )',
				'description' => __( 'Users with a valid upgrade key will be able to pay this amount.', 'woocommerce-software-add-on' ),
				'placeholder' => __( 'e.g. 10.99', 'woocommerce-software-add-on' ),
				'class'       => 'wc_input_price short',
				'type'        => 'text',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_upgrade_license_keys',
				'label'       => __( 'Valid upgrade keys', 'woocommerce-software-add-on' ),
				'description' => __( 'A comma separated list of keys which can be upgraded.', 'woocommerce-software-add-on' ),
				'placeholder' => '',
				'type'        => 'textarea',
				'desc_tip'    => true,
			),
			array(
				'id'          => '_software_used_license_keys',
				'label'       => __( 'Used upgrade keys', 'woocommerce-software-add-on' ),
				'description' => __( 'A comma separated list of keys which have been used for an upgrade already.', 'woocommerce-software-add-on' ),
				'placeholder' => '',
				'type'        => 'textarea',
				'desc_tip'    => true,
			),
			'end_group',
		);

	}

	/**
	 * is_software function.
	 */
	public function is_software() {

		woocommerce_wp_checkbox(
			array(
				'id'            => '_is_software',
				'wrapper_class' => 'show_if_simple',
				'label'         => __( 'Software', 'woocommerce' ),
				'description'   => __(
					'Enable this option if this is software (and you want to manage license keys)',
					'woocommerce'
				),
			)
		);

	}

	/**
	 * adds a new tab to the product interface
	 */
	public function product_write_panel_tab() {
		?>
		<li class="software_tab show_if_software"><a href="#software_data"><span><?php _e( 'Software', 'woocommerce-software-add-on' ); ?></span></a></li>
		<?php
	}

	/**
	 * adds the panel to the product interface
	 */
	public function product_write_panel() {
		global $post;

		$this->define_fields();

		$data = get_post_meta( $post->ID, 'product_data', true );
		?>
		<div id="software_data" class="panel woocommerce_options_panel">
		<?php
		foreach ( $this->product_fields as $field ) {

			if ( ! is_array( $field ) ) {

				if ( $field == 'start_group' ) {
					echo '<div class="options_group">';
				} elseif ( $field == 'end_group' ) {
					echo '</div>';
				}
			} else {

				$func = 'woocommerce_wp_' . $field['type'] . '_input';

				if ( function_exists( $func ) ) {
					$func( $field );
				}
			}
		}
		?>
		</div>
		<?php

		$javascript = "

			jQuery('input#_is_software').on( 'change', function(){

				jQuery('.show_if_software').hide();

				if ( jQuery('#_is_software').is(':checked') ) {
					jQuery('.show_if_software').show();
				} else {
					if ( jQuery('.software_tab').is('.active') ) jQuery('ul.tabs li:visible').eq(0).find('a').trigger( 'click' );
				}

			}).trigger( 'change' );

		";

		wc_enqueue_js( $javascript );
	}

	/**
	 * saves the data inputed into the product boxes into a serialized array
	 */
	public function product_save_data() {
		global $post;

		$this->define_fields();

		if ( ! empty( $_POST['_is_software'] ) ) {
			update_post_meta( $post->ID, '_is_software', 'yes' );
		} else {
			update_post_meta( $post->ID, '_is_software', 'no' );
		}

		foreach ( $this->product_fields as $field ) {

			if ( is_array( $field ) ) {

				$data = isset( $_POST[ $field['id'] ] ) ? esc_attr( trim( stripslashes( $_POST[ $field['id'] ] ) ) ) : '';

				update_post_meta( $post->ID, $field['id'], $data );

			}
		}

	}

}

$GLOBALS['WC_Software_Product_Admin'] = new WC_Software_Product_Admin(); // Init
