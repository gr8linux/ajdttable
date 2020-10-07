<?php
namespace AjaxTable;

/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_shortcode('AJDT', 'ajdt_handle_shortcode'); 
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu() {
        add_menu_page(__('AJDT Settings', 'ajdt'), __('AJDT Settings', 'ajdt'), 
            'activate_plugins', 'ajdtsettings', 'ajdt_list_api');

        add_submenu_page('ajdtsettings', __('AJDT Short Codes', 'fgpt'), __('AJDT Short Codes', 'fgpt'), 
            'activate_plugins', 'ajdttables', 'ajdt_render_shortcode');

        // foreach (get_option(AJDT_APILISTNAME) as $key => $Api) {
        //     //echo do_shortcode("[AJDT api='$key' allapi='$AllKeys']");
        //     add_submenu_page('ajdtsettings', __('Short Codes - '.$key, 'fgpt'), __('Short Codes - '.$key, 'fgpt'), 
        //     'activate_plugins', 'ajdttables', 'ajdt_render_shortcode');
        // }
    }

    /**
    * Add POS gateways
    *
    * @param $gateways
    *
    * @return array
    */
    public function payment_gateways( $gateways ) {
        $available_gateway = \AjaxTable::init()->available_gateway();
        // else add default POS gateways
        return array_merge( $gateways, apply_filters( 'ajdt_payment_gateway', array_keys( $available_gateway ) ) );
    }


    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        //add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        //add_shortcode('AjaxTableUtil', 'ajdt_handle_shortcode'); 
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'ajdt-flaticon' );
        wp_enqueue_style( 'ajdt-tinymce' );
        wp_enqueue_style( 'ajdt-tinymce' );
        wp_enqueue_style( 'ajdt-style' );
        wp_enqueue_style( 'ajdt-admin' );

        wp_enqueue_script( 'ajdt-tinymce-plugin' );
        wp_enqueue_script( 'ajdt-vendor' );
        wp_enqueue_script( 'ajdt-blockui' );

        wp_enqueue_script( 'ajdt-bootstrap' );
        do_action( 'ajdt_load_admin_scripts' );
        wp_enqueue_script( 'ajdt-admin' );
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page() {
        echo '<div class="wrap"><div id="ajdt-admin-app"></div></div>';
    }

    /**
     * Add pos order column
     *
     * @since 1.0.4
     *
     * @param array $defaults
     */
    public function add_pos_order_column($defaults) {
        $defaults['is_pos_order'] = apply_filters( 'ajdt_shop_order_pos_column_title', __( 'Is POS', 'ajdt' ) );

        return $defaults;
    }

    /**
     * Render if is pos order content
     *
     * @since 1.0.4
     *
     * @param string $column_name
     * @param integer $post_id
     *
     * @return string
     */
    public function render_is_pos_order_content( $column_name, $post_id ) {
        if ( $column_name === 'is_pos_order' ) {
            $order = wc_get_order( $post_id );

            if ( 'ajdt' === $order->get_created_via() ) {
                echo '<span class="dashicons dashicons-store"></span>';
            } else {
                echo '&ndash;';
            }
        }
    }

    /**
     * Added column styles
     *
     * @since 1.0.4
     */
    public function add_pos_column_style() {
        $css = '.widefat .column-is_pos_order { width: 9% !important; text-align: center; } .widefat .column-is_pos_order span.dashicons-store{ font-size: 17px; margin-top: 3px; }';
        wp_add_inline_style( 'woocommerce_admin_styles', $css );
    }
}
