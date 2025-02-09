<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; 
}

global $wpdb;
$table_name = $wpdb->prefix . "uact_user_activity"; 

// Delete the table
$wpdb->query("DROP TABLE IF EXISTS $table_name");

?>