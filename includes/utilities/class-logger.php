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
            add_action( 'edit_post', [ $this, 'track_post_edit' ], 10, 2 );
            add_action( 'wp_insert_post', [ $this, 'track_post_creation' ], 10, 3 );  
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

        public function track_post_creation( $post_id, $post, $update ) {
            $post = get_post( $post_id );
            if ( $update ) {
                return; // Only track new posts
            }
        
            if ( 'post' !== $post->post_type ) {
                return;
            }
        
            // Set a temporary post meta to identify new posts
            update_post_meta( $post_id, '_is_new_post', true );
            $obj_type = 'post';
            $action = 'create';
            $post_title = 'Created ' . $post->post_title;
            $this->uact_get_activity_function( $action, $obj_type, $post_id, $post_title );
        }
              
        public function track_post_edit( $post_id ) {
            // Get the post object
            $post = get_post( $post_id );
        
            // Ensure it's a standard post type
            if ( 'post' !== $post->post_type ) {
                return;
            }
        
            // Check if this is a new post
            $is_new_post = get_post_meta( $post_id, '_is_new_post', true );
        
            // If it's a new post, delete the meta and return (skip logging edit)
            if ( $is_new_post ) {
                delete_post_meta( $post_id, '_is_new_post' );
                return;
            }
        
            $obj_type = 'post';
            $action = 'edit';
            // Track only if it's an edit
            $post_title = 'Edited ' . $post->post_title;
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