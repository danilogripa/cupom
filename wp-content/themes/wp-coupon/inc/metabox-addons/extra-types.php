<?php
require_once get_template_directory() .'/inc/metabox-addons/coupon-store.php';


add_action( 'admin_print_styles-post.php', 'cmb2_render_coupon_summary_styles' );
add_action( 'admin_print_styles-post-new.php', 'cmb2_render_coupon_summary_styles' );
add_action( 'load-edit-tags.php', 'cmb2_render_coupon_summary_styles' );;

add_action( 'admin_print_scripts-post-new.php', 'cmb2_render_coupon_summary_js' );
add_action( 'admin_print_scripts-post.php', 'cmb2_render_coupon_summary_js' );
add_action( 'load-edit-tags.php', 'cmb2_render_coupon_summary_js' );

/**
 * Load css for edit post
 */
function cmb2_render_coupon_summary_styles(){
    wp_enqueue_style( 'semantic-icon', get_template_directory_uri() . '/assets/css/components/icon.min.css' );
    wp_enqueue_style( 'extra-types', get_template_directory_uri() . '/inc/metabox-addons/css/extra-types.css' );
}

/**
 * Load js for edit post
 */
function cmb2_render_coupon_summary_js(){
    wp_enqueue_script( 'extra-types', get_template_directory_uri() . '/inc/metabox-addons/js/extra-type.js' );
}



/**
 * Display coupon summary on coupon post type
 *
 * @param $field
 * @param $escaped_value
 * @param $object_id
 * @param $object_type
 * @param $field_type_object
 */
function cmb2_render_coupon_summary( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
     wpcoupon_setup_coupon( $object_id );
    ?>
    <div class="st-type-summary">
        <span><i class="wifi icon"></i> <?php printf( _n( '%s view', '%s views',  wpcoupon_coupon()->get_total_used(), 'wp-coupon' ), wpcoupon_coupon()->get_total_used() ); ?></span>
        <span><i class="smile outline icon"></i> <?php echo wpcoupon_coupon()->_wpc_vote_up; ?></span>
        <span><i class="frown outline icon"></i> <?php echo wpcoupon_coupon()->_wpc_vote_down; ?></span>
    </div>
    <?php
    wp_reset_postdata();
}
add_action( 'cmb2_render_coupon_summary', 'cmb2_render_coupon_summary', 10, 5 );


