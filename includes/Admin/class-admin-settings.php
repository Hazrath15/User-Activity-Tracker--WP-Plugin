<?php
if( !class_exists('UACT_Admin_Settings') ) {
    class UACT_Admin_Settings {
        public function __construct() {
            add_action('admin_menu', [ $this, 'register_admin_menu' ]);
        }
        public function register_admin_menu() {
            add_menu_page(
                'User Activity Tracker',
                'Activity Tracker',
                'manage_options',
                'uact-admin-dashboard',
                [ $this, 'uact_admin_dashboard' ],
                'dashicons-welcome-view-site'
            );
            add_submenu_page(
                'uact-admin-dashboard',
                'Settings',
                'Settings',
                'manage_options',
                'uact-settings',
                [ $this, 'uact_settings_dashboard' ],
            );
        }
        public function uact_admin_dashboard() {
            echo 'This is test page';
        }
        public function uact_settings_dashboard() {
            echo 'This is setting page';
        }
        
    }
}

?>