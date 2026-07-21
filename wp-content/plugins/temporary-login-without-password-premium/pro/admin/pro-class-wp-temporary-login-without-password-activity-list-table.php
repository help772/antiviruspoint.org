<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class TLWP_Activity_Logs_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct(array(
            'singular' => 'Activity Log',
            'plural'   => 'Activity Logs',
            'ajax'     => false,
        ));
    }

    public function get_columns() {
        return array(
            'message'   => 'Message',
            'username'  => 'Username', 
            'object'    => 'Object', 
            'action'    => 'Action', 
            'created_on'=> 'Date',                        
        );
    }

    public function prepare_items() {

        global $wpdb;

        $table = $wpdb->prefix . 'tlwp_activity_logs';
    
        // Pagination parameters
        $per_page     = 20;
        $current_page = $this->get_pagenum();
        $offset       = ($current_page - 1) * $per_page;
        
        $user_id = get_current_user_id();

        if ( current_user_can('administrator') ) {

            // Get total items count
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table");

            $items = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $table ORDER BY created_on DESC LIMIT %d OFFSET %d", $per_page, $offset),
                ARRAY_A
            );

        }  else {

            // Get total items count
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE user_id = ". $user_id );

            $items = $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY created_on DESC LIMIT %d OFFSET %d",
                $user_id, $per_page, $offset
            );
            
        }
 
        $this->_column_headers = array( $this->get_columns(), array(), array() );

        $this->items = $items;

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        )); 
        
    }

    public function column_default($item, $column_name) {

        global $wpdb;

        switch ($column_name) { 
            case 'username':
            case 'action':
            case 'object':
                return esc_html($item[$column_name]);
            case 'created_on':
                return date('M d, Y H:i:s', $item['created_on']);
            case 'message':
                $meta_table = $wpdb->prefix . 'tlwp_activity_logs_meta';
                $message = $wpdb->get_var($wpdb->prepare(
                    "SELECT meta_value FROM $meta_table WHERE activity_log_id = %d AND meta_key = %s",
                    $item['id'], 'message'
                ));
                return esc_html($message); 
            default:
                return print_r($item, true);
        }
    }
}
