<?php
/**
 * Metabox config file
 *
 * @package WP Coupon/inc/config
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

/**
 * Get the bootstrap!
 */
if ( file_exists(  get_template_directory() . '/inc/metabox/init.php' ) ) {
	require_once  get_template_directory() . '/inc/metabox/init.php';
    require_once  get_template_directory() . '/inc/metabox-addons/extra-types.php';
    require_once  get_template_directory() . '/inc/metabox-addons/icon/icon.php';
}


function cmb2_change_minutes_step( $l10n ){
    $l10n['defaults']['time_picker']['stepMinute'] = 1;
    return $l10n;
}

add_filter( 'cmb2_localized_data', 'cmb2_change_minutes_step' );


/**
 * Sanitizes WYSIWYG fields like WordPress does for post_content fields.
 */
function cmb2_html_content_sanitize( $content ) {
    return apply_filters( 'content_save_pre', $content );
}



/**
 * Metabox for Show on page IDs callback
 * @author Tom Morton
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters
 *
 * @param bool $display
 * @param array $meta_box
 * @return bool display metabox
 */
function wpcoupon_metabox_show_on_cb( $field ) {
    global $post;

    $meta_box = $field->args;
    if ( ! isset( $meta_box['show_on_page'] ) ) {
        return true ;
    }

    $post_id = $post->ID;

    if ( ! $post_id ) {
        return false;
    }

    // See if there's a match
    return in_array( $post_id, (array) $meta_box['show_on_page'] );
}



add_action( 'cmb2_init', 'wpcoupon_coupon_meta_boxes' );
add_action( 'cmb2_init', 'wpcoupon_page_meta_boxes' );

/**
 * Add metabox for coupon
 * @since 1.0.0
 */
function wpcoupon_coupon_meta_boxes() {
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_wpc_';

    $coupon_meta = new_cmb2_box( array(
        'id'            => $prefix . 'coupon',
        'title'         => esc_html__( 'Coupon Settings', 'wp-coupon' ),
        'object_types'  => array( 'coupon', ), // Post type
        // 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
        // 'context'    => 'normal',
        // 'priority'   => 'high',
        // 'show_names' => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // true to keep the metabox closed by default
    ) );


    $coupon_meta->add_field( array(
        'name'             => esc_html__( 'Coupon Type', 'wp-coupon' ),
        'id'               => $prefix . 'coupon_type',
        'type'             => 'select',
        'show_option_none' => false,
        'options'          => wpcoupon_get_coupon_types(),
    ) );


    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Coupon Code', 'wp-coupon' ),
        'id'            => $prefix . 'coupon_type_code',
        'type'          => 'text_medium',
        'attributes'    => array(
            'placeholder'   => esc_html__( 'Example: EMIAXHGF', 'wp-coupon' ),
        ),
        'before_row'    => '<div class="st-condition-field cmb-row" data-show-when = "code" data-show-on="' . $prefix . 'coupon_type' . '">',
        'after_row'     => '</div>'

    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Coupon Printable Image', 'wp-coupon' ),
        'id'            => $prefix . 'coupon_type_printable',
        'type'          => 'file',
        'attributes'    => array(
            'placeholder'   => esc_html__( 'http://...', 'wp-coupon' ),
        ),
        'before_row'    => '<div class="st-condition-field cmb-row" data-show-when = "print" data-show-on="' . $prefix . 'coupon_type' . '">',
        'after_row'     => '</div>'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Coupon URL', 'wp-coupon' ),
        'id'            => $prefix . 'destination_url',
        'type'          => 'text_url',
        'desc'          => esc_html__( 'Coupon URL, if this field empty then Store Aff URL will be use.', 'wp-coupon' ),
        'attributes'    => array(
            'placeholder'   => esc_html__( 'http://...', 'wp-coupon' ),
        ),
    ) );

    $coupon_meta->add_field( array(
        'name'       => esc_html__( 'Expires', 'wp-coupon' ),
        'id'         => $prefix . 'expires',
        'type'       => 'text_datetime_timestamp',
        'desc'       => sprintf( __( 'Set expires for coupon. By default expires date based on GMT+0, <a href="%1$s" target="_blank">Click here</a> to making the coupons get expired based on selected timezone.', 'wp-coupon' ), esc_url( admin_url( 'admin.php?page=wpcoupon_options&tab=9' ) ) ),
    ) );

    $coupon_meta->add_field( array(
        'name'       => esc_html__( 'Start Date', 'wp-coupon' ),
        'id'         => $prefix . 'start_on',
        'type'       => 'text_datetime_timestamp',
        'desc'       => sprintf( __( 'Set start date for coupon. By default start date based on GMT+0, <a href="%1$s" target="_blank">Click here</a> making the coupons start date based on selected timezone.', 'wp-coupon' ), esc_url( admin_url( 'admin.php?page=wpcoupon_options&tab=9' ) ) ),
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Discount Value', 'wp-coupon' ),
        'id'            => $prefix . 'coupon_save',
        'type'          => 'text_medium',
        'attributes'    => array(
            'placeholder'   => esc_html__( 'Example: 15% Off', 'wp-coupon' ),
        ),
        'desc'          => esc_html__( 'This text maybe display as coupon thumbnail.', 'wp-coupon' ),
        'before_row'    => '<div class="st-condition-field cmb-row">',
        'after_row'     => '</div>'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Free Shipping Coupon', 'wp-coupon' ),
        'desc'          => esc_html__( 'This coupon is free shipping coupon', 'wp-coupon' ),
        'id'            => $prefix . 'free_shipping',
        'type'          => 'checkbox'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Exclusive Coupon', 'wp-coupon' ),
        'desc'          => esc_html__( 'This coupon is exclusive', 'wp-coupon' ),
        'id'            => $prefix . 'exclusive',
        'type'          => 'checkbox'
    ) );


    // Custom tracking
    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Number Coupon Used', 'wp-coupon' ),
        'desc'          => esc_html__( '', 'wp-coupon' ),
        'id'            => $prefix . 'used',
        'type'          => 'text'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Number Views', 'wp-coupon' ),
        'desc'          => esc_html__( '', 'wp-coupon' ),
        'id'            => $prefix . 'views',
        'type'          => 'text'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Vote Up', 'wp-coupon' ),
        'desc'          => esc_html__( '', 'wp-coupon' ),
        'id'            => $prefix . 'vote_up',
        'type'          => 'text'
    ) );

    $coupon_meta->add_field( array(
        'name'          => esc_html__( 'Vote Down', 'wp-coupon' ),
        'desc'          => esc_html__( '', 'wp-coupon' ),
        'id'            => $prefix . 'vote_down',
        'type'          => 'text'
    ) );


}



/**
 * Add meta box for pages
 */
function wpcoupon_page_meta_boxes() {
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_wpc_';

    $page_meta = new_cmb2_box( array(
        'id'            => $prefix . 'page',
        'title'         => esc_html__( 'Page Settings', 'wp-coupon' ),
        'object_types'  => array( 'page' ), // Post type
    ) );

    $page_meta->add_field( array(
        'name'             => esc_html__( 'Page layout', 'wp-coupon' ),
        'desc'             => esc_html__( 'Select page layout to display, leave empty to use theme option settings.', 'wp-coupon' ),
        'id'               => $prefix . 'layout',
        'type'             => 'select',
        'show_option_none' => esc_html__( 'Default (Theme options)', 'wp-coupon' ),
        'default'          => '',
        'options'          => array(
            'right-sidebar'   => esc_html__( 'Right sidebar', 'wp-coupon' ),
            'left-sidebar'    => esc_html__( 'Left sidebar', 'wp-coupon' ),
            'no-sidebar'      => esc_html__( 'No sidebar', 'wp-coupon' ),
        ),
    ) );

    $page_meta->add_field( array(
        'name'             => esc_html__( 'Display content in shadow box', 'wp-coupon' ),
        'desc'             => esc_html__( 'Wrapper content by shadow box.', 'wp-coupon' ),
        'id'               => $prefix . 'shadow_box',
        'type'             => 'select',
        'default'          => 'yes',
        'options'          => array(
            'no'    => esc_html__( 'No, display by default', 'wp-coupon' ),
            'yes'   => esc_html__( 'Yes, Display content in a shadow box', 'wp-coupon' ),
        ),
    ) );

    $page_meta->add_field( array(
        'name'             => esc_html__( 'Custom page header', 'wp-coupon' ),
        'desc'             => esc_html__( 'Custom page header.', 'wp-coupon' ),
        'id'               => $prefix . 'show_header',
        'type'             => 'select',
        'show_option_none' => esc_html__( 'Default (Theme options)', 'wp-coupon' ),
        'default'          => 'on',
        'options'          => array(
            'on'    => esc_html__( 'Show page title', 'wp-coupon' ),
            'off'   => esc_html__( 'Hide page title', 'wp-coupon' ),

        ),
    ) );

    $page_meta->add_field( array(
        'name'          => esc_html__( 'Hide breadcrumb', 'wp-coupon' ),
        'id'            => $prefix . 'hide_breadcrumb',
        'desc'          => sprintf( esc_html__( 'Check this if you want to hide breadcrumb. NOTE: you must install plugin %1$s to use Breadcrumb.', 'wp-coupon' ),  '<a target="_blank" href="'.admin_url( 'update.php?action=install-plugin&plugin=breadcrumb-navxt&_wpnonce='.wp_create_nonce() ).'">'.esc_html__( 'Breadcrumb Navxt', 'wp-coupon' ).'</a>' ),
        'type'          => 'checkbox',
        //'default'       => 'on'
    ) );

    $page_meta->add_field( array(
        'name'          => esc_html__( 'Custom page title', 'wp-coupon' ),
        'id'            => $prefix . 'custom_title',
        'desc'          => esc_html__( 'Display page title difference the title above.', 'wp-coupon' ),
        'type'          => 'text_medium',
    ) );

    $page_meta->add_field( array(
        'name'          => esc_html__( 'Hide Header cover', 'wp-coupon' ),
        'id'            => $prefix . 'hide_cover',
        'desc'          => esc_html__( 'Check this if you want to hide header cover', 'wp-coupon' ),
        'type'          => 'checkbox',
        //'default'       => 'on'
    ) );

    $page_meta->add_field( array(
        'name'          => esc_html__( 'Cover background image', 'wp-coupon' ),
        'id'            => $prefix . 'cover_image',
        'type'          => 'file',
    ) );

    $page_meta->add_field( array(
        'name'    => esc_html__( 'Cover background color', 'wp-coupon' ),
        'id'      => $prefix . 'cover_color',
        'type'    => 'colorpicker',
        'default' => '',
    ) );


    if ( wpcoupon_is_wc() ) {
        $shop_id = wc_get_page_id( 'shop' );
        $page_meta->add_field(array(
            'name' => esc_html__('Number products to show', 'wp-coupon'),
            'id' => $prefix . 'shop_number_products',
            'type' => 'text',
            'default' => '',
            'show_on_cb' => 'wpcoupon_metabox_show_on_cb',
            'show_on_page' => $shop_id, // Specific post IDs to display this metabox
        ));

        $page_meta->add_field(array(
            'name' => esc_html__('Number products per row', 'wp-coupon'),
            'id' => $prefix . 'shop_number_products_per_row',
            'type' => 'select',
            'default' => '',
            'show_on_cb' => 'wpcoupon_metabox_show_on_cb',
            'show_on_page' => $shop_id, // Specific post IDs to display this metabox
            'show_option_none' => esc_html__( 'Default', 'wp-coupon' ),
            'options'          => array(
                '2'   => esc_html__( '2 columns', 'wp-coupon' ),
                '3'   => esc_html__( '3 Columns', 'wp-coupon' ),
                '4'   => esc_html__( '4 Columns', 'wp-coupon' ),
                '5'   => esc_html__( '5 Columns', 'wp-coupon' ),
                '6'   => esc_html__( '6 Columns', 'wp-coupon' ),
            ),
        ));


    }



}



add_action( 'cmb2_admin_init', 'wpcoupon_register_coupon_store_taxonomy_metabox' );
/**
 * Hook in and add a metabox to add fields to taxonomy terms
 */
function wpcoupon_register_coupon_store_taxonomy_metabox() {
    $prefix = '_wpc_';

    /**
     * Metabox to add fields to coupon store
     */
    $store_meta = new_cmb2_box( array(
        'id'               => $prefix . 'store_meta',
        'title'            => esc_html__( 'Store Descriptions', 'wp-coupon' ),
        'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
        'taxonomies'       => array( 'coupon_store' ), // Tells CMB2 which taxonomies should have these fields
        // 'new_term_section' => true, // Will display in the "Add New Category" section
    ) );

	$store_meta->add_field( array(
        'name'          => esc_html__( 'Home URL', 'wp-coupon' ),
        'id'            => $prefix . 'store_url',
        'desc'          => esc_html__( 'Store Website home page URL.', 'wp-coupon' ),
        'type'          => 'text_url',
        'attributes'    => array(
            'placeholder'   => esc_html__( 'http://example.com', 'wp-coupon' ),
        ),
    ) );

    $store_meta->add_field( array(
        'name'          => esc_html__( 'Affiliate URL', 'wp-coupon' ),
        'id'            => $prefix . 'store_aff_url',
        'desc'          => esc_html__( 'Store Affiliate URL.', 'wp-coupon' ),
        'type'          => 'text_url',
        'attributes'    => array(
            'placeholder'   => esc_html__( 'http://example.com', 'wp-coupon' ),
        ),
    ) );


    $store_meta->add_field( array(
        'name'          => esc_html__( 'Auto generate thumbnail', 'wp-coupon' ),
        'desc'          => esc_html__( 'Auto download store home page screenshoot and set it as thumbnail for this store if store url is correct. This function is disable automatically if the thumbnail bellow has data.', 'wp-coupon' ),
        'id'            => $prefix . 'auto_thumbnail',
        'type'          => 'checkbox'
    ) );

    $store_meta->add_field( array(
        'name'    => esc_html__( 'Thumbnail', 'wp-coupon' ),
        'id'      => $prefix . 'store_image',
        'type'    => 'file',
        // Optional:
        'options' => array(
            'url' => false, // Hide the text input for the url
            //'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
        ),
    ) );

	$store_meta->add_field( array(
        'name'          => esc_html__( 'Custom store heading', 'wp-coupon' ),
        'id'            => $prefix . 'store_heading',
        'desc'          => esc_html__( 'The title will display in single store, example: Macy\'s Coupon Code and Deals, if empty then store custom heading from theme option will be used. You can use %store_name% for current store name.', 'wp-coupon' ),
        'type'          => 'text_medium',
        'sanitization_cb'    => 'cmb2_html_content_sanitize'
    ) );

    $store_meta->add_field( array(
        'name'          => esc_html__( 'Featured Store', 'wp-coupon' ),
        'desc'          => esc_html__( 'Check this if you want to this store is featured.', 'wp-coupon' ),
        'id'            => $prefix . 'is_featured',
        'type'          => 'checkbox'
    ) );


	$store_meta->add_field( array(
        'name'     => esc_html__( 'Extra Info', 'wp-coupon' ),
        'desc'     => esc_html__( 'This content display after product listing on single store page.', 'wp-coupon' ),
        'id'       => $prefix . 'extra_info',
        'type'     => 'wysiwyg',
        'options' => array(
            'wpautop' => true, // use wpautop?
            'media_buttons' => true, // show insert/upload button(s)
            ///'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
            'textarea_rows' => get_option('default_post_edit_rows', 6), // rows="..."
            'tabindex' => '',
            'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
            'editor_class' => '', // add extra class(es) to the editor textarea
            'teeny' => false, // output the minimal editor config used in Press This
            'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
            'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
            'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
        ),
        'on_front' => true,
    ) );


    /**
     * Metabox to add fields to Coupon categories
     */
    $cat_meta = new_cmb2_box( array(
        'id'               => $prefix . 'coupon_category_meta',
        'title'            => esc_html__( 'Category info', 'wp-coupon' ),
        'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
        'taxonomies'       => array( 'coupon_category' ), // Tells CMB2 which taxonomies should have these fields
        // 'new_term_section' => true, // Will display in the "Add New Category" section
    ) );

    $cat_meta->add_field( array(
        'name'          => esc_html__( 'Icon', 'wp-coupon' ),
        'id'            => $prefix . 'icon',
        'type'          => 'icon',
        'desc'          => 'Category icon',
    ) );


    $cat_meta->add_field( array(
        'name'    => esc_html__( 'Image', 'wp-coupon' ),
        'desc'    => 'The image use as thumbnail on single category page',
        'id'      => $prefix . 'cat_image',
        'type'    => 'file',
        // Optional:
        'options' => array(
            'url' => false, // Hide the text input for the url
            //'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
        ),
    ) );


}
