<?php
if( !class_exists('UACT_Database_Handler') ) {
    class UACT_Database_Handler {
        public function __construct() {
            $this->create();
        }
        public function create() {
            global $wpdb;
            $table_name = $wpdb->prefix . "uact_user_activity";
            $charset_collate = $wpdb->get_charset_collate();
        
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}uact_user_activity'" ) != $table_name ) {
                $sql = "CREATE TABLE $table_name (
                uactid bigint(20) unsigned NOT NULL auto_increment,
                post_id int(20) unsigned NOT NULL,
                post_title varchar(250) NOT NULL,
                user_id bigint(20) unsigned NOT NULL default '0',
                user_name varchar(50) NOT NULL,
                user_role varchar(50) NOT NULL,
                user_email varchar(50) NOT NULL,
                ip_address varchar(50) NOT NULL,
                modified_date datetime NOT NULL default '0000-00-00 00:00:00',
                object_type varchar(50) NOT NULL default 'post',
                action varchar(50) NOT NULL,
                PRIMARY KEY (uactid)) $charset_collate;";
                
                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                dbDelta( $sql );
            }

        }
        
        public function uact_user_activity_add( $post_id, $post_title, $obj_type, $current_user_id, $current_user, $user_role, $user_mail, $modified_date, $ip, $action ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'uact_user_activity';
            $post_title = addslashes( $post_title );
            if ( '' == $obj_type ) {
                $obj_type = 'post';
            }
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $insert_query = $wpdb->insert(
                $table_name,
                array(
                    'post_id'       => $post_id,
                    'post_title'    => $post_title,
                    'user_id'       => $current_user_id,
                    'user_name'     => $current_user,
                    'user_role'     => $user_role,
                    'user_email'    => $user_mail,
                    'ip_address'    => $ip,
                    'modified_date' => $modified_date,
                    'object_type'   => $obj_type,
                    'action'        => $action,
                )
            );
            // Send email notification
            $admin_email = get_option( 'admin_email' ); // Get admin email
            $subject = "Activity Tracker: $action on $obj_type";
            $message = "A new activity has been recorded:<br><br>";
            $message .= "<strong>Action:</strong> $action<br>";
            $message .= "<strong>Object Type:</strong> $obj_type<br>";
            $message .= "<strong>Post Title:</strong> $post_title<br>";
            $message .= "<strong>User:</strong> $current_user ($user_role)<br>";
            $message .= "<strong>Date:</strong> $modified_date<br>";
            $message .= "<strong>IP Address:</strong> $ip<br>";

            $this->uact_send_email_notification( $subject, $message, $admin_email, $current_user, $action, $modified_date );
        }

        public static function delete() {
            global $wpdb;
            $table_name = $wpdb->prefix . "uact_user_activity";
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}uact_user_activity'" ) == $table_name ) {
                $wpdb->query("DROP TABLE IF EXISTS $table_name");
            }
        }
    }
}

?>