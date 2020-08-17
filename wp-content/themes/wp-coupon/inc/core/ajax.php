<?php
/**
 * Ajax Handle
 */
add_action( 'wp_ajax_wpcoupon_coupon_ajax', 'wpcoupon_coupon_ajax' );
add_action( 'wp_ajax_nopriv_wpcoupon_coupon_ajax', 'wpcoupon_coupon_ajax' );


add_action( 'wp_ajax_wpcoupon_coupon_ajax_search', 'wpcoupon_coupon_ajax_search' );
add_action( 'wp_ajax_nopriv_wpcoupon_coupon_ajax_search', 'wpcoupon_coupon_ajax_search' );

function wpcoupon_ajax_search_coupon_init(){
	if ( isset ( $_REQUEST['ajax_sc'] ) && $_REQUEST['ajax_sc'] != '') {
		wpcoupon_coupon_ajax_search();
	}

}
add_action( 'init', 'wpcoupon_ajax_search_coupon_init' );


function wpcoupon_coupon_ajax_search(){
    global $wpdb;
    $data =  array();
    $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
    if ( $action ){
    	$sql_tax = "IN ('coupon_store', 'coupon_category' )";
    } else {
	    $sql_tax = " = 'coupon_store' ";
    }

    $s = isset( $_REQUEST['ajax_sc'] ) ? (string) $_REQUEST['ajax_sc'] : '';
    $s = trim( $s );
    $_t = microtime( true );
    if ( strlen( $s ) > 0 ) {
        $n = apply_filters('ajax_coupon_search_num_posts', 8);
        $sql = "SELECT * FROM  $wpdb->terms 
         LEFT JOIN $wpdb->term_taxonomy ON {$wpdb->terms}.`term_id` = {$wpdb->term_taxonomy}.term_id 
         WHERE 
            {$wpdb->term_taxonomy}.`taxonomy` $sql_tax
            AND  {$wpdb->terms}.`name` LIKE %s 
        ORDER BY  {$wpdb->terms}.`name` ASC
        LIMIT 0, %d";
        $sql = $wpdb->prepare($sql, '%' . $wpdb->esc_like( $s ) . '%', $n);

        foreach (( array )$wpdb->get_results($sql) as $k => $store) {
        	if( $store ->taxonomy == 'coupon_category' ) {
		        $image_id = get_term_meta( $store->term_id, '_wpc_cat_image_id', true );
		        $thumb = false;
		        if ( $image_id > 0 ) {
			        $image= wp_get_attachment_image_src( $image_id, 'medium' );
			        if ( $image ) {
				        $thumb = '<img src="'.esc_attr( $image[0] ).'" alt=" ">';
			        }
		        }

		        if ( ! $thumb ) {
			        $icon = get_term_meta( $store->term_id, '_wpc_icon', true );
			        if ( trim( $icon ) !== '' ){
				        $thumb = '<i class="circular '.esc_attr( $icon ).'"></i>';
			        }
		        }

		        $item =  array(
			        "id" => $store->term_id,
			        "title" => $store->name,
			        "url" =>  get_term_link( $store ),
			        "image" => $thumb,
			        // "description" => sprintf(_n('%d Coupon', '%d Coupons', $n, 'wp-coupon'), $n)
		        );

	        } else {
		        wpcoupon_setup_store($store);
		        $item =  array(
			        "id" => wpcoupon_store()->term_id,
			        "title" => wpcoupon_store()->name,
			        "url" => wpcoupon_store()->get_url(),
			        "home" => wpcoupon_store()->get_home_url( true ),
			        "image" => wpcoupon_store()->get_thumbnail(),
			        // "description" => sprintf(_n('%d Coupon', '%d Coupons', $n, 'wp-coupon'), $n)
		        );
	        }

            //$n = wpcoupon_store()->count;
            $data[] = $item;
        }
    }

    $results =  array(
        'success' => true,
        'results' => $data,
        't' => microtime(  true ) - $_t,
    );

    $results = apply_filters( 'wp_coupon_ajax_search_result', $results );
    wp_send_json( $results );
    die();
}

function wpcoupon_coupon_ajax() {
    $nonce = isset( $_REQUEST['_wpnonce'] ) ?  $_REQUEST['_wpnonce'] : false;
    if (  ! wp_verify_nonce( $nonce ) ) {
        die( 'Security check' );
    }

    $doing = isset( $_REQUEST['st_doing'] ) ?  $_REQUEST['st_doing'] : ( isset( $_GET['st_doing'] ) ? $_GET['st_doing'] : ( isset( $_POST['st_doing'] ) ? $_POST['st_doing'] : false ) );

    switch ( $doing ) {

        case 'ajax_search':
            $s = isset( $_REQUEST['s'] ) ? (string) $_REQUEST['s'] : '';
            $n =  apply_filters( 'ajax_coupon_search_num_posts', 8 );
            $args =  wp_parse_args(array(
                'name__like' => trim( $s ),
                'number' => $n
            ), array() );

            $results =  array(
                'success' => true,
                'results' => wpcoupon_get_stores_search( $args ),
            );

            $results = apply_filters( 'wp_coupon_ajax_search_result', $results );
            wp_send_json( $results );
            die();
            break;

        case 'load_coupons':
        case 'load_category_coupons':
        case 'load_popular_coupons':
        case 'load_ending_soon_coupons':

            wp_send_json_success( wpcoupon_ajax_coupons( $doing ) );
            break;

        case 'load_store_coupons':
            wp_send_json_success( wpcoupon_ajax_store_coupons( ) );
            break;

        case 'get_coupon_modal':
            $hash = isset( $_REQUEST['hash'] ) ?  $_REQUEST['hash'] : '';
            ob_start();

            $hash = str_replace( '#coupon-id-', '', $hash );

            if ( is_numeric( $hash ) ) {
                global $post;
                $post = get_post( $hash );
                if ( $post ) {
                    wpcoupon_setup_coupon( $post );
                    get_template_part( 'loop/coupon-modal' );
                }
            }

            $content = ob_get_clean();

            wp_send_json_success( $content );
            break;

        case 'send_mail_to_friend':
            $email = $_POST['email'];
            $coupon_id = intval( $_POST['coupon_id'] );
            wpcoupon_send_coupon_to_email( $email , $coupon_id );
            wp_send_json_success( esc_html__( 'Your email has been sent successfully', 'wp-coupon' ) );
            break;

        case 'get_coupon_comments':
            wp_send_json_success( wpcoupon_ajax_get_coupon_comments( ) );
            break;
        case 'tracking_coupon':
        	$nonce =  isset( $_REQUEST['_coupon_nonce'] ) ? $_REQUEST['_coupon_nonce'] : '';
        	if ( ! wp_verify_nonce( $nonce, '_coupon_nonce' ) ) {
        		die( 'security_check' );
	        }
            WPCoupon_Coupon_Tracking::update_used( $_REQUEST['coupon_id'] );
            wp_send_json_success(  );
            break;

        case 'vote_coupon':
            $type = isset( $_REQUEST['vote_type'] ) ? $_REQUEST['vote_type'] : 'up';
            if ( $type != 'down' ){
                WPCoupon_Coupon_Tracking::vote( $_REQUEST['coupon_id'], 1 );
            } else {
                WPCoupon_Coupon_Tracking::vote( $_REQUEST['coupon_id'], -1 );
            }

            wp_send_json_success( );
            break;

        case 'new_comment':
            $r =  wpcoupon_new_coupon_comments();
            if ( $r ){
                wp_send_json_success( esc_html__( 'Your comment submitted.', 'wp-coupon' ) );
            } else {
                wp_send_json_error( esc_html__( 'Something wrong! Please try again later.', 'wp-coupon' ) );
            }
            break;

        case 'add_favorite':

            $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) :  false;
            WPCoupon_User::add_to_favorite( $id );

            $html = '';
            if ( is_user_logged_in() ) {
                ob_start();
                ob_end_clean();
                ob_start();
                $user = wp_get_current_user(  );
                WPCoupon_User::recent_favorite_stores_box( $user );
                $html = ob_get_clean();
            }
            wp_send_json_success( $html );

            break;

        case 'delete_favorite':

            $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) :  false;
            WPCoupon_User::delete_favorite( $id );
            $html = '';
            if ( is_user_logged_in() ) {
                ob_start();
                ob_end_clean();
                ob_start();
                $user = wp_get_current_user( );
                WPCoupon_User::recent_favorite_stores_box( $user );
                $html = ob_get_clean();
            }
            wp_send_json_success( $html );

            break;

        case 'save_coupon':
            $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) :  false;
            WPCoupon_User::save_coupon( $id );

            $html = '';
            if ( is_user_logged_in() ) {
                ob_start();
                ob_end_clean();
                ob_start();
                $user = wp_get_current_user(  );
                WPCoupon_User::recent_saved_coupons_box( $user );
                $html = ob_get_clean();
            }
            wp_send_json_success( $html );

            break;

        case 'remove_saved_coupon':
            $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) :  false;
            WPCoupon_User::remove_saved_coupon( $id );
            $html = '';
            if ( is_user_logged_in() ) {
                ob_start();
                ob_end_clean();
                ob_start();
                $user = wp_get_current_user(  );
                WPCoupon_User::recent_saved_coupons_box( $user );
                $html = ob_get_clean();
            }
            wp_send_json_success( $html );
            break;

    }

    do_action( 'wpcoupon_coupon_ajax', $doing );

    wp_send_json_error( array( 'msg' => esc_html__( 'Unknown request.','wp-coupon' ) ) );
}
