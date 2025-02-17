<?php
use Traits\UACT_Activity_Helper;
use Traits\UACT_Mail_Trait_Helper;

if( !class_exists('UACT_Logger') ) {
    class UACT_Logger extends UACT_Database_Handler {
        use UACT_Activity_Helper;
        use UACT_Mail_Trait_Helper;

        public function __construct() {
            add_action( 'wp_login', [ $this, 'uact_user_wp_login' ], 20, 2 );
            add_action( 'wp_insert_comment', [ $this, 'uact_wp_insert_comment' ] );
            add_action( 'post_updated', [ $this, 'track_post_action' ], 10, 3 );
            add_action( 'wp_trash_post', [ $this, 'track_post_deletion' ] );
            add_action( 'untrash_post', [ $this, 'track_post_restore' ] );
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