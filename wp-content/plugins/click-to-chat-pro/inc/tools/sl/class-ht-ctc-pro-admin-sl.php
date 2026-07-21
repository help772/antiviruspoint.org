<?php
/**
 * software licence fields
 * 	 activate, deactivate ..
 * 	doing in ajax way
 * 
 * @included sl.php
 * @only: this calls only in Click-to-Chat admin settings pages only
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_Admin_SL' ) ) :

class HT_CTC_PRO_Admin_SL {

    public function __construct() {
        $this->hooks();
    }

    // Hooks
    function hooks() {

		// software licence box
        add_action( 'ht_ctc_ah_admin_sidebar_contact', [$this, 'license_field'] );

		// ajax .. 
        add_action( 'wp_ajax_ctc_pro_activate_license', [$this, 'activate_license'] );
        add_action( 'wp_ajax_ctc_pro_deactivate_license', [$this, 'deactivate_license'] );
        // add_action( 'wp_ajax_nopriv_ctc_pro_activate_license', [$this, 'activate_license'] );

        // conversation api
        // add_action( 'wp_ajax_ctc_pro_capi', [$this, 'conversion_api'] );
		
    }

    // function conversion_api() {

    //     $post_data = ($_POST) ? map_deep( $_POST, 'sanitize_text_field' ) : '';
        
    //     $nonce = isset($post_data['nonce']) ? esc_attr( $post_data['nonce'] ) : 'unknown';
        
    //     if ( ! wp_verify_nonce( $nonce, 'ht_ctc_nonce' ) ) {
    //         return;
    //     }

    //     $options = get_option('ht_ctc_othersettings');

    //     $api_version = "v14.0";
    //     $pixel_id = isset($options['pixel_id']) ? esc_attr( $options['pixel_id'] ) : '';
    //     $token = isset($options['token']) ? esc_attr( $options['token'] ) : '';
        
    //     if ( '' == $pixel_id || '' == $token ) {
    //         return;
    //     }
        
    //     $pixel_id = str_replace(' ', '', $pixel_id);
    //     $token = str_replace(' ', '', $token);
        
    //     $event_name = isset($options['event_name']) ? esc_attr( $options['event_name'] ) : '';
    //     $test_event_code = isset($options['test_event_code']) ? esc_attr( $options['test_event_code'] ) : '';

    //     if ( '' == $event_name ) {
    //         $event_name = 'Click to Chat by HoliThemes';
    //     }

    //     $date = new DateTime();
    //     $time = $date->getTimestamp();

    //     $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';

    //     $url = isset($post_data['url']) ? esc_attr( $post_data['url'] ) : 'unknown';
    //     $number = isset($post_data['number']) ? esc_attr( $post_data['number'] ) : 'unknown';
    //     $title = isset($post_data['title']) ? esc_attr( $post_data['title'] ) : 'unknown';

    //     $api_url = "https://graph.facebook.com/$api_version/$pixel_id/events?access_token=$token";

    //     /**
    //      * client_user_agent
    //      * action_source
    //      * event_source_url
    //      */
    //     $data = [
    //         'action_source' => 'website',
    //         'event_name' => "'.$event_name.'",
    //         'event_source_url' => "' . $url . '",
    //         'event_time' => $time,
    //         'user_data' => [
    //             'client_user_agent' => "' . $agent . '",
    //             'em' => 'f660ab912ec121d1b1e928a0bb4bc61b15f5ad44d5efdc4e1c92a25e99b8e44a'
    //         ],
    //     ];
        
    //     $data = json_encode($data);

    //     $args = [
    //         'body' => [
    //             'data' => [$data],
    //             // 'test_event_code' => 'TEST77610',
    //             'Category' => 'Click to Chat for WhatsApp',
    //             'return_type' => 'chat',
    //             'ID' => $number,
    //             'Title' => $title,
    //             'URL' => $url
    //         ],
    //     ];

    //     // test_event_code
    //     if ( '' !== $test_event_code ) {
    //         $args['body']['test_event_code'] = $test_event_code;
    //     }


    //     $response = wp_remote_post( $api_url, $args );

    //     $response_body = ($response['body']) ? $response['body'] : '';
        
    //     $r = array(
    //         'message' => 'ctc',
    //         'response_body' => $response_body,
    //     );
        
    //     wp_send_json_success( $r );

    //     // Ends early - this wont runs
    //     wp_die();

    // }


	function license_field() {

        $license_key = get_option('ht_ctc_pro_license_key');
        $license_status = get_option('ht_ctc_pro_license_status');

        $key = (false !== $license_key) ? esc_attr( $license_key ) : '';
        $status = (false !== $license_status) ? esc_attr( $license_status ) : '';
        $li_class = 'active';

        $home_url = home_url();

        $key_styles = '';
        $after_activated_styles = '';
        $getlicense_class = '';
        $btn_class = 'ctc_pro_activate_btn';
        if ( 'valid' == $status ) {
            $text = __( 'Deactivate License', 'click-to-chat-for-whatsapp');
            $key_styles = 'display: none;';
            $btn_class = 'ctc_pro_deactivate_btn';
            $getlicense_class = 'ctc_init_display_none';
            $li_class = '';
        } else {
            $text = __( 'Activate License', 'click-to-chat-for-whatsapp');
            $btn_class = 'ctc_pro_activate_btn';
            $after_activated_styles = 'display: none;';
        }
        ?>

        <div class="col s12 m8 l12 xl12">
            <div class="row">
                <ul class="collapsible popout">
                    <li class="<?= $li_class ?>">
                        <div class="collapsible-header"><?php _e( 'Click to Chat Pro - Software License', 'click-to-chat-for-whatsapp' ); ?>
                            <span class="right_icon dashicons dashicons-arrow-down-alt2"></span>
                        </div>
                        <div class="collapsible-body">
                            <form action="" method="post">
                                <p class="description ctc_pro_license_message" style="display:none; margin-bottom: 5px;"></p>

                                <input type="text" required id="ctc_pro_license_key" name="ht_ctc_pro_license_key" value="<?= $key ?>" style="<?= $key_styles ?>" placeholder="License Key" >
                                <input type="text" id="ctc_pro_activated" value="Activated" style="color: green; <?= $after_activated_styles ?>" readonly >

                                <?php wp_nonce_field( 'ht_ctc_pro_nonce', 'ht_ctc_pro_nonce' ); ?>

                                <input type="submit" name="<?= $btn_class ?>" id="ctc_pro_license_button" class="<?= $btn_class ?> button-secondary" value="<?= $text ?>"/>

                                <p class="ctc_get_license <?= $getlicense_class ?>">
                                    <a href="https://holithemes.com/plugins/click-to-chat/pricing" target="_blank"><?php _e( 'Get License', 'click-to-chat-for-whatsapp' ); ?></a> | <a href="https://holithemes.com/shop/" target="_blank"><?php _e( 'My Account', 'click-to-chat-for-whatsapp' ); ?></a>
                                </p>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }


	
    function activate_license() {

        check_ajax_referer( 'ht_ctc_pro_nonce', 'ht_ctc_pro_nonce' );
        
        $key = ($_POST && $_POST['key']) ? $_POST['key'] : '';

        $license = trim($key);
        $license = esc_attr($key);
        
        $home_url = home_url();

        update_option( 'ht_ctc_pro_license_key', $license);

        // // add site url - useful if user changed the domain name and to deactivate.
        // update_option( 'ht_ctc_pro_license_site', $home_url);

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'    => HT_CTC_PRO_SL_ITEM_ID, // this is better then adding name
			'item_name'  => urlencode( HT_CTC_PRO_SL_ITEM_NAME ), // the name of our product
            'url'        => $home_url
		);

		// Call the custom API.
		$response = wp_remote_post( HT_CTC_PRO_SL_STORE_URL, array( 'timeout' => 45, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again. (not 200)' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), HT_CTC_PRO_SL_ITEM_ID );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again. (default) 1. might be another product license key' );
						break;
				}

			} 
            
		}
        
        // $license_data->license will be either "valid" or "invalid"
		update_option( 'ht_ctc_pro_license_status', $license_data->license );

        if ( empty( $message ) ) {
            $message = 'Activated';
		}

        $r = array(
            'message' => $message,
            'store_response' => $response,
        );

        $this->call_plugin_data('Activating License');

		if ( 'activated' == $message ) {
            wp_send_json_success($r);
		} else {
            wp_send_json_error($r);
        }

        // Ends early - this wont runs
        wp_send_json_success( $message );
        wp_die();
    }


    function call_plugin_data($call_back = 'Activating License') {
        if (class_exists('HT_CTC_PRO_Register')) {
            HT_CTC_PRO_Register::plugin_data($call_back);
        }
    }




    // deactivate
    function deactivate_license() {

        check_ajax_referer( 'ht_ctc_pro_nonce', 'ht_ctc_pro_nonce' );

        // retrieve the license from the database
        $license = trim( get_option( 'ht_ctc_pro_license_key' ) );

        $home_url = home_url();

        // // if home_url and saved url is not same.. (i.e. if domain changed..)
        // $ht_ctc_pro_license_site = get_option( 'ht_ctc_pro_license_key' );
        // if ( '' !== $ht_ctc_pro_license_site && $home_url !== $ht_ctc_pro_license_site ) {
        //     $home_url = $ht_ctc_pro_license_site;
        //     update_option( 'ht_ctc_pro_license_site', $home_url);
        // }
        
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license,
            'item_id'    => HT_CTC_PRO_SL_ITEM_ID, // this is better then adding name
            'item_name'  => urlencode( HT_CTC_PRO_SL_ITEM_NAME ), // the name of our product
            'url'        => $home_url
        );
        // Send the remote request
        $response = wp_remote_post( HT_CTC_PRO_SL_STORE_URL, array( 'body' => $api_params, 'timeout' => 45, 'sslverify' => false ) );


        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

            if ( is_wp_error( $response ) ) {
                $message = $response->get_error_message();
            } else {
                $message = __( 'An error occurred, please try again. (not 200)' );
            }

        } else {

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if ( false === $license_data->success ) {

                switch( $license_data->error ) {

                    case 'expired' :

                        $message = sprintf(
                            __( 'Your license key expired on %s.' ),
                            date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                        );
                        break;

                    case 'disabled' :
                    case 'revoked' :

                        $message = __( 'Your license key has been disabled.' );
                        break;

                    case 'missing' :

                        $message = __( 'Invalid license.' );
                        break;

                    case 'invalid' :
                    case 'site_inactive' :

                        $message = __( 'Your license is not active for this URL.' );
                        break;

                    case 'item_name_mismatch' :

                        $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), HT_CTC_PRO_SL_ITEM_ID );
                        break;

                    case 'no_activations_left':

                        $message = __( 'Your license key has reached its activation limit.' );
                        break;

                    default :

                        $message = __( 'An error occurred, please try again. (default) 1. might be another product license key' );
                        break;
                }

            }

        }

        // $license_data->license will be either "valid" or "invalid"
        update_option( 'ht_ctc_pro_license_status', $license_data->license );
        // exit();
        
        if ( empty( $message ) ) {
            $message = 'Deactivated';
		}

        $r = array(
            'message' => $message,
            'store_response' => $response,
        );

        $this->call_plugin_data('Deactivating License');

		if ( 'deactivated' == $message ) {
            wp_send_json_success($r);
		} else {
            wp_send_json_error($r);
        }

        // Ends early - this wont runs
        wp_send_json_success( $message );
        wp_die();
    }





}

new HT_CTC_PRO_Admin_SL();

endif; // END class_exists check