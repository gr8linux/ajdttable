<?php
/*
Plugin Name: AjaxTable
Description: AjaxTable is multipurpose data table used for displaying custom data from the database.
Version: 1.1.1
Author: shajeeb
Author URI: https://nimra-tech.com/
Text Domain: nimratech
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * AjaxUtility class
 *
 * @class AjaxUtility The class that holds the entire AjaxUtility plugin
 */
final class AjaxUtility {
    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.1.1';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];
  
    /**
     * Initializes the AjaxUtility() class
     *
     * Checks for an existing AjaxUtility() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new AjaxUtility();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'AJDT_VERSION', $this->version );
        define( 'AJDT_FILE', __FILE__ );
        define( 'AJDT_PATH', dirname( AJDT_FILE ) );
        define( 'AJDT_INCLUDES', AJDT_PATH . '/includes' );
        define( 'AJDT_URL', plugins_url( '', AJDT_FILE ) );//plugin_dir_url( __FILE__ )
        define( 'AJDT_ASSETS', AJDT_URL . '/assets' );
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {
        global $wpdb;
        $table_Utils = $wpdb->prefix.'ajdt_utils'; 

        // Utils details
        $sql = "CREATE TABLE IF NOT EXISTS $table_Utils (
            id int(11) AUTO_INCREMENT PRIMARY KEY,
            name     varchar(50) NOT NULL,
            age     INT NOT NULL,
            email varchar(50)  not null,
            place     varchar(50) NOT NULL,
            created_at date NOT NULL);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $data = [
            ['name' => 'Tarek, Rahman', 'age' => 22, 'email' => 'tarek.rahman@refin.com', 'place' => 'Nottingham', "created_at" => date("Y-m-d")],
            ['name' => 'Farish, George W', 'age' => 21, 'email' => 'george.farish@refin.com', 'place' => 'London', "created_at" => date("Y-m-d")],
            ['name' => 'Arjun, Menon', 'age' => 43, 'email' => 'arjun.menon@refin.com', 'place' => 'Birmingham', "created_at" => date("Y-m-d")],
            ['name' => 'Charles-Love, Nadege', 'age' => 55, 'email' => 'nadege.charles@refin.com', 'place' => 'Liverpool', "created_at" => date("Y-m-d")],
            ['name' => 'Wood, Claire', 'age' => 37, 'email' => 'claire.wood@refin.com', 'place' => 'Manchestor', "created_at" => date("Y-m-d")],
            ['name' => 'Dowell,  Campbell', 'age' => 28, 'email' => 'campbell.dowell@refin.com', 'place' => 'Sounthampton', "created_at" => date("Y-m-d")],
            ['name' => 'Telenkov, Evgenii', 'age' => 43, 'email' => 'evgenii.telenkov@refin.com', 'place' => 'Derby', "created_at" => date("Y-m-d")],
            ['name' => 'Davidson, Brian', 'age' => 65, 'email' => 'brian.davidson@refin.com', 'place' => 'Licestor', "created_at" => date("Y-m-d")],
            ['name' => 'Henry, Alan', 'age' => 33, 'email' => 'alan.henry@refin.com', 'place' => 'Beeston', "created_at" => date("Y-m-d")],
        ];

        foreach ($data as $stud) {
                $result = $wpdb->insert($table_Utils, $stud);
        }
        
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {
        global $wpdb;
        $table_Utils = $wpdb->prefix.'ajdt_utils'; 

        // Removing Utils details
        $sql = "DROP TABLE $table_Utils;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Constructor for the AjaxUtility class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        add_action( 'init', array( $this, 'init_plugin' ) );
        //add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        //$this->init_hooks();
        $this->init_classes();
        do_action( 'ajdt_loaded' );
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {
        $namespace = '/ajdt/v1/';
        include_once AJDT_PATH . '/class-api-registrar.php';
        include_once AJDT_PATH . '/class-assets.php';
        include_once AJDT_PATH . '/class-admin.php';
        include_once AJDT_PATH . '/ajaxtable-ui.php';
        // if ( $this->is_request( 'admin' ) ) { } require_once include_once
        // if ( $this->is_request( 'frontend' ) ) { }
        // if ( class_exists( 'WeDevs_Dokan' ) ) { }

        include_once AJDT_PATH . '/class-api-utils.php';
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {
        // if ( $this->is_request( 'admin' ) ) { }
        // if ( $this->is_request( 'frontend' ) ) { }
        // if ( class_exists( 'WeDevs_Dokan' ) ) { }
        $this->container['restUtility'] = new ClassApiUtils();
        $this->container['rest'] = new AjaxTable\API_Registrar();
        $this->container['admin'] = new AjaxTable\Admin();
        $this->container['assets'] = new AjaxTable\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'ajdt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

} // class AjaxUtility
$ajdt = AjaxUtility::init();

/*
function ajdt_install() {
    global $wpdb;
    $table_Utils = $wpdb->prefix.'ajdt_utils'; 

    // Utils details
    $sql = "CREATE TABLE IF NOT EXISTS $table_Utils (
        id int(11) AUTO_INCREMENT PRIMARY KEY,
        name     varchar(50) NOT NULL,
        age     INT NOT NULL,
        email varchar(50)  not null,
        place     varchar(50) NOT NULL,
        created_at date NOT NULL);";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
}
register_activation_hook(__FILE__, 'ajdt_install');

function ajdt_install_data(){
    global $wpdb;
    $table_Utils = $wpdb->prefix.'ajdt_utils'; 

    $data = [
        ['name' => 'Tarek, Rahman', 'age' => 22, 'email' => 'tarek.rahman@refin.com', 'place' => 'Nottingham', "created_at" => date("Y-m-d")],
        ['name' => 'Farish, George W', 'age' => 21, 'email' => 'george.farish@refin.com', 'place' => 'London', "created_at" => date("Y-m-d")],
        ['name' => 'Arjun, Menon', 'age' => 43, 'email' => 'arjun.menon@refin.com', 'place' => 'Birmingham', "created_at" => date("Y-m-d")],
        ['name' => 'Charles-Love, Nadege', 'age' => 55, 'email' => 'nadege.charles@refin.com', 'place' => 'Liverpool', "created_at" => date("Y-m-d")],
        ['name' => 'Wood, Claire', 'age' => 37, 'email' => 'claire.wood@refin.com', 'place' => 'Manchestor', "created_at" => date("Y-m-d")],
        ['name' => 'Dowell,  Campbell', 'age' => 28, 'email' => 'campbell.dowell@refin.com', 'place' => 'Sounthampton', "created_at" => date("Y-m-d")],
        ['name' => 'Telenkov, Evgenii', 'age' => 43, 'email' => 'evgenii.telenkov@refin.com', 'place' => 'Derby', "created_at" => date("Y-m-d")],
        ['name' => 'Davidson, Brian', 'age' => 65, 'email' => 'brian.davidson@refin.com', 'place' => 'Licestor', "created_at" => date("Y-m-d")],
        ['name' => 'Henry, Alan', 'age' => 33, 'email' => 'alan.henry@refin.com', 'place' => 'Beeston', "created_at" => date("Y-m-d")],
    ];

    foreach ($data as $stud) {
            $result = $wpdb->insert($table_Utils, $stud);
    }
}
register_activation_hook(__FILE__, 'ajdt_install_data');
*/

// //Register the API routes for the objects of the controller.
// add_action( 'rest_api_init', function () {
//     require_once(plugin_dir_path(__FILE__).'/class-api-utils.php');
//     $ctrl = new ClassApiUtils();
//     $ctrl->register_routes();
// });

// add_action('rest_api_init', function () {
// 	$apiList = get_option('AJDT_API_LIST');
// 	foreach ($apiList as $ApiName => $value) {
// 		$namespace = 'ajdt/v1';
// 		$route = '';
// 		if ($value['SelectedCondtion'] == 'no condition') {
// 			$route = $ApiName;
// 		} else {
// 			$route = $ApiName . '/(?P<id>[A-Za-z0-9]+)';
// 		}


// 		register_rest_route($namespace, $route, array(
// 			'methods'  => 'GET',
// 			'callback' => 'custom_api_wp_get_result',
// 			'args' => $value
// 		));
// 	}
// });

//add_action('rest_api_init', 'register_ajdt_routes');

// function register_ajdt_routes(){
//     $apiList = get_option('AJDT_API_LIST');
// 	foreach ($apiList as $ApiName => $value) {
// 		$namespace = 'ajdt/v1';
// 		$route = '';
// 		if ($value['SelectedCondtion'] == 'no condition') {
// 			$route = $ApiName;
// 		} else {
// 			$route = $ApiName . '/(?P<id>[A-Za-z0-9]+)';
// 		}


// 		register_rest_route($namespace, $route, array(
// 			'methods'  => 'GET',
// 			'callback' => 'custom_api_wp_get_result',
// 			'args' => $value
// 		));
// 	}
// }

// function custom_api_wp_get_result($request)
// {
// 	global $wpdb;
// //print_r($request);

// 	$need =  $request->get_attributes();
// //print_r($need);


// 	$GetQuery1 = $need['args']['query'];
// 	$SelectedCondtion = $need['args']['SelectedCondtion'];
// 	if (($SelectedCondtion == 'no condition')) {
// 		//echo $GetQuery1;

// 		$myrows = $wpdb->get_results("{$GetQuery1}");
// 		return $myrows;
// 	} else {
// 		$Spliting = explode($SelectedCondtion, $GetQuery1);
// 		$MainQuery = $Spliting[0];
// 		$type = gettype($request['id']);
// 		if ($type == "string") {
// 			$param = '"' . $request['id'] . '"';
// 		}
// 		if ($type == "integer") {
// 			$param = $request['id'];
// 		}
// 		// echo $MainQuery;

// 		if ('&amp;gt;' == $SelectedCondtion)
// 			$SelectedCondtion = '>';
// 		if ('less than' == $SelectedCondtion)
// 			$SelectedCondtion = '<';
// 		$SelectedCondtion = $SelectedCondtion.' ';
// 			// echo $SelectedCondtion;
// 		// echo $request['id'];
// 		$myrows = $wpdb->get_results("{$MainQuery} {$SelectedCondtion} {$param}");

// 		return $myrows;
// 	}
// }

// function ajdt_enqueue_vuejs_styles() {
//     wp_enqueue_style('bootstrap-vue', '//unpkg.com/bootstrap/dist/css/bootstrap.min.css');
//     wp_enqueue_style('bootstrap-vue-min', '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css');

//     //wp_enqueue_style('fgproduct-styles', plugins_url('/css/styles.css', __FILE__ ));
// }
// add_action('admin_enqueue_scripts', 'ajdt_enqueue_vuejs_styles');

// function ajdt_enqueue_vuejs_scripts(){
//     wp_enqueue_script('polyfill', '//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver', [], '');
//     wp_enqueue_script('vue', '//unpkg.com/vue@latest/dist/vue.min.js', [], '');
//     wp_enqueue_script('bootstrap-vue', '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js', [], '');
//     wp_enqueue_script('bootstrap-vue-icons', '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue-icons.min.js', [], '');
//     wp_enqueue_script('Utils-details', plugin_dir_url( __FILE__ ) . 'vueapp.js', [], '1.0', true);
// }
// add_action('admin_enqueue_scripts', 'ajdt_enqueue_vuejs_scripts' );

// function ajdt_enqueue_test_scripts(){
//     wp_register_style( 'Font_Awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
//     wp_enqueue_style('Font_Awesome');
// }
// add_action('admin_enqueue_scripts', 'ajdt_enqueue_test_scripts' );


// //[AjaxTableUtil] - checks for shortcode in wordpress and renders this div mount
// function handle_shortcode() {
//     return '<div id="mount"></div>';
// }
// add_shortcode('AjaxTableUtil', 'handle_shortcode'); 

// function vueAdminPage() {
//   add_menu_page('AjaxTable Settings', 'AjaxTable Settings', 'manage_options' ,__FILE__, 'RenderAjaxTable', 'dashicons-forms');
// }
// add_action('admin_menu', 'vueAdminPage');

// function RenderAjaxTable(){
//     echo handle_shortcode();
// }