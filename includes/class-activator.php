<?php
if( !class_exists('UACT_Activator') ){
    class UACT_Activator {
        public static function activate() {
            new UACT_Database_Handler();
        }
    }
}

?>