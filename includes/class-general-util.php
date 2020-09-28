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
     * Retrieves keys of the table 
     * @return void
     */
    function getTableKey($table){
        global $wpdb;
        $sql = "SELECT COLUMN_NAME FROM   information_schema.key_column_usage 
        WHERE  table_schema = schema() AND constraint_name = 'PRIMARY' AND table_name = '$table'";
        return $wpdb->get_row($sql);
    }

    /**
     * Retrieves keys of the table 
     * @return void
     */
    function getTableColumns($table){
        global $wpdb;
        $sql = "SHOW COLUMNS FROM $table WHERE Extra <> 'auto_increment'";
        return $wpdb->get_results($sql);
    }

    /**
     * Checks for wordpress shortcode and renders it based on number of Api List 
     * Example: [AJDT api="sha1"]
     * @return void
     */
    function render_shortcode(){
        // echo do_shortcode("[AJDT api='sha1']");
        // echo do_shortcode("[AJDT api='sha2']");
        // echo do_shortcode("[AJDT api='sha3']");
        // echo do_shortcode("[AJDT api='sha7']");
        // foreach ($apiList as $key => $Api) {
        //     echo do_shortcode("[AJDT api='$key']");
        // }
?>


<div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="table-name" class="col-form-label">API Name:</label>
                <select class="form-control" id="api-name">
                    <option value='all'>All</option>
                <?php $apiList = get_option(APILISTNAME);
                    foreach($apiList as $k => $v){
                        echo "<option value='$k'>$k</option>";
                    } ?>
                </select>
                
              </div>      
            </div>
            <div class="col-md-2">
                <br>
                <br>
              <button type="button" id="btnTestApi" class="btn btn-success">Render API Data</button>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                   <div class="input-group mb-3">
                    <select class="custom-select" id="inputGroupSelect02">
                        <option selected>Choose...</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                    <div class="input-group-append">
                        <button type="button" id="btnTestApi" class="btn btn-success">Render API Data</button>
                    </div>
                    </div>      
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="primary-key" class="col-form-label">Primary Key:</label>
                <input type="text" class="form-control" id="primary-key" disabled> </input>
              </div>
            </div>
          </div>          
        </div>

<?php
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
            $tableColumns = getTableColumns(get_option(APILISTNAME)[$api]['TableName']);
            $cols ='';
            foreach($tableColumns as $column){
                $key = $column->Field;
                $cols = empty($cols) ? $key: "$cols,$key";
            }
        }

        if(empty($cols)){
            echo show_error("The requested API(name: $api) doesn't exists or http method 'GET' might be missing...!. 
                        Please check for valid APIs in 'AjaxTable Settings' page");
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
                $httpMethods = $v['MethodName'];
                if(strpos($httpMethods, 'GET') !== false){
                    $isExist = true;
                    break;
                }
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