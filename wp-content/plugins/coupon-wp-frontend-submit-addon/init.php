<?php
/**
 * Plugin Name:       Coupon WP Frontend Submit Addon
 * Plugin URI:        http://famethemes.com/plugins/coupon-wp-frontend-submit-addon/
 * Description:       Submit coupon on front-end.
 * Version:           1.0.4
 * Author:            famethemes
 * Author URI:        http://famethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-coupon-submit
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


define('WP_COUPON_SUBMIT_URL', trailingslashit(plugins_url('', __FILE__)));
define('WP_COUPON_SUBMIT_PATH', trailingslashit(plugin_dir_path(__FILE__)));

add_action( 'plugins_loaded', 'wp_coupon_submit_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wp_coupon_submit_load_textdomain() {
    load_plugin_textdomain( 'wp-coupon-submit', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

function wp_coupon_submit_init(){
    require_once WP_COUPON_SUBMIT_PATH . 'coupon-submit.php';
}

require_once WP_COUPON_SUBMIT_PATH . 'widget.php';
add_action( 'init', 'wp_coupon_submit_init' );

if ( is_admin() ) {
    require_once WP_COUPON_SUBMIT_PATH . 'options.php';
}
