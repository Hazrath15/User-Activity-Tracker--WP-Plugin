<?php
if( !class_exists('UACT_Autoloader') ) {
    class UACT_Autoloader {
        public static function init() {

            //Load Database Handler
            require_once UACT_PLUGIN_DIR . 'includes/Database/class-database-handler.php';
            require_once UACT_PLUGIN_DIR . 'includes/Database/class-update-tracker.php';

            //Load Admin Settings
            require_once UACT_PLUGIN_DIR . 'includes/Admin/class-admin-settings.php';

            //Load Traits 
            require_once UACT_PLUGIN_DIR . 'includes/traits/trait-mail-helper.php';
            require_once UACT_PLUGIN_DIR . 'includes/traits/trait-activity-helper.php';
            
            //Load User Utilities
            require_once UACT_PLUGIN_DIR . 'includes/utilities/class-logger.php';

            // Load Plugin Activator and Deactivator 
            require_once UACT_PLUGIN_DIR . 'includes/class-activator.php';
            require_once UACT_PLUGIN_DIR . 'includes/class-deactivator.php';

            new UACT_Admin_Settings();
            // new UACT_Update_Tracker();
            new UACT_Logger();

        }
    }
}

?>