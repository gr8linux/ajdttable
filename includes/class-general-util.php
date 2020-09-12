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
        // echo do_shortcode("[AJDT api='sha1']");
        echo do_shortcode("[AJDT api='sha2']");
        // echo do_shortcode("[AJDT api='sha3']");
        // echo do_shortcode("[AJDT api='sha4']");
        // foreach ($apiList as $key => $Api) {
        //     echo do_shortcode("[AJDT api='$key' allapi='$AllKeys']");
        // }
    }

    /**
     * Handels shortcode request by rendering HTML component 
     * 
     * @return void
     */
    function handle_shortcode($atts) { 
        $api = $atts['api'];
        $allapi = get_allapi_names();  //AjaxTable\ApiCache()::init()->get_allapi_names(); 
        $colNames = get_column_names($api);
        if(empty($colNames))
            return "<div id='notice' class='error'><p>The requested API doesn't exists...!. 
            Please check valid APIs in 'AjaxTable Settings' page</p></div>";

        return "<div id='mount_$api'>
                    <input type='hidden' name='apiNames' value='$allapi'></input>
                    <input type='hidden' name='colNames' value='$colNames'></input>
                    <table id='table_$api' ></table>
                </div>"; 
    }

    function get_column_names($api) { 
        $cols = '';
        if(is_api_exists($api)){
            $fullURL = get_site_url().'/wp-json/'.get_option(APILISTNAME)[$api]['Url'];
            $restData = json_decode( wp_remote_retrieve_body( wp_remote_get($fullURL) ) );
            $cols = '';
            foreach ($restData[0] as $key => $object) 
                $cols = "$cols,$key";
        }
        return $cols; 
    }


    function get_allapi_names() { 
        $apiList = get_option(APILISTNAME);
        return implode(',', array_keys($apiList));
    }

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
     * Handels shortcode request by rendering HTML component 
     * 
     * @return void
     */
    function handle_shortcode_vuejs($atts) { 
        $api = $atts['api'];
        $allapi = $atts['allapi'];
        //print_r("API Name requested is : $api");
        //return "<div id='mount_$api' api='$api' allapi='$allapi' />"; 
        return "<div id='appp'><div id='mount_$api' api='$api' allapi='$allapi'>
                    <h3>Vue Component Goes Here</h3>
                    <p>
                        <router-link to='/foo'>Go to Foo</router-link><br>
                        <router-link to='/bar'>Go to Bar</router-link><br>
                        <router-link to='/dataitems'>Go to Items</router-link><br>
                    </p>
                    <router-view></router-view>
                </div></div>"; 
    }