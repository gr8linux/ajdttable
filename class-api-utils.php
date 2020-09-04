<?php
 //https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
class ClassApiUtils extends WP_REST_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        // // Init REST API routes.
        // add_action( 'rest_api_init', [ $this, 'register_rest_routes' ], 10 );
        $this->register_routes();
    }
  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    //http://localhost/wp55/wp-json/ajdt/v1/utility
    $namespace = 'ajdt/v1';
    $base = 'utility';

    register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_items' ),
        'permission_callback' => array( $this, 'get_items_permissions_check' ),
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( true ),
      ),
    ) );
    register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_item' ),
        'permission_callback' => array( $this, 'get_item_permissions_check' ),
        'args'                => array(
          'context' => array(
            'default' => 'view',
          ),
        ),
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array( $this, 'update_item' ),
        'permission_callback' => array( $this, 'update_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( false ),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
        'args'                => array(
          'force' => array(
            'default' => false,
          ),
        ),
      ),
    ) );
    register_rest_route( $namespace, '/' . $base . '/schema', array(
      'methods'  => WP_REST_Server::READABLE,
      'callback' => array( $this, 'get_public_item_schema' ),
    ) );
  }
 
  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_items( $request ) {
    //global $wpdb;
    //$table_Student = $wpdb->prefix.'wp55_ajdt_utils'; 
    //$items = $wpdb->get_results($wpdb->prepare("SELECT *  FROM wp55_ajdt_utils"), ARRAY_A);

    $data = get_option('AJDT_API_LIST');
    return new WP_REST_Response($data, 200 );
  }
 
  /**
   * Get one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_item( $request ) {
    //get parameters from request
    $params = $request->get_params();
    //$data = $this->prepare_item_for_response( $item, $request );
    // global $wpdb;
    // $table_Student = $wpdb->prefix.'vjpt_students'; 
    // $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_Student WHERE id=%d", $params['id']), ARRAY_A);
    // $data =$item;

    $data = get_option('AJDT_API_LIST');

    //return a response or error based on some conditional
    if ( 1 == 1 ) {
      return new WP_REST_Response( $data, 200 );
    } else {
      return new WP_Error( 'code', __( 'message', 'text-domain' ) );
    }
  }
 
  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_item( $request ) {
      if ( !isset( $request['name'] ) ) {
          return new WP_Error( 'cant-create', __( 'Api Name is required..', 'text-domain' ), array( 'status' => 500 ) );
      }

      if ( !isset( $request['table'] ) ) {
          return new WP_Error( 'cant-create', __( 'DB Table Name is required..', 'text-domain'), array( 'status' => 500 ) );
      }

      $methods = array("GET", "POST");
      if ( !isset( $request['method'] ) ) {
          return new WP_Error( 'cant-create', __( 'HTTP method is required.', 'text-domain' ), array( 'status' => 500 ) );
      } else if (!in_array($request['method'], $methods)){
          return new WP_Error( 'cant-create', __( 'Invalid HTTP method. Permitted HTTP methods are GET and POST.', 'text-domain' ), array( 'status' => 500 ) );
      }

      $list = get_option('AJDT_API_LIST');

      $list[$request['name']] =  array(
                          "TableName" => $request['table'],
                          "MethodName" => $request['method'],
                          "SelectedColumn" => 'name,age,email',
                          "ConditionColumn" => '',
                          "SelectedCondtion" => 'no condition',
                          "SelectedParameter" => 1,
                          "query" => 'Select * from '.$request['table'].';'
                  );
      update_option('AJDT_API_LIST', $list);
      $data = get_option('AJDT_API_LIST');

      if ( is_array( $data ) ) {
        return new WP_REST_Response( $data, 200 );
      }
 
    return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }
 
  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
 
    if ( function_exists( 'slug_some_function_to_update_item' ) ) {
      $data = slug_some_function_to_update_item( $item );
      if ( is_array( $data ) ) {
        return new WP_REST_Response( $data, 200 );
      }
    }
 
    return new WP_Error( 'cant-update', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }
 
  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_item( $request ) {
    $item = $this->prepare_item_for_database( $request );
 
    if ( function_exists( 'slug_some_function_to_delete_item' ) ) {
      $deleted = slug_some_function_to_delete_item( $item );
      if ( $deleted ) {
        return new WP_REST_Response( true, 200 );
      }
    }
 
    return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }
 
  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_items_permissions_check( $request ) {
    return true; //<--use to make readable by all
    //return current_user_can('administrator');
  }
 
  /**
   * Check if a given request has access to get a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_item_permissions_check( $request ) {
    return $this->get_items_permissions_check( $request );
  }
 
  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_item_permissions_check( $request ) {
    return true; //current_user_can( 'edit_something' );
  }
 
  /**
   * Check if a given request has access to update a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function update_item_permissions_check( $request ) {
    return $this->create_item_permissions_check( $request );
  }
 
  /**
   * Check if a given request has access to delete a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function delete_item_permissions_check( $request ) {
    return $this->create_item_permissions_check( $request );
  }
 
  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database( $request ) {
    return array();
  }
 
  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response( $item, $request ) {
    return array();
  }
 
  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function get_collection_params() {
    return array(
      'page'     => array(
        'description'       => 'Current page of the collection.',
        'type'              => 'integer',
        'default'           => 1,
        'sanitize_callback' => 'absint',
      ),
      'per_page' => array(
        'description'       => 'Maximum number of items to be returned in result set.',
        'type'              => 'integer',
        'default'           => 10,
        'sanitize_callback' => 'absint',
      ),
      'search'   => array(
        'description'       => 'Limit results to those matching a string.',
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ),
    );
  }
}