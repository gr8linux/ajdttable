<?php

/**
 * AjdtApiRegistrar class for API actions
 */
class AjdtApiRegistrar extends WP_REST_Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $this->register_ajdt_routes();
    }

    /**
     * Registers all the routes
     */
    function register_ajdt_routes(){
        $apiList = get_option(AJDT_APILISTNAME);
        if (is_array($apiList) || is_object($apiList)) {
            foreach ($apiList as $ApiName => $value) {
                $base = '';
                if ($value['SelectedCondtion'] == 'no condition') {
                $base = $ApiName;
                } else {
                $base = $ApiName . '/(?P<id>[A-Za-z0-9]+)';
                }

                $this->process_routes($value['MethodName'], $base, $value); 
            }
        }
    }

    /**
     * Process HTTP Methods
     */
    function process_routes($methods, $base, $args) {
        $keyId = '/(?P<keyId>[\w]+)';
        foreach(explode(",", $methods) as $method) {
            switch ($method) {
                case 'GET': 
                    register_rest_route(AJDT_API_NAMESPACE, $base,  [
                            'methods'             => WP_REST_Server::READABLE,
                            'callback'            => array( $this, 'ajdt_get_items' ),
                            'permission_callback' => array( $this, 'ajdt_get_items_permissions_check' ),
                            'args'                => $args,
                         ] );
                    register_rest_route(AJDT_API_NAMESPACE, $base.$keyId,  [
                            'methods'             => WP_REST_Server::READABLE,
                            'callback'            => array( $this, 'ajdt_get_item' ),
                            'permission_callback' => array( $this, 'ajdt_get_items_permissions_check' ),
                            'args'                => $args,
                         ] );
                    break;
                case 'POST':
                    register_rest_route(AJDT_API_NAMESPACE, $base,  [
                            'methods'             => WP_REST_Server::CREATABLE,
                            'callback'            => array( $this, 'ajdt_create_item' ),
                            'permission_callback' => array( $this, 'ajdt_crud_item_permissions_check' ),
                            'args'                => [
                                'schema' => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                                'args' => $args
                            ],
                         ] );
                    break;
                case 'PUT':
                    register_rest_route(AJDT_API_NAMESPACE, $base.$keyId,  [
                            'methods'             => WP_REST_Server::EDITABLE,
                            'callback'            => array( $this, 'ajdt_update_item' ),
                            'permission_callback' => array( $this, 'ajdt_crud_item_permissions_check' ),
                            'args'                => $this->get_collection_params()
                         ] );
                    break;
                case 'DELETE':  
                    register_rest_route(AJDT_API_NAMESPACE, $base.$keyId,  [
                            'methods'             => WP_REST_Server::DELETABLE,
                            'callback'            => array( $this, 'ajdt_delete_item' ),
                            'permission_callback' => array( $this, 'ajdt_crud_item_permissions_check' ),
                            'args'                => $args,
                         ] );
                    break;
            }
        }
    }

    /**
    * Check if a given request has access to get items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function ajdt_get_items_permissions_check( $request ) {
        return current_user_can('administrator');
    }

    /**
    * Check if a given request has access to create items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function ajdt_crud_item_permissions_check( $request ) {
        return current_user_can('administrator'); 
    }

    /**
     * Process GET Request
     */
    function ajdt_get_item($request) {
        $attrs =  $request->get_attributes();
        $table = $attrs['args']['TableName'];
        $primaryKey = $attrs['args']['PrimaryKey'];
        $keyId = $request->get_params()['keyId'];

        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM $table WHERE $primaryKey = $keyId");

        return new WP_REST_Response($result, 200 );
    }

    /**
     * Process GET Request
     */
    function ajdt_get_items($request) {
        global $wpdb;
        $need =  $request->get_attributes();
        $GetQuery = $need['args']['Query'];
        $SelectedCondtion = $need['args']['SelectedCondtion'];
        if (($SelectedCondtion == 'no condition')) {
            $data = $wpdb->get_results("{$GetQuery}");
        } else {
            $Spliting = explode($SelectedCondtion, $GetQuery);
            $MainQuery = $Spliting[0];
            $type = gettype($request['id']);
            if ($type == "string") {
                $param = '"' . $request['id'] . '"';
            }
            if ($type == "integer") {
                $param = $request['id'];
            }
            if ('&amp;gt;' == $SelectedCondtion)
                $SelectedCondtion = '>';
            if ('less than' == $SelectedCondtion)
                $SelectedCondtion = '<';
            $SelectedCondtion = $SelectedCondtion.' ';
            $data = $wpdb->get_results("{$MainQuery} {$SelectedCondtion} {$param}");
        }

        return new WP_REST_Response($data, 200 );
    }

    /**
    * Create one item from the collection
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|WP_REST_Response
    */
    public function ajdt_update_item( $request ) {
        $params = $request->get_params();
        $attrs =  $request->get_attributes()['args'];
        $table = $attrs['args']['TableName'];
        $primaryKey = $attrs['args']['PrimaryKey'];
        $keyId = $request->get_params()['keyId'];
        try {
            $tableColumns = ajdt_get_table_columns($table);
            $validCols = $updateData = [];
            foreach($tableColumns as $column){
                if(!empty($params[$column->Field]))
                    $updateData[$column->Field] = $params[$column->Field];

                $validCols[$column->Field] = "";
            }

            // $vvv['postdata'] = $_POST;
            // $vvv['attrs'] = $request->get_attributes();
            // $vvv['par'] = $request->get_params();
            return new WP_REST_Response("PUT http method is not implemented. Please contact shajeeb.s@gmail.com for support", 200 );

            global $wpdb;
            $result = $wpdb->update($table, $updateData, [ "$primaryKey" => $keyId ]);
            if($result)
                return new WP_REST_Response("Updated successfully. No of rows affected: $result", 200 );
            else
                return new WP_Error( 'cant-insert', __('No rows added. Valid Columns: '.json_encode($validCols), 'text-domain' ), array( 'status' => 501 ) );
        } catch (Exception $e) {
            return new WP_Error( 'cant-insert', __('Error! '. $wpdb->last_error, 'text-domain' ), array( 'status' => 500 ) );
        }

        return new WP_Error( 'cant-insert', __( 'Unexpected exception happened..!', 'text-domain' ), array( 'status' => 500 ) );
    }

    /**
    * Create one item from the collection
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|WP_REST_Response
    */
    public function ajdt_create_item( $request ) {
        $params = $request->get_params();
        $attrs =  $request->get_attributes()['args'];
        $table = $attrs['args']['TableName'];
        $primaryKey = $attrs['args']['PrimaryKey'];
        try {
            $tableColumns = ajdt_get_table_columns($table);
            $validCols = $insertData = [];
            foreach($tableColumns as $column){
                if(!empty($params[$column->Field]))
                    $insertData[$column->Field] = $params[$column->Field];

                $validCols[$column->Field] = "";
            }

            global $wpdb;
            $result = $wpdb->insert($table, $insertData);
            if($result)
                return new WP_REST_Response("Inserted successfully. No of rows affected: $result", 200 );
            else
                return new WP_Error( 'cant-insert', __('No rows added. Valid Columns: '.json_encode($validCols), 'text-domain' ), array( 'status' => 501 ) );
        } catch (Exception $e) {
            return new WP_Error( 'cant-insert', __('Error! '. $wpdb->last_error, 'text-domain' ), array( 'status' => 500 ) );
        }

        return new WP_Error( 'cant-insert', __( 'Unexpected exception happened..!', 'text-domain' ), array( 'status' => 500 ) );
    }

    /**
    * Process DELETE Request
    */
    function ajdt_delete_item($request) {
        $attrs =  $request->get_attributes();
        $keyId = $request->get_params()['keyId'];
        $table = $attrs['args']['TableName'];
        $primaryKey = $attrs['args']['PrimaryKey'];

        try {

            global $wpdb;
            $result = $wpdb->delete($table, [ "$primaryKey" => $keyId ]);
            if($result)
                return new WP_REST_Response("Deleted successfully. No of rows affected: $result", 200 );
            else
                return new WP_Error( 'no-deletion', __('Invalid "id" value, no rows affected', 'text-domain' ), array( 'status' => 501 ) );

        } catch (Exception $e) {
            return new WP_Error( 'no-deletion', __('Error! '. $wpdb->last_error, 'text-domain' ), array( 'status' => 500 ) );
        }
    }
}
