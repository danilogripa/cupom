<?php

////////////////////////////
// API ENDPOINTS
////////////////////////////

//UNINSTALL ENDPOINT
function fca_ga_uninstall_ajax() {
	
	$msg = esc_textarea( $_REQUEST['msg'] );
	$nonce = $_REQUEST['nonce'];
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_ga_uninstall_nonce') == 1;

	if ( $nonceVerified && !empty( $msg ) ) {
		
		$url =  "https://api.fatcatapps.com/api/feedback.php";
				
		$body = array(
			'product' => 'gabyfca',
			'msg' => $msg,		
		);
		
		$args = array(
			'body' => json_encode( $body ),		
		);		
		
		$return = wp_remote_post( $url, $args );
		
		wp_send_json_success( $msg );

	}
	wp_send_json_error( $msg );

}
add_action( 'wp_ajax_fca_ga_uninstall', 'fca_ga_uninstall_ajax' );

//ADD AN EVENT FOR DRIP SUBSCRIBER FOR ACTIVATION/DEACTIVATION OF PLUGIN
function fca_ga_api_action( $action = '' ) {
	$tracking = get_option( 'fca_ga_activation_status' );
	if ( $tracking !== false ) {
		$user = wp_get_current_user();
		$url =  "https://api.fatcatapps.com/api/activity.php";
		
		$body = array(
			'user' => $user->user_email,
			'action' => $action,		
		);
		
		$args = array(
			'body' => json_encode( $body ),		
		);		
		
		$return = wp_remote_post( $url, $args );
		
		return true;
	}
	
	return false;
	
}