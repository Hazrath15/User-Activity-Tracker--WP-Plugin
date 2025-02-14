<?php
// trait UACT_Activity_Helper {
//     public function uact_get_activity_function( $action, $obj_type, $post_id, $post_title ) {
//         $current_user_id           = '';
//         $current_user_display_name = '';
//         $user_mail                 = '';
//         $user_role                 = '';
//         $modified_date             = '';
//         $modified_date             = current_time( 'mysql' );
//         $ip                        = '';
//         $current_user_id = get_current_user_id();
//         $user            = new WP_User( $current_user_id );
//         $user_mail       = $user->user_email;
//         global $wp_roles;
//         $role_name = array();
//         if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
//             foreach ( $user->roles as $user_r ) {
//                 $role_name[] = $wp_roles->role_names[ $user_r ];
//             }
//             $user_role = implode( ', ', $role_name );
//         }

//         $current_user_display_name = $user->display_name;
//         $this->uact_user_activity_add( $post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action );
//     }
// }
?>