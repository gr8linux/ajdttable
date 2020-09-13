<?php

    /**
     * Retrieves all the tables 
     * @return void
     */
    function getTables(){
        global $wpdb;
        $sql = "SHOW TABLES LIKE '%%'";
        return $wpdb->get_results($sql);
    }

    /**
     * Checks for wordpress shortcode and renders it based on number of Api List 
     * Example: [AJDT api="sha1"]
     * @return void
     */
    function render_shortcode(){
        // echo do_shortcode("[AJDT api='sha1']");
        echo do_shortcode("[AJDT api='sha2']");
        // echo do_shortcode("[AJDT api='sha3']");
        // echo do_shortcode("[AJDT api='sha7']");
        // foreach ($apiList as $key => $Api) {
        //     echo do_shortcode("[AJDT api='$key' allapi='$AllKeys']");
        // }
    }

    /**
     * Handels shortcode request by rendering HTML component 
     * @return html component which mounts the data table
     */
    function handle_shortcode($atts) { 
        $api = $atts['api'];
        $allapi = get_allapi_names();  //AjaxTable\ApiCache()::init()->get_allapi_names(); 
        $colNames = get_column_names($api);
        if(empty($colNames))
            return '';

        return "<div id='mount_$api'>
                    <input type='hidden' name='apiNames' value='$allapi'></input>
                    <input type='hidden' name='colNames' value='$colNames'></input>
                    <table id='table_$api' ></table>
                </div>"; 
    }

    /**
     * Gets the columns names from API
     * @return comma seperated string
     */
    function get_column_names($api) { 
        $cols = '';
        if(is_api_exists($api)){
            $fullURL = get_site_url().'/wp-json/'.get_option(APILISTNAME)[$api]['Url'];
            $response = wp_remote_get($fullURL);
            $body = wp_remote_retrieve_body( $response );
            $restData = json_decode( $body );
            if(empty($restData)) {
                echo show_error("No data found in the api(name: $api)..!");
                return $cols;
            }
            foreach ($restData[0] as $key => $object) {
                $cols = empty($cols) ? $key: "$cols,$key";
            }
        }

        if(empty($cols)){
            echo show_error("The requested API(name: $api) doesn't exists...!. 
                        Please check APIs list in the 'AjaxTable Settings' page");
            return $cols;
        }
        
        return $cols; 
    }

    /**
     * Gets API names
     * @return comma seperated string
     */
    function get_allapi_names() { 
        $apiList = get_option(APILISTNAME);
        return implode(',', array_keys($apiList));
    }

    /**
     * Checks if the requested API exists or not
     * @return boolean value
     */
    function is_api_exists($api) { 
        $apiList = get_option(APILISTNAME);
        $isExist = false;
        foreach($apiList as $k => $v){
            if($k==$api){
                $isExist = true;
                break;
            }
        }
        return $isExist;
    }

    /**
     * Displays error message
     * @return error component
     */
    function show_error($msg){
        return "<div id='notice' class='error'><p>$msg</p></div>";
    }