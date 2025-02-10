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
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            global $wpdb;
            $table_name = $wpdb->prefix . 'uact_user_activity';
            $table_name = esc_sql($table_name);
            $query = "SELECT * FROM `$table_name` ORDER BY modified_date DESC LIMIT %d";
            $results = $wpdb->get_results($wpdb->prepare($query, 50));

            ?>
            <div class="wrap">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">User</th>
                            <th width="15%">Email</th>
                            <th width="10%">Role</th>
                            <th width="15%">Action</th>
                            <th width="20%">Description</th>
                            <th width="15%">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ( ! empty( $results ) ) { ?>
                    <?php foreach ( $results as $row ) { ?>
                        <tr>
                            <td><?php echo esc_html( $row->uactid ); ?></td>
                            <td><?php echo esc_html( $row->user_name ); ?></td>
                            <td><?php echo esc_html( $row->user_email ); ?></td>
                            <td><?php echo esc_html( $row->user_role ); ?></td>
                            <td><?php echo esc_html( ucfirst( $row->action ) ); ?></td>
                            <td><?php echo esc_html( $row->post_title ) ; ?></td>
                            <td><?php echo esc_html( gmdate( 'Y-m-d H:i:s', strtotime( $row->modified_date ) ) ); ?></td>
                        </tr>
                    <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        public function uact_settings_dashboard() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            ?>
            <div class="wrap">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            </div>
            <?php
        }
        
    }
}

?>