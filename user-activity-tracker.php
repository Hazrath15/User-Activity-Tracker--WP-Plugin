<?php
/*
* Plugin Name: User Activity Tracker
* Description: This is first plugin for learning. From this plugin the plugin development lession is starting.
* Version: 1.0.0
* Author: Hazrath Ali
* Author URI: https://github.com/Hazrath15
* License: GPL-2.0+
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: user-activity-tracker
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'UACT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once UACT_PLUGIN_DIR . 'includes/class-loader.php';
UACT_Autoloader::init();

register_activation_hook(__FILE__,['UACT_Activator', 'activate']);
register_deactivation_hook(__FILE__,['UACT_Deactivator', 'deactivate']);
?>