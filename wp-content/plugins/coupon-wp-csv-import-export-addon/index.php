<?php
/**
 * Plugin Name:       Coupon WP CSV Import Export Addon
 * Plugin URI:        https://www.famethemes.com/plugins/coupon-wp-frontend-submit-addon
 * Description:       Export coupons to csv, Import coupons form csv.
 * Version:           1.0.9
 * Author:            famethemes
 * Author URI:        https://www.famethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp_coupon_ie
 * Domain Path:       /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'WP_COUPON_IE_URL', trailingslashit( plugins_url('', __FILE__) ) );
define( 'WP_COUPON_IE_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );



function wp_coupon_ie_remove_none_printable( $string ) {
    if ( is_array( $string ) ) {
        foreach ( $string as $k => $v ) {
            $string[ $k ] = wp_coupon_ie_remove_none_printable( $v );
        }
        return $string;
    }
    ///return preg_replace('/[[:^print:]]/', "", $string );
    return apply_filters( 'wp_coupon_ie_remove_none_printable_string', $string );
}



class WP_Coupon_IE {

    function __construct(){
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu',  array( $this, 'add_menus' ) );
        add_action( 'init',  array( $this, 'init' ), 99 );
        add_action( 'wp_ajax_coupons_import',  array( $this, 'import' ) );
    }

    function init(){
        //Start new or resume existing session
        session_start();

        // Upload handle
        if ( isset( $_POST['wp_coupon_ie'] ) ) {
           if (  wp_verify_nonce( $_POST['wp_coupon_ie'], 'wp_coupon_ie' ) ) {
               require_once WP_COUPON_IE_PATH.'inc/parsecsv.lib.php';
               require_once WP_COUPON_IE_PATH.'inc/handle-upload-file.php';
           }
        }


        // Cancel import
        if ( isset( $_REQUEST['wp_cie_action'] ) && $_REQUEST['wp_cie_action'] == 'cancel-import' ) {
            $nonce =  isset(  $_REQUEST['nonce'] ) ?  $_REQUEST['nonce'] : '';
            if ( wp_verify_nonce( $nonce, 'wp_coupon_ie' ) ) {
                unset( $_SESSION['wp_coupon_import_data'] );
                unset( $_SESSION['wp_coupons_import_settings'] );
                // back to import page
                wp_redirect(admin_url('tools.php?page=wp_coupon_import'));
                die();
            } else {
                wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp_coupon_ie' ) );
            }
        }

        // Maybe import coupon
        if ( isset( $_POST['wp_cie_export'] ) && $_POST['wp_cie_export'] != '' ) {
            if ( isset( $_POST['wp_coupon_ie'] ) ) {
                if (  wp_verify_nonce( $_POST['wp_coupon_ie'], 'wp_coupon_ie' ) ) {
                    $this->export( $_POST );
                }
            }
        }


    }

    function export( $args = array() ){
        require_once WP_COUPON_IE_PATH.'inc/parsecsv.lib.php';
        require_once WP_COUPON_IE_PATH.'inc/class-export.php';
        wp_coupons_export( $args );
    }

    function import(){
        require_once WP_COUPON_IE_PATH.'inc/class-import.php';

        $nonce = isset ( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'wp_coupon_ie' ) ) {
            die( 'Security check' );
        }

        $form_data = $_POST['form_data'];
        if ( $form_data ) {
            $form_data = wp_parse_args( $form_data );
        }

        // $index = isset( $_POST['index'] ) ? $_POST['index'] : 1;

        $import = new WP_Coupons_Import( );
        $import->setup_fields( $form_data );

        $row_data =  current( $_SESSION['wp_coupon_import_data'] );
        $key = key( $_SESSION['wp_coupon_import_data'] );
        unset( $_SESSION['wp_coupon_import_data'][ $key ] );

        /*
        if ( isset( $_SESSION['wp_coupon_import_data'][ $index - 1 ] ) ) {
            $row_data = $_SESSION['wp_coupon_import_data'][ $index - 1 ];
            unset( $_SESSION['wp_coupon_import_data'][ $index - 1 ] );
        } else {
            $row_data = array();
        }
        */

        $import->setup_import_data( $row_data );
        $r = $import->save();

        if ( empty( $_SESSION['wp_coupon_import_data'] ) ) {
            unset( $_SESSION['wp_coupon_import_data'] );
            unset( $_SESSION['wp_coupons_import_settings'] );
        }
        if ( $r['save'] ) {
            wp_send_json_success( join( "<br/>", $import->msg ) );
        } else {
            wp_send_json_error( esc_html__( 'Nothing saved.', 'wp_coupon_ie' ));
        }

        die();
    }

    function add_menus(){
        add_submenu_page( 'tools.php', 'Coupons Import', 'Import Coupons', 'import', 'wp_coupon_import', array( $this, 'display_import' ) );
        add_submenu_page( 'tools.php', 'Coupons Export', 'Export Coupons', 'import', 'wp_coupon_export', array( $this , 'display_export' ) );
    }

    function enqueue_scripts( $hook ){
        if ( $hook = 'tools_page_wp_coupon_import' || $hook == 'tools_page_wp_coupon_export' ) {
            wp_register_style( 'wp_coupon_import', WP_COUPON_IE_URL. 'assets/css/import-export.css', false);
            wp_enqueue_style( 'wp_coupon_import' );

            wp_register_script( 'wp_coupon_import', WP_COUPON_IE_URL. 'assets/js/import.js',array( 'jquery' ), true );
            wp_enqueue_script( 'wp_coupon_import' );
            wp_localize_script( 'jquery', 'wp_coupon_import', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wp_coupon_ie' ),
                'warning_file' => esc_html__( 'Please select a .CSV, .XLS or .XLS file.', 'wp_coupon_ie' ),
                'import_start' => esc_html__( 'Starting import...', 'wp_coupon_ie' ),
                'import_done' => esc_html__( 'All done. Have fun !', 'wp_coupon_ie' ),
                'confirm_cancel' => esc_html__( 'Do you want cancel import data ?', 'wp_coupon_ie' ),
                'confirm_import' => esc_html__( 'Some fields were not set, Do you want to continue import ?', 'wp_coupon_ie' ),
                'confirm_close_window' => esc_html__( "Import coupons script is running, do you want to close window and stop importing coupons ?", 'wp_coupon_ie' ),
            ) );
        }
    }

    function display_import(){
       include dirname( __FILE__ ).'/tpl/import.php';
    }

    function display_export(){
        include dirname( __FILE__ ).'/tpl/export.php';
    }

    static function get_data_fields(){
        $fields = array(
            'coupon' => array(
                'post_title'   => array(
                    'label' => esc_html__( 'Coupon title', 'wp_coupon_ie' ),
                    'field' => 'title',
                ),
                'post_content' =>array(
                    'label' => esc_html__( 'Coupon content', 'wp_coupon_ie' ),
                    'field' => 'content',
                ),
                'post_excerpt' =>array(
                    'label' => esc_html__( 'Coupon Excerpt', 'wp_coupon_ie' ),
                    'field' => 'excerpt'
                ),

                'post_status' =>array(
                    'label' => esc_html__( 'Coupon Status', 'wp_coupon_ie' ),
                    'field' => 'status'
                ),

                'post_date' =>array(
                    'label' => esc_html__( 'Date added', 'wp_coupon_ie' ),
                    'field' => 'date'
                ),

                '_wpc_coupon_type' =>array(
                    'label'=> esc_html__( 'Coupon type', 'wp_coupon_ie' ),
                    'field' => 'type'
                ),
                '_wpc_expires' =>array(
                    'label'  => esc_html__( 'Coupon expires', 'wp_coupon_ie' ),
                    'field'  => 'expires'
                ),
                '_wpc_coupon_type_code' =>array(
                    'label' => esc_html__( 'Coupon code', 'wp_coupon_ie' ),
                    'field' => 'code',
                ),
                '_wpc_coupon_save' =>array(
                    'label' => esc_html__( 'Discount Value', 'wp_coupon_ie' ),
                    'field' => 'discount_value',
                ),
                '_wpc_destination_url' =>array(
                    'label' =>  esc_html__( 'Destination url', 'wp_coupon_ie' ),
                    'field' => 'destination_url'
                ),
                '_wpc_exclusive' =>array(
                    'label' =>  esc_html__( 'Exclusive', 'wp_coupon_ie' ),
                    'field' => 'exclusive'
                ),

                'printable_url' =>array(
                    'label' => esc_html__( 'Print image url', 'wp_coupon_ie' ),
                    'field' => 'printable_url'
                ),

                'store' =>array(
                    'label' => esc_html__( 'Stores', 'wp_coupon_ie' ),
                    'field' => 'store',

                ),
                'category' => array(
                    'label' => esc_html__( 'Categories', 'wp_coupon_ie' ),
                    'field' => 'category'
                ),
                'author' => array(
                    'label' => esc_html__( 'Author', 'wp_coupon_ie' ),
                    'field' => 'author'
                ),

            ),

            'store' =>array(
                'name' =>array(
                    'label' => esc_html__( 'Store name', 'wp_coupon_ie' ),
                    'field' => 'store_name',
                ),
                'slug' => array(
                    'label' => esc_html__( 'Store slug', 'wp_coupon_ie' ),
                    'field' => 'store_slug'
                ),

                'description' => array(
                    'label' => esc_html__( 'Store description', 'wp_coupon_ie' ),
                    'field' => 'store_description'
                ),
                'parent' => array(
                    'label' => esc_html__( 'Store Parent', 'wp_coupon_ie' ),
                    'field' => 'store_parent',
                ),
                'store_url' => array(
                    'label' => esc_html__( 'Store home url', 'wp_coupon_ie' ),
                    'field' => 'store_url'
                ),
                'store_aff_url' => array(
                    'label' => esc_html__( 'Store affiliate URL', 'wp_coupon_ie' ),
                    'field' => 'store_aff_url'
                ),
                'store_heading' =>array(
                    'label' => esc_html__( 'Store heading', 'wp_coupon_ie' ),
                    'field' => 'store_heading'
                ),
                'is_featured' =>array(
                    'label' => esc_html__( 'Store is featured', 'wp_coupon_ie' ),
                    'field' => 'store_is_featured'
                ),
                'extra_info' =>array(
                    'label' => esc_html__( 'Store extra info', 'wp_coupon_ie' ),
                    'field' => 'store_extra_info'
                ),
                'store_image' =>array(
                    'label' => esc_html__( 'Store thumbnail', 'wp_coupon_ie' ),
                    'field' => 'store_image'
                ),
            ),

            'category' =>array(
                'name' =>array(
                    'label' => esc_html__( 'Category name', 'wp_coupon_ie' ),
                    'field' => 'category_name'
                ),
                'slug' => array(
                    'label' => esc_html__( 'Category slug', 'wp_coupon_ie' ),
                    'field' => 'category_slug',
                ),
                'description' => array(
                    'label' => esc_html__( 'Category description', 'wp_coupon_ie' ),
                    'field' => 'category_description',
                ),
                'parent' =>array(
                    'label' => esc_html__( 'Category Parent', 'wp_coupon_ie' ),
                    'field' => 'category_parent'
                ),
                'icon' => array(
                    'label' => esc_html__( 'Icon', 'wp_coupon_ie' ),
                    'field' => 'category_icon'
                ),
                'cat_image' => array(
                    'label' => esc_html__( 'Thumbnail', 'wp_coupon_ie' ),
                    'field' => 'category_cat_image'
                )
            ),
        );
        return  $fields;
    }
}

if ( is_admin() ) {
    new WP_Coupon_IE();
}

