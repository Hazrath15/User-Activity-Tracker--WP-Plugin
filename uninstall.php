<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; 
}

global $wpdb;
$cache_key = 'my_cache_key';
$data = wp_cache_get($cache_key);

if ($data === false) {
    $data = $wpdb->get_results("SELECT * FROM `$table_name`");
    wp_cache_set($cache_key, $data, 'my_cache_group', 3600);
}

?>