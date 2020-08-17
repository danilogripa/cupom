<?php
/**
 * Add js, css file to widgets
 */
function wpcoupon_admin_widgets_js(){
    $url = get_template_directory_uri().'/inc/widgets/';

    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'backbone' );

    wp_enqueue_media();

    wp_enqueue_script( 'wpcoupon-admin-widget', $url.'assets/js/admin-widget.js', array( 'jquery' ) );
    wp_enqueue_style( 'wpcoupon-admin-widget', $url.'assets/css/admin-css.css' );

}


/**
 * Add js, css file to post using by page builder
 */
function wpcoupon_admin_widgets_posts_js(){
    if ( ! defined( 'SITEORIGIN_PANELS_BASE_FILE' ) ) {
        $url = get_template_directory_uri().'/inc/widgets/';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'wpcoupon-admin-widget', $url.'assets/js/admin-widget.js', array( 'jquery' ) );
        wp_enqueue_style( 'wpcoupon-admin-widget', $url.'assets/css/admin-css.css' );
    }
}

// add_action( 'admin_print_scripts-widgets.php', 'wpcoupon_admin_widgets_js' );
if ( is_admin() ) {
    //add_action( 'admin_enqueue_scripts', 'wpcoupon_admin_widgets_js' );
    /*
    add_action( 'load-post.php', 'wpcoupon_admin_widgets_js' );
    add_action( 'load-post-new.php', 'wpcoupon_admin_widgets_js' );
    */

    add_action( 'admin_print_scripts-post-new.php', 'wpcoupon_admin_widgets_posts_js' );
    add_action( 'admin_print_scripts-post.php', 'wpcoupon_admin_widgets_js' );

    add_action( 'load-widgets.php', 'wpcoupon_admin_widgets_js' );
}


