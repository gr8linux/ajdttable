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
        echo do_shortcode("[AJDT api='sha1']");
        // echo do_shortcode("[AJDT api='sha2']");
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
        $apiList = get_option(APILISTNAME);
        $allapi = implode(',', array_keys($apiList));
        //print_r("API Name requested is : $api");
        //return "<div id='mount_$api' api='$api' allapi='$allapi' />"; 
        return "<div id='mount_$api'>
                    <input type='hidden' value='$allapi'></input>
                    <table id='table_$api' ></table>
                </div>"; 
    }

    function handle_shortcode1($atts) { 
        $api = $atts['api'];
        $allapi = $atts['allapi'];
        //print_r("API Name requested is : $api");
        //return "<div id='mount_$api' api='$api' allapi='$allapi' />"; 
        return "<div id='mount_$api'>
                    <input type='hidden' name='AllApi' value='$allapi'>
                    <table data-toggle='table'>
                        <thead>
                            <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Item Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>1</td>
                            <td>Item 1</td>
                            <td>$1</td>
                            </tr>
                            <tr>
                            <td>2</td>
                            <td>Item 2</td>
                            <td>$2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>"; 
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