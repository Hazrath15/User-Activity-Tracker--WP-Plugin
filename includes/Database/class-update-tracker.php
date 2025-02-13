<?php
if( !class_exists('UACT_Update_Tracker') ) {
    class UACT_Update_Tracker {
        public function __construct() {
            add_action( 'wp_login', [ $this, 'uact_user_wp_login' ], 20, 2 );
            add_action( 'wp_insert_comment', [ $this, 'uact_wp_insert_comment' ] );
            add_action( 'post_updated', [ $this, 'track_post_action' ], 10, 3 );
            add_action( 'wp_trash_post', [ $this, 'track_post_deletion' ] );
            add_action( 'untrash_post', [ $this, 'track_post_restore' ] );
        }

        public function send_activity_notification() {
            $admin_email = get_option( 'uact_admin_gmail', get_bloginfo('admin_email') );
            $email_template = get_option( 'uact_email_template', 'User {username} performed {action} on {date}.' );
        
            // Replace placeholders with actual values
            $message = 'test message';
        
            $subject = "User Activity Notification";
            $headers = ['Content-Type: text/html; charset=UTF-8'];
        
            wp_mail( $admin_email, $subject, $message, $headers );
        }   
        
        // public function send_activity_notification( $user_name, $action, $date ) {
        //     // Get the admin email from settings
        //     $admin_email = get_option( 'uact_admin_gmail', '' );
        
        //     // Fallback if the setting is empty
        //     if ( empty( $admin_email ) ) {
        //         $admin_email = get_bloginfo('admin_email');
        //     }
        
        //     // Debug: Check if admin email is being retrieved
        //     error_log('Admin Email: ' . $admin_email);
        
        //     // Retrieve the email template
        //     $email_template = get_option( 'uact_email_template', 'User {username} performed {action} on {date}.' );
        
        //     // Replace placeholders with actual values
        //     $message = str_replace(
        //         ['{username}', '{action}', '{date}'],
        //         [$user_name, $action, $date],
        //         $email_template
        //     );
        
        //     $subject = "User Activity Notification";
        //     $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        //     // Debug: Check if wp_mail() executes successfully
        //     if ( wp_mail( $admin_email, $subject, $message, $headers ) ) {
        //         error_log('Email sent successfully to: ' . $admin_email);
        //     } else {
        //         error_log('Failed to send email to: ' . $admin_email);
        //     }
        // }        

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
        }

        public function uact_get_activity_function( $action, $obj_type, $post_id, $post_title ) {
            $current_user_id           = '';
            $current_user_display_name = '';
            $user_mail                 = '';
            $user_role                 = '';
            $modified_date             = '';
            $modified_date             = current_time( 'mysql' );
            $ip                        = '';
            $current_user_id = get_current_user_id();
            $user            = new WP_User( $current_user_id );
            $user_mail       = $user->user_email;
            global $wp_roles;
            $role_name = array();
            if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
                foreach ( $user->roles as $user_r ) {
                    $role_name[] = $wp_roles->role_names[ $user_r ];
                }
                $user_role = implode( ', ', $role_name );
            }
    
            $current_user_display_name = $user->display_name;
            $this->uact_user_activity_add( $post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action );
        }

        public function uact_user_wp_login( $user_login, $user ) {
            global $wpdb;
            $action          = 'logged in';
            $obj_type        = 'user';
            $user_mail       = $user->user_email;
            $current_user_id = $user->ID;
            $user            = new WP_User( $current_user_id );
            if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
                foreach ( $user->roles as $role ) {
                    $user_role = $role;
                }
            }
            $post_id       = $current_user_id;
            $post_title    = $user_login;
            $modified_date = current_time( 'mysql' );
            $ip            = '';
            $current_user_display_name = $user->display_name;
            $this->uact_user_activity_add( $post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action );
        } 

        public function uact_wp_insert_comment( $comment ) {
            $action     = 'insert comment';
            $obj_type   = 'comment';
            $comment_id = $comment;
            $com        = get_comment( $comment_id );
            $post_id    = $com->comment_post_ID;
            $post_link  = get_the_permalink( $post_id );
            $post_title = 'Comment inserted in ' . get_the_title( $post_id );
    
            $this->uact_get_activity_function( $action, $obj_type, $post_id, $post_title );
        }

        public function track_post_action( $post_id, $post, $update ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            if ( 'post' !== $post->post_type ) {
                return;
            }

            $post_title = $post->post_title;
            $obj_type = 'post';
            
            if ( ! $update ) {
                $action = 'create';
                $post_title = 'Created: ' . $post_title;
            } else {
                $action = 'update';
                $post_title = 'Edited: ' . $post_title;
            }

            $this->uact_get_activity_function( $action, $obj_type, $post_id, $post_title );
        }

        public function track_post_deletion( $post_id ) {
            $post = get_post( $post_id );

            if ( 'post' !== $post->post_type ) {
                return;
            }

            $post_title = $post->post_title;
            $obj_type = 'post';
            $action = 'delete';
            $post_title = 'Deleted ' . $post_title;

            $this->uact_get_activity_function( $action, $obj_type, $post_id, $post_title );
            $this->send_activity_notification( );
        }

        public function track_post_restore( $post_id ) {
            // Get post object
            $post = get_post( $post_id );
        
            if ( ! $post || 'post' !== $post->post_type ) {
                return;
            }

            $post_title = 'Restored: ' . $post->post_title;
            $obj_type = 'post';
            $action = 'restore';

            $this->uact_get_activity_function( $action, $obj_type, $post_id, $post_title );
        }
   
    }
}

?>