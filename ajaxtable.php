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
        define( 'AJDT_URL', plugins_url( '', AJDT_FILE ) );
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
        include_once AJDT_PATH . '/class-general-util.php';
        
        // if ( $this->is_request( 'admin' ) ) { } 
        // if ( $this->is_request( 'frontend' ) ) { }
        // if ( class_exists( 'okan' ) ) { }

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
        // if ( class_exists( 'okan' ) ) { }
        //$this->container['generalutil'] = new AjaxTable\GeneralUtil();
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