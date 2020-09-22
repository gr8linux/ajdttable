<?php
//namespace AjaxTable;

/**
 * API_Registrar class
 */
class API_Registrar extends WP_REST_Controller {
    /**
     * Constructor
     */
    public function __construct() {
        // // Init REST API routes.
        $this->register_ajdt_routes();
    }

    /**
     * Registers all the routes
     */
    function register_ajdt_routes(){
      $apiList = get_option(APILISTNAME);
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

    /**
     * Process HTTP Methods
     */
    function process_routes($methods, $base, $args) {
        foreach(explode(",", $methods) as $method) {
            switch ($method) {
                case 'GET': 
                    register_rest_route(API_NAMESPACE, $base,  [
                            'methods'             => WP_REST_Server::READABLE,
                            'callback'            => array( $this, 'ajdt_get_items' ),
                            'permission_callback' => array( $this, 'ajdt_get_items_permissions_check' ),
                            'args'                => $args,
                         ] );
                    break;
                case 'POST':
                    register_rest_route(API_NAMESPACE, $base,  [
                            'methods'             => WP_REST_Server::CREATABLE,
                            'callback'            => array( $this, 'ajdt_create_item' ),
                            'permission_callback' => array( $this, 'ajdt_create_item_permissions_check' ),
                            'args'                => [
                                'schema' => $this->get_endpoint_args_for_item_schema( true ),
                                'args' => $args
                            ],
                         ] );
                    break;
                case 'PUT': break;
                case 'DELETE':  
                    register_rest_route(API_NAMESPACE, $base. '/(?P<keyId>[\w]+)',  [
                            'methods'             => WP_REST_Server::DELETABLE,
                            'callback'            => array( $this, 'ajdt_delete_item' ),
                            'permission_callback' => array( $this, 'ajdt_delete_item_permissions_check' ),
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
    return true; //<--use to make readable by all
    //return current_user_can('administrator');
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
    * Check if a given request has access to create items
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function ajdt_create_item_permissions_check( $request ) {
        return true;
        //return current_user_can('administrator'); //current_user_can( 'edit_something' );
    }

    /**
    * Check if a given request has access to delete a specific item
    *
    * @param WP_REST_Request $request Full data about the request.
    * @return WP_Error|bool
    */
    public function ajdt_delete_item_permissions_check( $request ) {
        return $this->ajdt_create_item_permissions_check( $request );
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
        $keyId = $request->get_params()['keyId'];
        $table = $attrs['args']['TableName'];
        $primaryKey = $attrs['args']['PrimaryKey'];

        
        try {

            //$cols = getTableColumns($table);
            $insertKeyValues = [];
            foreach(getTableColumns($table) as $column){
                array_push($insertKeyValues, $column->Field);
            }
            return new WP_REST_Response($insertKeyValues, 200 );

            global $wpdb;
            $insertKeyValues = [
                "prod_id" => $val['id'],
                "quantity" => 0,
                "created_at" => date("Y-m-d")
            ];

            $result = $wpdb->insert($table, $insertKeyValues);
            if($result)
                return new WP_REST_Response("Inserted successfully. No of rows affected: $result", 200 );
            else
                return new WP_Error( 'cant-insert', __('Invalid records for insertion, no rows added', 'text-domain' ), array( 'status' => 501 ) );

        } catch (Exception $e) {
            return new WP_Error( 'cant-insert', __('Error! '. $wpdb->last_error, 'text-domain' ), array( 'status' => 500 ) );
        }

        if ( is_array( $data ) ) {
            return new WP_REST_Response( $data, 200 );
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

    /**
     * Register REST API routes.
     *
     * @since 1.2.0
     */
    // public function register_rest_routes() {
    //     $controllers = [
    //         '\WeDevs\ERP\API\Utility_Controller'
    //     ];

    //     if ( erp_is_module_active( 'crm' ) ) {
    //         $controllers = array_merge( $controllers, [
    //             '\WeDevs\ERP\API\Contacts_Controller',
    //             '\WeDevs\ERP\API\Contacts_Groups_Controller',
    //             '\WeDevs\ERP\API\Activities_Controller',
    //             '\WeDevs\ERP\API\Schedules_Controller',
    //         ] );
    //     }

    //     $controllers = apply_filters( 'erp_rest_api_controllers', $controllers );

    //     foreach ( $controllers as $controller ) {
    //         $controller = new $controller();
    //         $controller->register_routes();
    //     }
    // }
}
