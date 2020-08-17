<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wp_submit_options( $sections ){
    /*--------------------------------------------------------*/
    /* Submit settings
    /*--------------------------------------------------------*/
    $sections[] = array(
        'title'  => esc_html__( 'Coupon Submit', 'wp-coupon-submit'),
        'desc'   => '',
        'icon'   => 'el-icon-upload',
        'submenu' => true,
        'fields' => array(

            array(
                'id'       => 'cs_who_can_submit',
                'type'     => 'select',
                'title'    => esc_html__('Who can submit coupons', 'wp-coupon-submit'),
                'default'  => 'anyone',
                'options'  => array(
                    'anyone' =>  esc_html__('Anyone', 'wp-coupon-submit'),
                    'logged_in' =>  esc_html__('Only registered', 'wp-coupon-submit'),
                )
            ),

            array(
                'id'       => 'cs_enable_new_store',
                'type'     => 'switch',
                'title'    => esc_html__('Add new store', 'wp-coupon-submit'),
                'subtitle' => esc_html__('Allow user add new custom store on front-end.', 'wp-coupon-submit'),
                'default'  => true,
            ),

            array(
                'id'       => 'cs_enable_store_logo',
                'type'     => 'switch',
                'title'    => esc_html__('Custom store logo', 'wp-coupon-submit'),
                'subtitle' => esc_html__('Allow user upload custom store logo on front-end.', 'wp-coupon-submit'),
                'default'  => true,
            ),

            array(
                'id'       => 'cs_coupon_status',
                'type'     => 'select',
                'title'    => esc_html__('Coupon status', 'wp-coupon-submit'),
                'subtitle' => esc_html__('Coupon status when user submit.', 'wp-coupon-submit'),
                'default'  => 'default',
                'options'  => array(
                    'default' =>  esc_html__('Publish if submitted buy admin else pending', 'wp-coupon-submit'),
                    'pending' =>  esc_html__('Pending', 'wp-coupon-submit'),
                    'publish' =>  esc_html__('Publish', 'wp-coupon-submit'),
                )
            ),

        )
    );

    return $sections;
}

add_filter( 'wpcoupon_more_options_settings', 'wp_submit_options' );