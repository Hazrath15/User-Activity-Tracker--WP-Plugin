<?php
if( !class_exists('UACT_Admin_Settings') ) {
    class UACT_Admin_Settings {
        public function __construct() {
            add_action('admin_menu', [ $this, 'register_admin_menu' ]);
            add_action( 'admin_init', [ $this, 'uact_export_activity_csv' ] );
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
            global $wpdb;
            $table_name = $wpdb->prefix . 'uact_user_activity';
        
            // Fetch the latest 50 user activity logs
            $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY modified_date DESC LIMIT 50" );

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
            <form method="get" action="">
                <input type="hidden" name="uact_export_csv" value="1">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'uact_export_csv' ); ?>">
                <p><input type="submit" class="button-secondary" value="Export Activity as CSV"></p>
            </form>

            <?php
            echo get_option( 'uact_admin_gmail', get_bloginfo('admin_email') );
        }
        public function uact_settings_dashboard() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
        
            // Save settings when form is submitted
            if ( isset( $_POST['uact_save_settings'] ) ) {
                check_admin_referer( 'uact_settings_save', 'uact_settings_nonce' );
        
                update_option( 'uact_admin_gmail', sanitize_email( $_POST['uact_admin_gmail'] ) );
                update_option( 'uact_email_template', wp_kses_post( $_POST['uact_email_template'] ) );
        
                echo '<div class="updated"><p>Settings saved successfully.</p></div>';
            }
        
            // Retrieve stored values
            $gmail = get_option( 'uact_admin_gmail', '' );
            $email_template = get_option( 'uact_email_template', '' );
        
            ?>
        
            <div class="wrap">
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        
                <form method="post">
                    <?php wp_nonce_field( 'uact_settings_save', 'uact_settings_nonce' ); ?>
        
                    <table class="form-table">
                        <tr>
                            <th><label for="uact_admin_gmail">Admin Gmail</label></th>
                            <td>
                                <input type="email" name="uact_admin_gmail" id="uact_admin_gmail" value="<?php echo esc_attr( $gmail ); ?>" class="regular-text">
                                <p class="description">Enter the Gmail address to send notifications from.</p>
                            </td>
                        </tr>
        
                        <tr>
                            <th><label for="uact_email_template">Email Template</label></th>
                            <td>
                                <textarea name="uact_email_template" id="uact_email_template" rows="6" class="large-text"><?php echo esc_textarea( $email_template ); ?></textarea>
                                <p class="description">Use {username}, {action}, {date} in the template.</p>
                            </td>
                        </tr>
                    </table>
        
                    <p><input type="submit" name="uact_save_settings" class="button-primary" value="Save Settings"></p>
                </form>
            </div>
        
            <?php
        }
        
        public function uact_export_activity_csv() {
            if ( isset( $_REQUEST['uact_export_csv'] ) ) {
        
                // Verify nonce for security
                if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'uact_export_csv' ) ) {
                    wp_die( 'Security check failed' );
                }
        
                global $wpdb;
                $table_name = $wpdb->prefix . 'uact_user_activity';
        
                // Fetch user activity records
                $activities = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        
                if ( empty( $activities ) ) {
                    wp_die( 'No activity records found.' );
                }
        
                // Prevent any output before headers
                ob_clean();
                header( 'Content-Type: text/csv; charset=utf-8' );
                header( 'Content-Disposition: attachment; filename=user_activity_log.csv' );
                header( 'Pragma: no-cache' );
                header( 'Expires: 0' );
        
                // Open output stream
                $output = fopen( 'php://output', 'w' );
        
                // Add CSV Column Headers
                fputcsv( $output, array( 'ID', 'Post ID', 'Post Title', 'User ID', 'User Name', 'User Role', 'User Email', 'IP Address', 'Date', 'Object Type', 'Action' ) );
        
                // Loop through each activity and write to CSV
                foreach ( $activities as $activity ) {
                    fputcsv( $output, array(
                        $activity['uactid'],
                        $activity['post_id'],
                        $activity['post_title'],
                        $activity['user_id'],
                        $activity['user_name'],
                        $activity['user_role'],
                        $activity['user_email'],
                        $activity['ip_address'],
                        $activity['modified_date'],
                        $activity['object_type'],
                        $activity['action'],
                    ));
                }
        
                fclose( $output );
                exit;
            }
        }
        
    }
}

?>