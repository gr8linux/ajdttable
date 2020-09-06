<?php

    /**
     * Retrieves all the tables 
     *
     * @return void
     */
    function getTables(){
        global $wpdb;
        $sql = "SHOW TABLES LIKE '%%'";
        return $wpdb->get_results($sql);
    }
