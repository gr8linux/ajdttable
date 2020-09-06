<?php
namespace AjaxTable;

/**
 * Admin Pages Handler
 */
class GeneralUtil {

    public function __construct() {}
    
    /**
     * Initializes the WeDevs_ERP() class
     *
     * Checks for an existing WeDevs_ERP() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Retrieves all the tables 
     *
     * @return void
     */
    public function getTables(){
        global $wpdb;
        $sql = "SHOW TABLES LIKE '%%'";
        return $wpdb->get_results($sql);
    }

    
}
