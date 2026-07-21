<?php

namespace WcPaysafe\Gateways\Redirect;

use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015-2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Abstracted_Gateway extends \WC_Payment_Gateway {
	
	public $available_cc;
	/**
	 * @var Checkout\Processes|Hosted\Processes
	 */
	protected $integration_object;
	
	public function get_integration_options() {
		return apply_filters( 'wc_paysafe_redirect_integration_options', array(
			'checkoutjs' => __( 'Checkout API', 'wc_paysafe' ),
			'hosted'     => __( 'Hosted Payments API', 'wc_paysafe' ),
		) );
	}
	
	/**
	 * Filter the gateway icon according to the accepted cards option
	 *
	 * @return string The Card images in a html format
	 */
	function get_icon() {
		if ( $this->available_cc ) {
			$icon = '';
			foreach ( $this->available_cc as $card ) {
				if ( file_exists( Paysafe::plugin_path() . '/assets/images/' . strtolower( $card ) . '.png' ) ) {
					$icon .= '<img src="' . esc_url( \WC_HTTPS::force_https_url( Paysafe::plugin_url() . '/assets/images/' . strtolower( $card ) . '.png' ) ) . '" alt="' . esc_attr( strtolower( $card ) ) . '" />';
				}
			}
		} else {
			$icon = '<img src="' . esc_url( \WC_HTTPS::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';
		}
		
		return $icon;
	}
	
	/**
	 * Returns the integration object
	 *
	 * @return Checkout\Processes|Hosted\Processes
	 */
	public function get_integration_object() {
		do_action( 'wc_paysafe_redirect_main_integration_object', $this );
		
		if ( null === $this->integration_object ) {
			if ( 'checkoutjs' == $this->get_option( 'integration', 'hosted' ) ) {
				$this->set_integration_object( new Checkout\Processes( $this ) );
			} else {
				$this->set_integration_object( new Hosted\Processes( $this ) );
			}
		}
		
		return $this->integration_object;
	}
	
	/**
	 * Sets the integration object
	 *
	 * @param $object
	 */
	public function set_integration_object( $object ) {
		$this->integration_object = $object;
	}
	
	/**
	 * Return the integration specific settings
	 *
	 * @return array
	 */
	public function get_integration_settings() {
		$integration = $this->get_integration_object();
		
		return $integration->get_settings();
	}
	
	/**
	 * Initialise Gateway Settings Form Fields
	 **/
	public function init_form_fields() {
		
		// Get the integration type
		$options = get_option( 'woocommerce_' . $this->id . '_settings', array() );
		if ( ! empty( $options['integration'] ) ) {
			$integration = $options['integration'];
		} else {
			$integration = 'hosted';
		}
		
		$this->form_fields = [];
		if ( 'netbanx' == $this->id ) {
			$this->form_fields = [
				'integration' => array(
					'title'             => __( 'Integration Type', 'wc_paysafe' ),
					'type'              => 'select',
					'description'       => __( 'Choose the API integration you want to connect and Save Settings.', 'wc_paysafe' ) . '<br/> <span class="paysafe_warning"></span>',
					'options'           => $this->get_integration_options(),
					'class'             => 'chosen_select',
					'css'               => 'min-width:350px;',
					'default'           => 'checkoutjs',
					'custom_attributes' => array(
						'data-initial-type' => $integration,
					),
				),
				
				'integration_type_settings_end' => array(
					'title'       => '<hr/>',
					'type'        => 'title',
					'description' => '',
				),
			];
		}
		
		/**
		 * @deprecated 'wc_netbanx_settings_form_fields' will be removed, use 'wc_paysafe_settings_form_fields'
		 */
		$this->form_fields += array(
			'enable_settings_start' => array(
				'type'        => 'title',
				'description' => '',
			),
			
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'wc_paysafe' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Paysafe', 'wc_paysafe' ),
				'default' => 'no',
			),
			
			'testmode' => array(
				'title'   => __( 'Sandbox', 'wc_paysafe' ),
				'type'    => 'select',
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Sandbox Mode' ),
					'no'  => __( 'Live Mode', 'wc_paysafe' ),
				),
			),
		);
		
		if ( 'netbanx' == $this->id ) {
			$this->form_fields += [
				'hide_for_payments' => array(
					'title'       => __( 'Hide for Checkout Payments', 'wc_paysafe' ),
					'type'        => 'select',
					'default'     => 'no',
					'description' => __( 'This option will remove the payment method from the checkout page and any new payment screens, but will keep the method active in case of subscription recurring payments.', 'wc_paysafe' ),
					'options'     => array(
						'yes' => __( 'Hidden' ),
						'no'  => __( 'Displayed', 'wc_paysafe' ),
					),
				),
			];
		}
		
		$this->form_fields +=
			array(
				
				'enable_settings_end' => array(
					'title'       => '<hr/>',
					'type'        => 'title',
					'description' => '',
				),
				
				'general_settings_start' => array(
					'title'       => __( 'General Settings', 'wc_paysafe' ),
					'type'        => 'title',
					'description' => '',
				),
				
				'title'                => array(
					'title'       => __( 'Method Title', 'wc_paysafe' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'wc_paysafe' ),
					'default'     => __( 'Paysafe', 'wc_paysafe' ),
				),
				'description'          => array(
					'title'       => __( 'Description', 'wc_paysafe' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'wc_paysafe' ),
					'default'     => __( "Pay using your credit/debit card.", 'wc_paysafe' ),
				),
				'general_settings_end' => array(
					'title'       => '<hr/>',
					'type'        => 'title',
					'description' => '',
				),
			);
		
		// Add the integration settings
		$this->form_fields += $this->get_integration_settings();
		
		$this->form_fields += array(
			'debug_testmode_settings_start' => array(
				'title' => __( 'Test and Debug Settings', 'wc_paysafe' ),
				'type'  => 'title',
			),
			
			'debug' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Enable Debug mode', 'wc_paysafe' ),
				'default'     => 'no',
				'description' => sprintf(
					__(
						'Debug logs the plugin processes for easier troubleshooting. Logged inside %s'
					),
					'<code>' . _x( 'WooCommerce > Status > Logs >', 'debug log location', 'wc_paysafe' ) . ' ' . \WC_Log_Handler_File::get_log_file_name( 'paysafe' ) . '</code>'
				),
			),
		);
		
		$this->form_fields = apply_filters(
			'wc_netbanx_settings_form_fields',
			$this->form_fields
		);
		
		$this->form_fields = apply_filters(
			'wc_paysafe_settings_form_fields',
			$this->form_fields,
			$this->id
		);
	} // End init_form_fields()
	
	/**
	 * Validate Password Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 * We are not showing the password value to the front end,
	 * so we will overwrite the password validation, so we can update the password only when it is not empty.
	 * If left empty the password will be saved with the old value.
	 *
	 * @access public
	 *
	 * @param mixed $key
	 * @param mixed $value
	 *
	 * @since  1.1
	 * @return string
	 */
	public function validate_password_field( $key, $value ) {
		$text = $this->get_option( $key );
		
		if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) && '' != $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) {
			$text = wc_clean( stripslashes( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) );
		}
		
		return $text;
	}
	
	/**
	 * Generate Password Input HTML.
	 * Overwrite here so it is accessible for WC 2.0
	 *
	 * @access public
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @since  1.1
	 * @return string
	 */
	public function generate_password_html( $key, $data ) {
		$data['type'] = 'password';
		
		return $this->generate_text_html( $key, $data );
	}
	
	/**
	 * Generate Text Input HTML.
	 * Modify the text html to remove the password value from the front end.
	 *
	 * @access public
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @since  1.1
	 * @return string
	 */
	public function generate_text_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);
		
		$data  = wp_parse_args( $data, $defaults );
		$value = $this->get_option( $key );
		
		// Passwords will not have the exact password displayed,
		// so lets add a placeholder letting the user know that the password is set or not.
		$is_password_field = 'password' == $data['type'];
		$placeholder       = '' != $data['placeholder'] ? $data['placeholder'] : $data['title'];
		if ( $is_password_field ) {
			$data['placeholder'] = sprintf( _x( '%s is not set', 'password field placeholder', 'wc_paysafe' ), $placeholder );
			if ( '' != $value ) {
				$data['placeholder'] = sprintf( _x( '%s is set', 'password field placeholder', 'wc_paysafe' ), $placeholder );
			}
			$value = '';
		}
		
		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo Formatting::kses_form_html( $data['title'] ); ?>
					<?php echo Formatting::kses_form_html( $this->get_tooltip_html( $data ) ); ?>
				</label>

			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo Formatting::kses_form_html( $data['title'] ); ?></span>
					</legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo Formatting::kses_form_html( $this->get_description_html( $data ) ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Generate the custom reset button field HTML.
	 *
	 * @access public
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @since  2.3
	 * @return string
	 */
	public function generate_reset_button_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'action'            => __( 'Reset Profiles', 'wc_paysafe' ),
		);
		
		$data = wp_parse_args( $data, $defaults );
		
		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo Formatting::kses_form_html( $this->get_tooltip_html( $data ) ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<label for="woocommerce_paysafe_reset_profile">
						<input class="" id="woocommerce_paysafe_reset_profile" value="1" style="" type="checkbox">
						<?php echo esc_html( __( 'Show Reset Profiles', 'wc_paysafe' ) ); ?>
						<?php echo $this->get_description_html(
							array(
								'desc_tip'    => false,
								'description' => __( 'Check the checkbox and show the reset button', 'wc_paysafe' ),
							)
						); ?>
					</label>
					<div class="wc-paysafe-reset-profiles-wrapper" style="display: none;">
						<?php echo $this->get_description_html( $data ); ?>
						<a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php?action=wc_paysafe_reset_profiles' ), 'wc-paysafe-reset' ); ?>" class="button button-primary wc-paysafe-reset-profiles">
							<?php echo esc_html( $data['action'] ); ?>
						</a>
					</div>
				</fieldset>
				<script type="application/javascript">
					<?php // @formatter:off ?>
					(function ($) {
						$(document).ready(function () {
							$('body').on('change', '#woocommerce_paysafe_reset_profile', function () {
								if ($(this).attr('checked')) {
									$(".wc-paysafe-reset-profiles-wrapper").show();
								} else {
									$(".wc-paysafe-reset-profiles-wrapper").hide();
								}
							})
							.on('click', '.wc-paysafe-reset-profiles', function () {
								if (!window.confirm('<?php echo esc_js(__( 'Are you sure you want to reset customer profiles?', 'wc_paysafe' )); ?>')) {
									return false;
								}
							});
						});
					})(jQuery);
					<?php // @formatter:on ?>
				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Get HTML for tooltips
	 * Overwrite here so it is accessible for WC 2.0
	 *
	 * @since 1.1
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_tooltip_html( $data ) {
		if ( $data['desc_tip'] === true ) {
			$tip = $data['description'];
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$tip = $data['desc_tip'];
		} else {
			$tip = '';
		}
		
		return $tip ? wc_help_tip( $tip, true ) : '';
	}
	
	/**
	 * Get HTML for descriptions
	 * Overwrite here so it is accessible for WC 2.0
	 *
	 * @since 1.1
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_description_html( $data ) {
		if ( $data['desc_tip'] === true ) {
			$description = '';
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$description = $data['description'];
		} elseif ( ! empty( $data['description'] ) ) {
			$description = $data['description'];
		} else {
			$description = '';
		}
		
		return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
	}
	
	/**
	 * Get custom attributes
	 * Overwrite here so it is accessible for WC 2.0
	 *
	 * @since 1.1
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_custom_attribute_html( $data ) {
		$custom_attributes = array();
		
		if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {
			foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		
		return implode( ' ', $custom_attributes );
	}
}