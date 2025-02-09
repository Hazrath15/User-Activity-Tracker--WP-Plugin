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