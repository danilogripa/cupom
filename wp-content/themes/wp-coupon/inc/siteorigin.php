<?php
/**
 * Add more settings for widget
 *
 * @param $fields
 * @return mixed
 */
function wpcoupon_custom_widget_style_fields($fields) {
    $fields['wpcoupon_boxed_mod'] = array(
        'name'        => esc_html__('Boxed mod', 'wp-coupon'),
        'type'        => 'select',
        'options'   => array (
            'default-box' => esc_html__( 'Default', 'wp-coupon'),
            'shadow-box'    => esc_html__( 'Shadow box', 'wp-coupon'),
        ),
        'group'       => 'design',
        //'description' => esc_html__('If enabled, the background image will have a parallax effect.', 'wp-coupon'),
        'priority'    => 3,
    );

    return $fields;
}
add_filter( 'siteorigin_panels_widget_style_fields', 'wpcoupon_custom_widget_style_fields' );

/**
 * Add custom css classes for widget
 *
 * @param $attributes
 * @param $args
 * @return mixed
 */
function wpcoupon_custom_widget_style_attributes( $attributes, $args ) {
    if( isset( $args['wpcoupon_boxed_mod'] ) ) {
        array_push($attributes['class'],  $args['wpcoupon_boxed_mod'] );
    }

    return $attributes;
}

add_filter('siteorigin_panels_widget_style_attributes', 'wpcoupon_custom_widget_style_attributes', 10, 2);


/**
 * Change default SiteOrigin Page Builder settings
 *
 * @param $settings
 * @return mixed
 */
function wpcoupon_siteorigin_panels_settings_defaults( $settings ){
    /**
     * @todo change default margin bottom to fit with desgign
     */
    $settings['responsive'] = false;
    $settings['tablet-layout'] = 1;
    $settings['margin-bottom'] = false;
    $settings['margin-sides'] =  50;
    $settings['mobile-width'] =  false; // 790
    return $settings;
}
add_filter( 'siteorigin_panels_settings_defaults', 'wpcoupon_siteorigin_panels_settings_defaults' );


function wpcoupon_siteorigin_panels_css_row_gutter( $margin_side, $gird, $gi = null, $panel_data = array() ){
    if ( isset( $gird['style'] ) ) {
        if ( isset(  $gird['style']['gutter'] )  && $gird['style']['gutter'] != '' ) {
            return $margin_side;
        }
    }
    return false;
}
add_filter( 'siteorigin_panels_css_row_gutter', 'wpcoupon_siteorigin_panels_css_row_gutter', 35, 4 );


/**
 * Add cusstom class for row
 *
 * @param $style_attributes
 * @param array $style_args
 * @return mixed
 */
function wpcoupon_siteorigin_panels_row_classes( $style_attributes , $style_args = array() ){
    $style_attributes['class'][] = 'wpc-pn-row-wrapper';
    return $style_attributes;
}
add_filter('siteorigin_panels_row_style_attributes', 'wpcoupon_siteorigin_panels_row_classes', 35, 2 );

/**
 * Remove setting layout
 *
 * @param array $fields
 * @return array
 */
function wpcoupon_siteorigin_settings_fields( $fields = array() ){
    unset( $fields['layout'] );
    return $fields;
}
add_filter( 'siteorigin_panels_settings_fields', 'wpcoupon_siteorigin_settings_fields' );

