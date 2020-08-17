<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package WP Coupon
 */


/**
 * Check if enable single store
 *
 * @return bool|string
 */
function wpcoupon_is_single_enable(){
    return wpcoupon_get_option( 'enable_single_coupon', false );
}


/**
 * Maybe show coupon thumbnail in the loop
 *
 * @return bool
 */
function wpcoupon_maybe_show_coupon_thumb( $type = '' ){

    if ( is_tax( 'coupon_store' ) || $type == 'store' ) {
        $logo_option = wpcoupon_get_option( 'coupon_store_show_thumb' );
    } elseif ( is_tax( 'coupon_category' ) || $type == 'category' ) {
        $logo_option = wpcoupon_get_option( 'coupon_cate_show_thumb' );
    } else {
        $logo_option = wpcoupon_get_option( 'coupon_item_logo' );
    }

    if ( $logo_option == 'save_value' ) {
        if ( get_post_meta( wpcoupon_coupon()->ID, '_wpc_coupon_save', true ) ) {
            return $logo_option;
        }

        if ( get_post_meta( wpcoupon_coupon()->ID, '_wpc_free_shipping', true ) ) {
            return $logo_option;
        }

        return false;
    }

    if ( $logo_option == 'hide_if_no_thumb' ) {
        $has_thumb = has_post_thumbnail( wpcoupon_coupon()->ID );
    } elseif( $logo_option == 'hide' ){
        $has_thumb = false;
    } else {
        $has_thumb = wpcoupon_coupon()->has_thumb();

    }

    return $has_thumb;
}


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function wpcoupon_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpcoupon_page_menu_args' );

/**
 * Output the status of widets for footer column.
 *
 */
function wpcoupon_sidebar_desc( $sidebar_id ) {

	$desc           = '';
	$column         = str_replace( 'footer-', '', $sidebar_id );
	$footer_columns = wpcoupon_get_option('footer_columns');

	if ( $column > $footer_columns ) {
		$desc = esc_html__( 'This widget area is currently disabled. You can enable it Theme Options - Footer section.', 'wp-coupon' );
	}

	return esc_html( $desc );
}

/**
 * Browser detection body_class() output
 */
function wpcoupon_browser_body_class($classes) {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
        if($is_lynx) $classes[] = 'lynx';
        elseif($is_gecko) $classes[] = 'gecko';
        elseif($is_opera) $classes[] = 'opera';
        elseif($is_NS4) $classes[] = 'ns4';
        elseif($is_safari) $classes[] = 'safari';
        elseif($is_chrome) $classes[] = 'chrome';
        elseif($is_IE) {
                $classes[] = 'ie';
                if(preg_match('/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version))
                $classes[] = 'ie'.$browser_version[1];
        } else $classes[] = 'unknown';
        if($is_iphone) $classes[] = 'iphone';
        if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
                 $classes[] = 'osx';
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
                 $classes[] = 'linux';
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
                 $classes[] = 'windows';
           }
        return $classes;
}
add_filter('body_class','wpcoupon_browser_body_class');


function wpcoupon_set_html_content_type() {
	return 'text/html';
}



//Adding the Open Graph in the Language Attributes
function wpcoupon_add_opengraph_doctype( $output ) {
    return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'wpcoupon_add_opengraph_doctype');


/**
 * Remove OG Image If have from plugin Yoat SEO
 */
function wpcoupon_remove_og_img(){
    if ( is_tax( 'coupon_store' ) ) {
        add_filter('wpseo_og_og:image', '__return_false');
        add_filter('wpseo_og_og_image', '__return_false');
    }
}
add_action( 'wp', 'wpcoupon_remove_og_img', 97 );


function wpcoupon_toggle_content_more( $content ) {
    $more_content = false;
    $contents = explode(  '<!--more-->', $content );
    if ( count( $contents ) > 1 ) {
        $html = apply_filters( 'the_content', $contents[ 0 ] );
        unset( $contents[ 0 ] );
        $more_content = apply_filters( 'the_content', join( " \n\r ", $contents ) );
    } else {
        $html = apply_filters( 'the_content', $content );
    }

    if ( $more_content ) {
        $html = '<div class="content-toggle"><div class="content-less">' . $html . '</div>';
        $html .= '<div class="content-more">' . $more_content . '</div>';
        $html .= '<a class="show-more" href="#">' . esc_html__('Read more', 'wp-coupon') . '</a></div>';
    }

    return $html;

}


