<?php

if ( ! function_exists( 'st_debug' ) ) {
	/**
	 * Debug function
	 *
	 * @param $whatever
	 */
	function st_debug( $whatever, $var_dump = false ) {
		 echo '<pre>';
		if ( $var_dump ) {
			var_dump( $whatever );
		} else {
			print_r( $whatever );
		}
		echo '</pre>';
	}
}


/**
 * Remove Class Filter Without Access to Class Object
 *
 * In order to use the core WordPress remove_filter() on a filter added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove filters with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
 *
 * @param string $tag         Filter to remove
 * @param string $class_name  Class name for the filter's callback
 * @param string $method_name Method name for the filter's callback
 * @param int    $priority    Priority of the filter (default 10)
 *
 * @return bool Whether the function is removed.
 */
function wp_coupon_remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	global $wp_filter;

	// Check that filter actually exists first
	if ( ! isset( $wp_filter[ $tag ] ) ) {
		return false;
	}

	/**
	 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
	 * a simple array, rather it is an object that implements the ArrayAccess interface.
	 *
	 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
	 *
	 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
	 */
	if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
		// Create $fob object from filter tag, to use below
		$fob = $wp_filter[ $tag ];
		$callbacks = &$wp_filter[ $tag ]->callbacks;
	} else {
		$callbacks = &$wp_filter[ $tag ];
	}

	// Exit if there aren't any callbacks for specified priority
	if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
		return false;
	}

	// Loop through each filter for the specified priority, looking for our class & method
	foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

		// Filter should always be an array - array( $this, 'method' ), if not goto next
		if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
			continue;
		}

		// If first value in array is not an object, it can't be a class
		if ( ! is_object( $filter['function'][0] ) ) {
			continue;
		}

		// Method doesn't match the one we're looking for, goto next
		if ( $filter['function'][1] !== $method_name ) {
			continue;
		}

		// Method matched, now let's check the Class
		if ( get_class( $filter['function'][0] ) === $class_name ) {

			// WordPress 4.7+ use core remove_filter() since we found the class object
			if ( isset( $fob ) ) {
				// Handles removing filter, reseting callback priority keys mid-iteration, etc.
				$fob->remove_filter( $tag, $filter['function'], $priority );

			} else {
				// Use legacy removal process (pre 4.7)
				unset( $callbacks[ $priority ][ $filter_id ] );
				// and if it was the only filter in that priority, unset that priority
				if ( empty( $callbacks[ $priority ] ) ) {
					unset( $callbacks[ $priority ] );
				}
				// and if the only filter for that tag, set the tag to an empty array
				if ( empty( $callbacks ) ) {
					$callbacks = array();
				}
				// Remove this filter from merged_filters, which specifies if filters have been sorted
				unset( $GLOBALS['merged_filters'][ $tag ] );
			}

			return true;
		}
	}

	return false;
}

/**
 * Remove Class Action Without Access to Class Object
 *
 * In order to use the core WordPress remove_action() on an action added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove actions with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Action to remove
 * @param string $class_name  Class name for the action's callback
 * @param string $method_name Method name for the action's callback
 * @param int    $priority    Priority of the action (default 10)
 *
 * @return bool               Whether the function is removed.
 */
function wp_coupon_remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	wp_coupon_remove_class_filter( $tag, $class_name, $method_name, $priority );
}


add_action( 'init', 'flush_rewrite_rules' );


/**
 * Add rewrite for go out store
 *
 * domain-name.com/out/123
 *
 * @since 1.0.0
 */
function wpcoupon_add_rewrite_rules() {
	// Blog post feed links
	add_rewrite_rule( '^blog/(feed|rdf|rss|rss2|atom)/?$', 'index.php?feed=feed', 'top' );
	add_rewrite_rule( '^blog/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?feed=$matches[1]', 'top' );
	// Change default feed link to feed coupons
	add_rewrite_rule( '^(feed|rdf|rss|rss2|atom)/?$', 'index.php?home_feed=coupon&feed=$matches[1]', 'top' );
	// Redirect to coupon site
	$slug = wpcoupon_get_option( 'go_out_slug', 'out' );
	add_rewrite_rule( '^' . $slug . '/([0-9]+)/?', 'index.php?out=$matches[1]', 'top' );
	// Go to store redirect
	$slug = wpcoupon_get_option( 'go_store_slug', 'go-store' );
	add_rewrite_rule( '^' . $slug . '/([0-9]+)/?', 'index.php?go_store_id=$matches[1]', 'top' );

	$store_slug = trim( wpcoupon_get_option( 'rewrite_store_slug', '' ) );
	if ( ! $store_slug ) {
		$store_slug = 'store';
	}

	// [store/([^/]+)/page/?([0-9]{1,})/?$] => index.php?coupon_store=$matches[1]&paged=$matches[2]
	// [store/([^/]+)/?$] => index.php?coupon_store=$matches[1]
	// Store with page number in url
	add_rewrite_rule( '^' . $store_slug . '/([^/]+)/page/?([0-9]{1,})/?', 'index.php?coupon_store=$matches[1]&paged=$matches[2]', 'top' );

	// Store Feed link
	add_rewrite_rule( '^' . $store_slug . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?', 'index.php?coupon_store=$matches[1]&feed=$matches[1]', 'top' );

	// Share url
	add_rewrite_rule( '^' . $store_slug . '/([^/]+)/share/([0-9]+)/?', 'index.php?coupon_store=$matches[1]&share_id=$matches[2]', 'top' );

	if ( wpcoupon_get_option( 'enable_single_coupon', false ) ) {
		// Single coupon link
		add_rewrite_rule( '^' . $store_slug . '/([^/]+)/([^/]+)/?', 'index.php?coupon=$matches[2]', 'top' );
	} else {
		// Open coupon modal
		add_rewrite_rule( '^' . $store_slug . '/([^/]+)/([^/]+)/?', 'index.php?coupon_store=$matches[1]&coupon_id=$matches[2]', 'top' );
	}

}

/**
 * Add new query vars
 *
 * @see get_query_var()
 * @since 1.0.0
 */
function wpcoupon_rewrite_tags() {
	add_rewrite_tag( '%home_feed%', '([^&]+)' );
	add_rewrite_tag( '%out%', '([^&]+)' );
	add_rewrite_tag( '%go_store_id%', '([^&]+)' );
	add_rewrite_tag( '%share_id%', '([^&]+)' );
	add_rewrite_tag( '%coupon_id%', '([^&]+)' );
}

/**
 * Init rewrite setup
 */
add_action( 'init', 'wpcoupon_add_rewrite_rules', 11, 0 );
add_action( 'init', 'wpcoupon_rewrite_tags', 11, 0 );


add_action( 'init', 'wpcoupon_request_uri_setup' );
/**
 * Do set up with request uri
 */
function wpcoupon_request_uri_setup() {
	$GLOBALS['st_paged'] = 0;
	global $wp_rewrite;
	$matches = false;
	if ( $wp_rewrite->using_permalinks() ) {
		preg_match( '/page\/([0-9]+)/', $_SERVER['REQUEST_URI'], $matches );
	} else {
		preg_match( '/paged=([0-9]+)/', $_SERVER['REQUEST_URI'], $matches );
	}
	if ( $matches ) {
		$GLOBALS['st_paged'] = $matches[1];
	}

	if ( preg_match( '/(feed|rdf|rss|rss2|atom)/', $_SERVER['REQUEST_URI'], $matches_2 ) ) {

	}

	// var_dump( $matches_2 ); die();

	/**
	 * @see redirect_canonical
	 */
	// remove_filter('template_redirect','redirect_canonical');
}


/**
 * HANDLE IF IS OUT URL
 *
 * Redirect coupon aff url
 *
 * @since 1.0
 */
function wpcoupon_out_url_redirect() {

	if ( get_option( 'permalink_structure' ) != '' ) {
		$out = get_query_var( 'out' );
	} else {
		$out = isset( $_GET['out'] ) ? $_GET['out'] : 'false';
	}

	if ( is_numeric( $out ) ) {
		$id = intval( $out );
		$coupon = new WPCoupon_Coupon( $id );
		if ( $coupon->is_coupon() ) {
			WPCoupon_Coupon_Tracking::update_used( $coupon->ID );
			$url = $coupon->get_destination_url();
			if ( ! $url ) {
				$url = site_url( '/' );
			}

			ob_start();
			ob_get_clean(); // Make sure no header already sent error.
			wp_redirect( $url );
			die();
		}
	}
}
add_action( 'wp', 'wpcoupon_out_url_redirect', 5 );


/**
 * HANDLE IF IS STORE OUT URL
 *
 * Redirect coupon aff url
 *
 * @since 1.0
 */
function wpcoupon_store_out_url_redirect() {

	if ( get_option( 'permalink_structure' ) != '' ) {
		$out = get_query_var( 'go_store_id' );
	} else {
		$out = isset( $_GET['go_store_id'] ) ? $_GET['go_store_id'] : 'go_store_id';
	}
	if ( is_numeric( $out ) ) {
		$id = intval( $out );
		$store = new WPCoupon_Store( $id );
		if ( $store->is_store() ) {
			$go_out = get_term_meta( $store->term_id, '_wpc_go_out', true );
			$go_out = intval( $go_out );
			$go_out += 1;
			// tracking store out
			update_term_meta( $store->term_id, '_wpc_go_out', $go_out );
			do_action( 'wpcoupon_store_out_url_redirect' );
			$url = $store->get_website_url();
			if ( ! $url ) {
				$url = $store->get_url();
			}
			$status = apply_filters( 'wpcoupon_store_out_url_redirect_status', 301 );
			// if the link empty
			if ( $url ) {
				ob_start();
				ob_clean(); // Make sure no header already sent error.
				wp_redirect( $url, $status );
				die();
			}
		}
	}
}
add_action( 'wp', 'wpcoupon_store_out_url_redirect', 101 );

/**
 * Get paged number
 */
function wpcoupon_get_paged() {
	global $paged;
	if ( ! $paged ) {
		return intval( $GLOBALS['st_paged'] ) > 0 ? intval( $GLOBALS['st_paged'] ) : 1;
	}

	return $paged;
}


/**
 * Get registered_sidebars for setting options
 */
function wpcoupon_get_registered_sidebars() {
	global $wp_registered_sidebars;

	// st_debug( $wp_registered_sidebars );
	$a = array();
	foreach ( $wp_registered_sidebars as $k => $s ) {
		$a[ $k ] = $s['name'];
	}
	return $a;
}

/**
 * Convert number to html class name
 *
 * @param $number
 * @return bool
 */
function wpcoupon_number_to_html_class( $number ) {
	$words = array(
		'1' => 'one',
		'2' => 'two',
		'3' => 'three',
		'4' => 'four',
		'5' => 'five',
		'6' => 'six',
		'7' => 'seven',
		'8' => 'eight',
		'9' => 'nine',
		'10' => 'ten',
		'11' => 'eleven',
		'12' => 'twelve',
		'13' => 'thirteen',
		'14' => 'fourteen',
		'15' => 'fifteen',
		'16' => 'sixteen',
	);
	return $words[ $number ] ? $words[ $number ] : false;
}


/**
 * Download image form url
 *
 * @return bool
 */
function wpcoupon_download_image( $url, $name = '' ) {
	if ( ! $url || empty( $url ) ) {
		return false;
	}
	// These files need to be included as dependencies when on the front end.
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$file_array = array();
	// Download file to temp location.
	$file_array['tmp_name'] = download_url( $url );

	// If error storing temporarily, return the error.
	if ( empty( $file_array['tmp_name'] ) || is_wp_error( $file_array['tmp_name'] ) ) {
		// return $file_array['tmp_name'];
		return false;
	}

	if ( $name ) {
		$file_array['name'] = $name;
	} else {
		// Set variables for storage, fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file_array['tmp_name'], $matches );
		if ( ! empty( $matches ) ) {
			$file_array['name'] = basename( $matches[0] );
		} else {
			$file_array['name'] = uniqid( 'store-' ) . '.jpeg';
		}
	}

	// Do the validation and storage stuff.
	$id = media_handle_sideload( $file_array, 0 );

	// If error storing permanently, unlink.
	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );
		return false;
	}

	return $id;
}

/**
 * Download and save a website screenshot form a url.
 *
 * @param $website_url
 * @param string      $size
 * @return bool
 */
function wpcoupon_download_webshoot( $website_url, $size = '' ) {
	if ( ! $website_url || empty( $website_url ) ) {
		return false;
	}
	global $_wp_additional_image_sizes;
	if ( $size && ! is_array( $size ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
		$_size = $_wp_additional_image_sizes[ $size ];
	} else {
		$_size = false;
	}
	$_size = wp_parse_args(
		$_size,
		array(
			'width' => 800,
			'height' => 500,
		)
	);
	$p = parse_url( $website_url );
	$name = sanitize_title( $p['host'] ) . '.jpeg';
	$url = 'http://s.wordpress.com/mshots/v1/' . urlencode( $website_url ) . '?w=' . $_size['width'] . '&h=' . $_size['height'];
	$url = apply_filters( 'wpcoupon_webshoot_url', $url, $website_url, $_size );
	return wpcoupon_download_image( $url, $name );
}

/**
 * Convert number to string name
 * From 1 to 16
 *
 * @param $number
 * @return string
 */
function wpcoupon_number_to_column_class( $number ) {
	switch ( $number ) {
		case 1:
			return 'one';
		break;
		case 2:
			return 'two';
		break;
		case 3:
			return 'three';
		break;
		case 4:
			return 'four';
		break;
		case 5:
			return 'five';
		break;
		case 6:
			return 'six';
		break;
		case 7:
			return 'seven';
		break;
		case 8:
			return 'eight';
		break;
		case 9:
			return 'nine';
		break;
		case 10:
			return 'ten';
		break;
		case 11:
			return 'eleven';
		break;
		case 12:
			return 'twelve';
		break;
		case 13:
			return 'thirteen';
		break;
		case 14:
			return 'fourteen';
		break;
		case 15:
			return 'fifteen';
		break;
		case 16:
			return 'sixteen';
		break;
		default:
			return 'sixteen';
		break;
	}

}

/**
 * Change feed link for coupon
 * Redirect to store page and open modal
 *
 * @since 1.0.8
 *
 * @param $link
 * @return string
 */
function wpcoupon_change_coupon_feed_link( $link ) {
	$post = get_post();
	// if not a coupon return the link
	if ( get_post_type( $post ) != 'coupon' ) {
		return $link;
	}
	$link = wpcoupon_coupon( $post )->get_href();
	return $link;
}
add_filter( 'the_permalink_rss', 'wpcoupon_change_coupon_feed_link', 55 );

function wpcoupon_disable_feed_links() {
	if ( wpcoupon_get_option( 'disable_feed_links' ) ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
		remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
		remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
		remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
		remove_action( 'wp_head', 'index_rel_link' ); // index link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
		remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
		remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
	}
}
add_filter( 'init', 'wpcoupon_disable_feed_links', 55 );


/**
 * Query coupon feed
 *
 * @param $query
 * @see WP_Query
 * @see query_posts()
 */
function wpcoupon_coupons_feed( $wp_query ) {
	if ( is_feed() ) {
		if (
			get_query_var( 'home_feed' ) == 'coupon'
			|| isset( $wp_query->query['coupon_store'] ) // Store feed link
			|| ( isset( $wp_query->query['taxonomy'] ) && $wp_query->query['taxonomy'] == 'coupon_store' ) // Store feed link
			|| isset( $wp_query->query['coupon_category'] ) // Category feed link
			|| ( isset( $wp_query->query['taxonomy'] ) && $wp_query->query['taxonomy'] == 'coupon_category' ) // Category feed link
		) {
			$wp_query->set( 'post_type', 'coupon' );
		}
	}
}
add_action( 'pre_get_posts', 'wpcoupon_coupons_feed' );

/**
 * Change Blog posts feed link
 *
 * @see get_feed_link
 */
function wpcoupon_change_feed_link( $link, $feed ) {
	if ( is_home() ) {
		global $wp_rewrite;
		if ( false === strpos( $link, 'comments' ) ) {
			$permalink = $wp_rewrite->get_feed_permastruct();
			if ( '' != $permalink ) {
				if ( get_default_feed() == $feed ) {
					$feed = '';
				}
				if ( ! $feed ) {
					$feed = '/' . $feed;
				}
				$link = trailingslashit( home_url( 'blog' . $feed ) );
			}
		}
	}
	return $link;
}

add_filter( 'feed_link', 'wpcoupon_change_feed_link', 35, 2 );



