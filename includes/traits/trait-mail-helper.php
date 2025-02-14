<?php
// if( !trait_exists('UACT_Trait_Helper') ) {
//     trait UACT_Mail_Trait_Helper {
//         public function uact_send_email_notification( $subject, $message, $to_email, $current_user, $action, $modified_date ) {
//             // Retrieve the custom email template from the database
//             $email_template = get_option( 'uact_email_template', '' );
        
//             // If no custom template is set, use the default message
//             if ( empty( $email_template ) ) {
//                 $email_template = "A new activity has been recorded:\n\n{message}";
//             }
        
//             // Replace placeholders with actual values
//             $placeholders = array(
//                 '{username}' => $current_user, // Replace with the actual username
//                 '{action}'   => $action,       // Replace with the actual action
//                 '{date}'     => $modified_date,// Replace with the actual date
//                 '{message}'  => $message,      // Replace with the actual message
//             );
        
//             // Replace placeholders in the template
//             $processed_message = str_replace(
//                 array_keys( $placeholders ),
//                 array_values( $placeholders ),
//                 $email_template
//             );
        
//             // Send the email
//             $headers = array( 'Content-Type: text/html; charset=UTF-8' );
//             wp_mail( $to_email, $subject, $processed_message, $headers );
//         }
//     }
// }
?>