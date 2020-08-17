<?php
/*
Plugin Name: Wp Coupon Demo Import
Plugin URI: https://github.com/shrimp2t/wpcoupon-demo-import
Description: Import your content, widgets and theme settings with one click. Theme authors! Enable simple demo import for your theme demo data.
Version: 1.0.2
Author: Shrimp2t
Author URI: https://github.com/shrimp2t/
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Text Domain: coupon-import
*/


class WPCoupon_Demo_Import {
    public $dir;
    public $url;
    public $demo_args;

    function __construct( ){

        $this->demo_args = array(
            'page_on_front' => 194,
            'page_for_posts' => 21,
            'nav'=> array(
                'primary' => 5,
                'footer' => 22
            ),
            'options' => array(
                'coupons_listing_page' => 'post',
                'stores_listing_page'  => 'post',
                'top_search_stores'    => 'term',
            ),
            'widget_keys' => array(
                'image_id' => 'post',
                'nav_menu' => 'term',
            ),

        );

        add_action( 'wp_ajax_c_demo_import_content', array( $this, 'ajax_import' ) );
        add_filter( 'wie_widget_settings_array', array( $this, 'resetup_widget_data' ), 15, 2 );
    }

    static function import_menu() {
        add_submenu_page(
            'wpcoupon_options',
            esc_html__( 'Wp Coupon demo content', 'wp-coupon' ),
            esc_html__( 'Demo Content', 'wp-coupon' ),
            'manage_options',
            'wpcoupon-demo-content',
            array( __CLASS__, 'display' )
        );
    }

    function ajax_import(){

        $nonce = $_REQUEST['_nonce'];
        if ( ! wp_verify_nonce( $nonce, 'wpc_demo_import' ) ) {
            die( 'Security check' );
        } else {
            // Do stuff here.
        }

        $import = new WP_Import_Demo_Content(array(
            'xml'        => dirname( __FILE__ ).'/dummy-data/dummy-data.xml',
            'customize'  => dirname( __FILE__ ).'/dummy-data/customize.json',
            'widget'     => dirname( __FILE__ ).'/dummy-data/widgets.json',
            'option'     => dirname( __FILE__ ).'/dummy-data/options.json',
            'term_meta'  => dirname( __FILE__ ).'/dummy-data/term-meta.json',
            'option_key' => 'st_options',
        ) );
        $import->import();
        $this->setup_demo( $import );

        update_option( 'wpc_demo_imported', 1 );

        die( 'done' );
    }

    function resetup_widget_data( $array, $import = false ){
        if ( ! $import ) {
            return $array;
        }
        // processed_posts
        // processed_terms
        foreach ( $array as $k => $v ){
            if ( ! is_array( $v ) ){
                if ( isset( $this->demo_args['widget_keys'][ $k ] ) ){
                    $t = $this->demo_args['widget_keys'][ $k ];
                    if ( $t == 'term' ){
                        if ( is_numeric( $v ) ) {
                            if ( isset( $import->processed_terms[ $v ] ) ) {
                                $array[ $k ] = $import->processed_terms[ $v ];
                            }
                        }
                    } else if ( $t == 'post' ) {
                        if ( is_numeric( $v ) ) {
                            if ( isset( $import->processed_posts[ $v ] ) ) {
                                $array[ $k ] = $import->processed_posts[ $v ];
                            }
                        }
                    }
                }
            } else {
                $array[ $k ] = $this->resetup_widget_data( $v, $import );
            }
        }
        return $array;
    }


    function setup_demo( $import ){
        // $processed_posts
        // $processed_terms
        $home_page_id = isset( $import->processed_posts[ $this->demo_args['page_on_front'] ] ) ? $import->processed_posts[ $this->demo_args['page_on_front'] ] : false;
        $home_blog_id = isset( $import->processed_posts[ $this->demo_args['page_for_posts'] ] ) ? $import->processed_posts[ $this->demo_args['page_for_posts'] ] : false;

        if ( $home_page_id || $home_blog_id ) {
            update_option( 'show_on_front', 'page' );
            if ( $home_page_id ){
                update_option( 'page_on_front', $home_page_id );
            }
            if ( $home_blog_id ) {
                update_option( 'page_for_posts', $home_blog_id );
            }
        }

        // Setup demo menu
        $nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
        foreach( $this->demo_args['nav'] as $location => $id ){
            if ( isset( $import->processed_terms[ $id ] ) ) {
                $menu_id = $import->processed_terms[ $id ];
                $nav_menu_locations[ $location ] =$menu_id;
            }
        }
        set_theme_mod( 'nav_menu_locations', $nav_menu_locations );

    }

    static function display( ){

        $show_export = false;
        if ( isset( $_REQUEST['export'] ) && $_REQUEST['export'] == 1 ) {
            $show_export = true;
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'WP Coupon demo content', 'wp-coupon' ); ?></h1>

            <div class="theme_info info-tab-content">
                <?php if ( $show_export ) { ?>

                    <div style="margin-bottom: 25px;">
                        <h3>Options Export</h3>
                        <textarea readonly="true" style="width: 100%;" rows="10"><?php echo esc_textarea( WP_Import_Demo_Content::generate_options_export_data( 'st_options' ) );  ?></textarea>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <h3>Widget Export</h3>
                        <textarea readonly="true" style="width: 100%;" rows="10"><?php echo esc_textarea( WP_Import_Demo_Content::generate_widgets_export_data() );  ?></textarea>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <h3>Customize Export</h3>
                        <textarea readonly="true" style="width: 100%;" rows="10"><?php echo esc_textarea( WP_Import_Demo_Content::generate_theme_mods_export_data( ) );  ?></textarea>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <h3>Term Meta</h3>
                        <textarea readonly="true" style="width: 100%;" rows="10"><?php echo esc_textarea( WP_Import_Demo_Content::generate_term_meta_export_data() );  ?></textarea>
                    </div>

                <?php } ?>

                <div class="action-required">
                    <p class="tie_message_hint">Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme. It will
                        allow you to quickly edit everything instead of creating content from scratch. When you import the data following things will happen:</p>

                    <ul style="padding-left: 20px;list-style-position: inside;list-style-type: square;}">
                        <li>No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified .</li>
                        <li>No WordPress settings will be modified .</li>
                        <li>Posts, pages, some images, some widgets and menus will get imported .</li>
                        <li>Images will be downloaded from our server, these images are copyrighted and are for demo use only .</li>
                        <li>Please click import only once and wait, it can take a couple of minutes</li>
                    </ul>
                </div>

                <div class="action-required"><p class="tie_message_hint">Before you begin, make sure all the required plugins are activated.</p></div>
                <?php if ( get_option( 'wpc_demo_imported' ) == 1 ) { ?>
                    <div class="action-required updated settings-error notice" style="border-left-color: #a1d3a2; clear:both;">
                        <p><?php _e('Demo already imported', 'wp-coupon'); ?></p>
                    </div>
                <?php } ?>

                <p>
                    <a href="#" class="wpc_demo_import button-primary">
                    <?php
                    if( get_option( 'wpc_demo_imported' ) == 1 ) {
                        _e('Import Again', 'wp-coupon');
                    } else {
                        _e('Import Demo Data', 'wp-coupon');
                    }
                    ?>
                    </a>
                    <span class="spinner" style="float: none; margin-left: 0px; margin-top: -2px;"></span>
                </p>

            </div>
        </div>


        <script type="text/javascript">
            jQuery( document).ready( function( $ ){
                $( '.wpc_demo_import').on( 'click', function( e ){
                    e.preventDefault();
                    var btn = $(this);
                    if ( btn.hasClass( 'disabled' ) ) {
                        return false;
                    }
                    var c = confirm( "<?php echo  esc_js(esc_attr__( 'Are you sure want to import demo content ?', 'wp-coupon' ) ); ?>" );
                   // var c = true;
                    if ( c ) {

                        btn.addClass('disabled');

                        $('.spinner', btn.parent() ).css('visibility', 'visible');

                        //return;

                        var params = {
                            'action': 'c_demo_import_content',
                            '_nonce': '<?php echo wp_create_nonce( 'wpc_demo_import' ); ?>',
                            _time:  new Date().getTime()
                        };

                        $.post( window.ajaxurl, params, function ( data) {
                            btn.removeClass('disabled');
                            $('.spinner', btn.parent() ).css('visibility', 'hidden');
                            window.location = '<?php echo admin_url( 'admin.php?page=wpcoupon-demo-content&imported=1' ); ?>';
                            //$( '.ajax_console').val( data );
                        });
                    }

                } );
            } );
        </script>
        <?php
    }

}

if ( is_admin() ) {
    require_once dirname(__FILE__) . '/class-content.php';
    add_action('admin_menu', array('WPCoupon_Demo_Import', 'import_menu'), 35);
    function wpcoupon_demo_import_init()
    {
        new WPCoupon_Demo_Import();
    }

    add_action('init', 'wpcoupon_demo_import_init');
}