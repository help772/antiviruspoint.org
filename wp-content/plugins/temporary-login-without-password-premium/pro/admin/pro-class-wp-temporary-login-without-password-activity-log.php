<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (! class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


if (! class_exists('Wp_Temporary_Login_Without_Password_Activity_Log')) {
    /**
     * Public face of Temporary Login Without Password Pro
     *
     * @package Temporary Login Without Password
     */

    /**
     * Class Wp_Temporary_Login_Without_Password_Activity_Log
     *
     * @package Temporary Login Without Password
     */

    class Wp_Temporary_Login_Without_Password_Activity_Log extends WP_List_Table
    {

        public function __construct()
        {
            $this->init();
        }

        public function init()
        {

            add_filter('tlwp_custom_extra_tab', array( $this, 'add_tlwp_extra_tab' ), 10, 2);

            add_action('tlwp_custom_extra_tab_content', array( $this, 'render_tlwp_extra_tab_content' ) );

            // TLWP user login log
            add_filter('wp_login', array( $this, 'generate_auth_login_log' ), 10);

            // Plugin Activated log
            add_action('activated_plugin', array( $this, 'generate_plugin_activation_log' ), 10, 1);

            // Plugin Deactivated log
            add_action('deactivated_plugin', array( $this, 'generate_plugin_deactivation_log' ), 10, 1);

            // check login and store in session
            add_action('init', array( $this, 'check_user_login_and_store_session') );

            // create logout user log
            add_action('wp_logout', array( $this, 'generate_logout_user_log' ) );
        }

        function generate_logout_user_log() {

            $logged_out_data = isset($_SESSION['tlwp_last_logged_out_user']) ? $_SESSION['tlwp_last_logged_out_user'] : null;

            if ($logged_out_data && ( Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user($logged_out_data['user_id']) == true ) ) {

                $this->tlwp_log_event('User', 'Logout', 'TLWP user logged out', $logged_out_data['user_id'], array(
                    'user_login' => $logged_out_data['user_login'],
                    'email'      => $logged_out_data['user_email'],
                ));

                // Clear session after logging
                unset($_SESSION['tlwp_last_logged_out_user']);
            }
            
        }

        function check_user_login_and_store_session() { 

            if (is_user_logged_in()) {

                $user = wp_get_current_user();

                if ($user && $user->ID && Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user( $user->ID ) == true ) {
                    $_SESSION['tlwp_last_logged_out_user'] = array(
                        'user_login' => $user->user_login,
                        'user_email' => $user->user_email,
                        'user_id'    => $user->ID,
                    );
                }
            } 
        }

        function generate_plugin_deactivation_log($plugin)
        {

            $user_id = get_current_user_id();

            if ( Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user( $user_id ) == true ) {

                if (!function_exists('get_plugin_data')) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }

                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $plugin_name = isset($plugin_data['Name']) ? $plugin_data['Name'] : $plugin;
                $version     = isset($plugin_data['Version']) ? $plugin_data['Version'] : '';
                $author      = isset($plugin_data['Author']) ? $plugin_data['Author'] : '';

                $this->tlwp_log_event('Plugin', 'Deactivated', $plugin_name, null, array(
                    'plugin' => $plugin,
                    'version' => $version,
                    'author' => $author,
                ));
            }
        }

        function generate_plugin_activation_log($plugin)
        {

            $user_id = get_current_user_id();

            if ( Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user( $user_id ) == true ) {

                if (!function_exists('get_plugin_data')) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }

                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $plugin_name = isset($plugin_data['Name']) ? $plugin_data['Name'] : $plugin;
                $version     = isset($plugin_data['Version']) ? $plugin_data['Version'] : '';
                $author      = isset($plugin_data['Author']) ? $plugin_data['Author'] : '';

                $this->tlwp_log_event('Plugin', 'Activated', $plugin_name, null, array(
                    'plugin' => $plugin,
                    'version' => $version,
                    'author' => $author,
                ));
            }
        }       

        function generate_auth_login_log()
        {

            $user_id = get_current_user_id();

            $user = get_user_by('ID', $user_id);

            if ( Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user( $user_id ) == true ) {

                $this->tlwp_log_event('User', 'Login', __('TLWP user logged in'), $user->ID, array(
                    'user_login' => $user->user_login,
                    'email'      => $user->user_email,
                ));
            }
        }

        /**
         * Add log entry 
         */
        function tlwp_log_event($object, $action, $context = '', $user_id = null, $extra_data = array())
        {

            global $wpdb;

            if (!$user_id) {
                $user_id = get_current_user_id();
            }

            $user = get_user_by('ID', $user_id);
            $username = $user ? $user->user_login : 'system';
            $roles = $user && !empty($user->roles) ? implode(', ', $user->roles) : 'no role';

            $table = $wpdb->prefix . 'tlwp_activity_logs';
            $meta_table = $wpdb->prefix . 'tlwp_activity_logs_meta';

            $created_on = current_time('timestamp');

            $log_data = array(
                'username'    => $username,
                'user_roles'  => $roles,
                'user_id'     => $user_id,
                'object'      => $object,
                'action'      => $action,
                'created_on'  => $created_on,
                'client_ip'   => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            );

            if ($object === 'Plugin' && isset($extra_data['plugin'])) {

                $log_data['post_type'] = 'plugin'; // overload safely
                $context = $context ?: $extra_data['plugin'];
            }

            $wpdb->insert($table, $log_data);
            $log_id = $wpdb->insert_id;

            if ($log_id) {

                if (!empty($context)) {
                    $wpdb->insert($meta_table, array(
                        'activity_log_id' => $log_id,
                        'meta_key'        => 'message',
                        'meta_value'      => maybe_serialize($context),
                    ));
                }

                // Save additional meta from $extra_data
                foreach ($extra_data as $key => $value) {
                    if ($key === 'post' || $key === 'plugin' || $key === 'user_login') continue; // already handled
                    $wpdb->insert($meta_table, array(
                        'activity_log_id' => $log_id,
                        'meta_key'        => sanitize_key($key),
                        'meta_value'      => maybe_serialize($value),
                    ));
                }
            }
        }
        

        /**
         * Render extra tab content
         */
        public function render_tlwp_extra_tab_content()
        {

            $is_tlwp_user = get_user_meta(get_current_user_id(), '_wtlwp_user', true);

            if ($is_tlwp_user) {
                return array();
            }

            if (isset($_GET['tab']) && $_GET['tab'] === 'activity-logs') {

                require_once WTLWP_PLUGIN_DIR . '/pro/admin/pro-class-wp-temporary-login-without-password-activity-list-table.php';

                $logs_table = new TLWP_Activity_Logs_Table();
                $logs_table->prepare_items();

                echo '<div class="wrap"><h2>' . esc_html__('Activity Logs Content', 'temporary-login-without-password') . '</h2>'; 
                echo '<form method="get">';
                echo '<input type="hidden" name="page" value="' . esc_attr($_GET['page']) . '">';
                $logs_table->display();
                echo '</form>';

                echo '</div>';
            }
        }

        /**
         * Addd extra tab
         */
        public function add_tlwp_extra_tab($tabs)
        {

            $base_url = admin_url( 'users.php?page=wp-temporary-login-without-password&tab=' );

            $check_user_wtlwp_user = Wp_Temporary_Login_Without_Password_Common::check_user_wtlwp_user( get_current_user_id() ) == true ? true : false;

            $tabs['activity-logs'] = array(
                'url'  => $base_url . 'activity-logs',
                'name' => __( 'Activity Logs', 'temporary-login-without-password' ),
                'visible' => current_user_can('administrator') && ! $check_user_wtlwp_user,  
            );
            
            return $tabs;

        } 
    }
}