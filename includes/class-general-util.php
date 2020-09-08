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

    /**
     * Checks for wordpress shortcode and renders it based on number of Api List 
     * Sample: [AJDT api="sha1" allapi="sha1,sha2,sha3,sha4"]
     * @return void
     */
    function render_shortcode(){
        $apiList = get_option(APILISTNAME);
        $AllKeys = implode(',', array_keys($apiList));
        foreach ($apiList as $key => $Api) {
            echo do_shortcode("[AJDT api='$key' allapi='$AllKeys']");
        }
    }

    /**
     * Handels shortcode request by rendering HTML component 
     * 
     * @return void
     */
    function handle_shortcode($atts) { 
        $api = $atts['api'];
        $allapi = $atts['allapi'];
        //print_r("API Name requested is : $api");
        return "<div id='mount_$api' api='$api' allapi='$allapi' />"; 
    }
