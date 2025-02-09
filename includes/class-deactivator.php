<?php
if( !class_exists('UACT_Deactivator') ) {
    class UACT_Deactivator {
        public static function deactivate() {
            UACT_Database_Handler::delete();
        }
    }
}

?>