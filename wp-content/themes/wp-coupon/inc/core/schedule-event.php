<?php

/*
if ( ! is_admin() ) {
    echo date( 'Y-m-d H:i:s', microtime( true ) );
}

function wpcoupon_additional_schedules($schedules) {
    // interval in seconds
    $schedules['every1min'] = array('interval' => 1*60, 'display' => 'Every two minutes');
    return $schedules;
}
add_filter('cron_schedules', 'wpcoupon_additional_schedules');

*/


function wpcoupon_post_expires_status() {
    $args = array(
        'public'                    => false,
        'internal'                  => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'exclude_from_search'       => true,
        'label_count'               => _n_noop( 'Expires <span class="count">(%s)</span>', 'Expires <span class="count">(%s)</span>', 'wp-coupon' ),
    );
    register_post_status( 'expires', $args );
}
add_action( 'init', 'wpcoupon_post_expires_status', 0 );


function wp_coupon_get_schedule_time( $post_id ){
    $expired = absint( get_post_meta( $post_id, '_wpc_expires', true ) );
    if ( $expired > 0 ) {
        $expired += absint( wpcoupon_get_option( 'coupon_expires_time', 604800 ) );
    }

    return $expired;
}

add_action( "wp_insert_post", 'wp_coupon_future_post_hook', 99, 2 );


function wp_coupon_future_post_hook( $post_id,  $post ) {
    if ( get_post_type( $post ) != 'coupon' ) {
        return ;
    }
    wp_clear_scheduled_hook( 'wp_coupon_future_coupon', array( $post->ID ) );
    $expired = wp_coupon_get_schedule_time( $post->ID );
    if ( $expired <= 0 ) {
        return;
    }
   //  echo $expired; die();
    if ( $expired <= microtime( true ) ) { // move to trash right now
        wpcoupon_check_and_move_expires_coupon( $post_id );
    }
    wp_schedule_single_event( $expired , 'wp_coupon_future_coupon', array( $post->ID ) );

}
add_action( 'wp_coupon_future_coupon', 'wpcoupon_check_and_move_expires_coupon',   10, 1 );

/**
 * Remove coupon expired
 *
 * Invoked by cron 'wp_coupon_future_coupon' event.
 *
 *
 * @param int|WP_Post $post_id Post ID or post object.
 */
function wpcoupon_check_and_move_expires_coupon( $post_id ) {

    $action = wpcoupon_get_option( 'coupon_expires_action' );

    if ( $action == '' || ! in_array( $action, array( 'set_status', 'remove' ) ) ){
        return;
    }

    $post = get_post( $post_id );

    if ( empty( $post ) ) {
        return;
    }

    if ( 'expires' == $post->post_status ) {
        return;
    }

    $time = wp_coupon_get_schedule_time( $post->ID );
    if ( $time <= 0 ) {
        return ;
    }


    if ( $time > microtime( true ) ) {
        wp_clear_scheduled_hook( 'wp_coupon_future_coupon', array( $post->ID ) ); // clear anything else in the system
        wp_schedule_single_event( $time, 'wp_coupon_future_coupon', array( $post->ID ) );
        return;
    }


    wp_clear_scheduled_hook( 'wp_coupon_future_coupon', array( $post->ID ) );
    if ( $action == 'remove' ) {
        // clear event schedule
        wp_delete_post( $post->ID , true );
    } else {
        global $wpdb;
        $wpdb->update(
            $wpdb->posts,
            array(
                'post_status' => 'expires',
            ),
            array( 'ID' => $post->ID ),
            array(
                '%s',	// value1
            ),
            array( '%d' )
        );
    }

}