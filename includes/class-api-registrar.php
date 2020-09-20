<?php
namespace AjaxTable;

/**
 * API_Registrar class
 */
class API_Registrar {
    /**
     * Constructor
     */
    public function __construct() {
        // // Init REST API routes.
        // add_action( 'rest_api_init', [ $this, 'register_rest_routes' ], 10 );
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

        switch ($value['MethodName']) {
            case 'GET': 
                register_rest_route(API_NAMESPACE, $base, [
                    'methods'  => 'GET',
                    'callback' => array( $this, 'process_api_get' ),
                    'args' => $value
                ]);
                break;
            case 'POST': echo 'Not Implemented'; break;
            case 'PUT': echo 'Not Implemented'; break;
            case 'DELETE': echo 'Not Implemented'; break;
        }
      }
    }

    /**
     * Processes GET Request
     */
    function process_api_get($request) {
        global $wpdb;
        //print_r($request);
        $need =  $request->get_attributes();
        $GetQuery = $need['args']['Query'];
        $SelectedCondtion = $need['args']['SelectedCondtion'];
        if (($SelectedCondtion == 'no condition')) {
            $data = $wpdb->get_results("{$GetQuery}");
            return $data;
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
            // echo $MainQuery;
            if ('&amp;gt;' == $SelectedCondtion)
                $SelectedCondtion = '>';
            if ('less than' == $SelectedCondtion)
                $SelectedCondtion = '<';
            $SelectedCondtion = $SelectedCondtion.' ';
            // echo $SelectedCondtion;
            // echo $request['id'];
            $data = $wpdb->get_results("{$MainQuery} {$SelectedCondtion} {$param}");

            return $data;
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
