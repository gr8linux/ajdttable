<?php
namespace AjaxTable;

/**
 * Scripts and Styles Class
 */
class Assets {

    function __construct() {
        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
        } else {
            add_action( 'wepos_enqueue_scripts', [ $this, 'register' ], 5 );
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
        $this->enqueue_all_scripts();
        $this->register_localize();
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : null;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : null;
            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            //$deps = isset( $style['deps'] ) ? $style['deps'] : false;
            //wp_register_style( $handle, $style['src'], $deps, AJDT_VERSION );
            wp_register_style( $handle, $style['src'] );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        global $wp_version;

        $scripts = [
            'popper.min.js' => [
                'src'       => 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
                'in_footer' => true
            ],
            'bootstrap.min' => [
                'src'       => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
                'in_footer' => true
            ],
            'polyfill' => [
                'src'       => '//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver',
                'in_footer' => true
            ],
            'vue' => [
                'src'       => '//unpkg.com/vue@latest/dist/vue.min.js',
                'in_footer' => true
            ],
            'bootstrap-vue' => [
                'src'       => '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js',
                'in_footer' => true
            ],
            'bootstrap-vue-icons' => [
                'src'       => '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue-icons.min.js',
                'in_footer' => true
            ],
            'ajdt-lib' => array(
                'src'       => AJDT_URL . '/js/vueapp.js',
                'in_footer' => true
            ),
            'ajdt-scripts' => array(
                'src'       => AJDT_URL . '/js/scripts.js',
                'in_footer' => true
            )
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {

        $styles = [
            'bootstrap-vue' => [
                'src' =>  '//unpkg.com/bootstrap/dist/css/bootstrap.min.css'
            ],
            'bootstrap-vue-min' => [
                'src' =>  '//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css'
            ]
        ];

        return $styles;
    }

    public function enqueue_all_scripts() {
       //if ( ! is_admin() ) {
            // Enqueue all style
            wp_enqueue_style( 'bootstrap-vue' );
            wp_enqueue_style( 'bootstrap-vue-min' );

            // Load scripts
            wp_enqueue_script( 'popper.min.js' );
            wp_enqueue_script( 'bootstrap.min' );
            wp_enqueue_script( 'polyfill' );
            wp_enqueue_script( 'vue' );
            wp_enqueue_script( 'bootstrap-vue' );
            wp_enqueue_script( 'bootstrap-vue-icons' );

            do_action( 'ajdt_load_forntend_scripts' );
            wp_enqueue_script( 'ajdt-lib' );
            wp_enqueue_script( 'ajdt-scripts' );
            
       // }
    }

    /**
     * Set localize script data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_localize() {
        $localize_data = apply_filters( 'adjt_localize_data', [
            'rest' => array(
                'privusername'    => 'shajeeb shahul hameed',
                'root'    => esc_url_raw( get_rest_url() ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'wcversion' => 'wc/v3',
                'posversion' => 'adjt/v1',
            ),
            'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
            'nonce'                        => wp_create_nonce( 'wepos_nonce' ),
            'libs'                         => [],
            'routeComponents'              => array( 'default' => null ),
            'assets_url'                   => AJDT_ASSETS,
            'ajax_loader'                  => AJDT_ASSETS . '/images/spinner-2x.gif',
            'logout_url'                   => wp_logout_url( site_url() )
        ] );

        wp_localize_script( 'adjt-vendor', 'adjt', $localize_data );
    }

     /**
     * SPA Routes
     *
     * @return array
     */
    public function get_vue_admin_routes() {
        $routes = array(
            array(
                'path'      => '/settings',
                'name'      => 'Settings',
                'component' => 'Settings'
            ),
        );

        return apply_filters( 'wepos_admin_routes', $routes );
    }
}
