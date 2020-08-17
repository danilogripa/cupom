<?php
/**
 * Coupon store functions
 */


/**
 * Get ending soon coupons
 *
 * @param int $days_left
 * @param int $posts_per_page
 * @param int $paged
 * @return array
 */
function wpcoupon_get_ending_soon_coupons( $days_left = 1, $posts_per_page = 10, $paged = 1 ) {

	global $wp_query;
	$current = current_time( 'timestamp' );
	$next_day = $current + intval( $days_left ) * 24 * 3600;

	$args = array(
		'post_type'      => 'coupon',
		'posts_per_page' => $posts_per_page,
		'meta_key'       => '_wpc_expires',
		'paged'          => $paged,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_wpc_expires',
				'value'   => array( $current, $next_day ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			),

		),
		'orderby'         => 'meta_value_num',
		'order'           => 'asc',
	);

	$wp_query = new WP_Query( $args );
	return $wp_query->get_posts();
}



/**
 * Get coupons
 *
 * @since 1.0.0
 * @param $cat_id
 * @param int    $number_post
 * @return array
 */
function wpcoupon_get_coupons( $post_args = array(), $paged = 1, &$max_pages = 0 ) {
	global $wp_query;
	$default = array(
		'post_type'      => 'coupon',
		'paged'          => $paged,
		'posts_per_page' => apply_filters( 'st_coupons_number', get_option( 'posts_per_page' ) ),
		'orderby'       => 'date',
		'order'         => 'desc',
	);

	$args = wp_parse_args( $post_args, $default );

	if ( isset( $args['tax_query'] ) ) {
		$args['posts_per_page'] = wpcoupon_get_option( 'coupon_cate_number', 15 );
	}

	$args['post_status'] = 'publish';

	if ( isset( $post_args['hide_expired'] ) ) {
		unset( $args['hide_expired'] );
		if ( $post_args['hide_expired'] ) {
			$current = current_time( 'timestamp' );
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => '_wpc_expires',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => '_wpc_expires',
					'value'   => $current,
					'compare' => '>',
				),
			);
		}
	}

	$args = apply_filters( 'wpcoupon_get_coupons_args', $args );

	$wp_query = new WP_Query( $args );
	$max_pages = $wp_query->max_num_pages;
	return $wp_query->get_posts();
}



/**
 * Setup coupon data
 *
 * @since 1.0.0
 *
 * @param null $coupon
 * @return null
 */
function wpcoupon_setup_coupon( $coupon = null, $current_link = null ) {
	if ( $coupon instanceof WPCoupon_Coupon ) {
		return $coupon;
	}
	global $post;
	$post = get_post( $coupon );
	setup_postdata( $post );
	$GLOBALS['coupon'] = new WPCoupon_Coupon( $post, $current_link );

}

/**
 * Alias of WPCoupon_Coupon
 *
 * This function MUST use in the loop. Better if after function wpcoupon_setup_coupon
 *
 * @since 1.0,0
 *
 * @see WPCoupon_Coupon
 * @see wpcoupon_setup_coupon()
 *
 * @param null $coupon
 * @return null|WPCoupon_Coupon
 */
function wpcoupon_coupon( $coupon = null, $current_link = null ) {
	// check if coupon is set
	if ( ! $coupon && isset( $GLOBALS['coupon'] ) ) {
		$coupon = $GLOBALS['coupon'];
	} else {
		wpcoupon_setup_coupon( $coupon );
	}

	/*
	if ( ! $coupon ) {
		wpcoupon_setup_coupon( );
		$coupon = $GLOBALS['coupon'];
	}
	*/

	if ( $coupon instanceof WPCoupon_Coupon ) {
		return $coupon;
	}

	return new WPCoupon_Coupon( $coupon, $current_link );
}



class WPCoupon_Coupon {

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * ID of post author.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	public $post_author = 0;

	/**
	 * The post's local publication time.
	 *
	 * @var string
	 */
	public $post_date = '0000-00-00 00:00:00';

	/**
	 * The post's GMT publication time.
	 *
	 * @var string
	 */
	public $post_date_gmt = '0000-00-00 00:00:00';

	/**
	 * The post's content.
	 *
	 * @var string
	 */
	public $post_content = '';

	/**
	 * The post's title.
	 *
	 * @var string
	 */
	public $post_title = '';

	/**
	 * The post's excerpt.
	 *
	 * @var string
	 */
	public $post_excerpt = '';

	/**
	 * The post's status.
	 *
	 * @var string
	 */
	public $post_status = 'publish';

	/**
	 * Whether comments are allowed.
	 *
	 * @var string
	 */
	public $comment_status = 'open';

	/**
	 * Whether pings are allowed.
	 *
	 * @var string
	 */
	public $ping_status = 'open';

	/**
	 * The post's password in plain text.
	 *
	 * @var string
	 */
	public $post_password = '';

	/**
	 * The post's slug.
	 *
	 * @var string
	 */
	public $post_name = '';

	/**
	 * URLs queued to be pinged.
	 *
	 * @var string
	 */
	public $to_ping = '';

	/**
	 * URLs that have been pinged.
	 *
	 * @var string
	 */
	public $pinged = '';

	/**
	 * The post's local modified time.
	 *
	 * @var string
	 */
	public $post_modified = '0000-00-00 00:00:00';

	/**
	 * The post's GMT modified time.
	 *
	 * @var string
	 */
	public $post_modified_gmt = '0000-00-00 00:00:00';

	/**
	 * A utility DB field for post content.
	 *
	 * @var string
	 */
	public $post_content_filtered = '';

	/**
	 * ID of a post's parent post.
	 *
	 * @var int
	 */
	public $post_parent = 0;

	/**
	 * The unique identifier for a post, not necessarily a URL, used as the feed GUID.
	 *
	 * @var string
	 */
	public $guid = '';

	/**
	 * A field used for ordering posts.
	 *
	 * @var int
	 */
	public $menu_order = 0;

	/**
	 * The post's type, like post or page.
	 *
	 * @var string
	 */
	public $post_type = 'post';

	/**
	 * An attachment's mime type.
	 *
	 * @var string
	 */
	public $post_mime_type = '';

	/**
	 * Cached comment count.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	public $comment_count = 0;

	/**
	 * Stores the post object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @var string
	 */
	public $filter;

	/**
	 * Check coupon thumbnail
	 *
	 * @var string
	 */
	public $_thumb_id = 0;

	/**
	 * Current url
	 *
	 * The current URL of page that this coupon displaying, use for social share.
	 *
	 * @var string
	 */
	public $current_url = null;

	/**
	 * Percent success of user voted
	 *
	 * @var int
	 */
	public $percent_success = 100;

	/**
	 * Coupon WP post
	 *
	 * @var array|mixed|null|WP_Post
	 */
	public $post;

	/**
	 * @var WPCoupon_Store
	 */
	public $store;

	/**
	 * Check if still has more content after get_excerpt call
	 *
	 * @var $has_more_content
	 */
	public $has_more_content = false;

	/**
	 *  Construct function
	 *
	 * @param mixed  $p
	 * @param string $current_url
	 */
	function __construct( $p = null, $current_url = null ) {

		if ( ! is_object( $p ) ) {
			$p = get_post( $p );
		}

		$meta = array();

		if ( $p && $p->post_type == 'coupon' ) {
			foreach ( $p as $k => $v ) {
				$this->$k = $v;
			}

			$this->post = $p;

			if ( $p ) {
				$meta = get_post_custom( $p->ID );
			}
		}

		// default meta keys
		$default_meta = array(
			'_wpc_store'                     => '',
			'_wpc_coupon_type'               => '',
			// 'start_datetime'                => '',
			'_wpc_expires'                   => '',
			'_wpc_exclusive'                 => '',
			'_wpc_coupon_type_code'          => '',
			// '_wpc_coupon_type_sale'          => '',
			'_wpc_coupon_type_printable_id'  => '',
			'_wpc_coupon_type_printable'     => '',
			'_wpc_destination_url'           => '',
			// meta keys for tracking
			'_wpc_used'                      => 0,
			'_wpc_percent_success'           => 100,
			'_wpc_views'                     => 0,
			'_wpc_today'                     => '',
			'_wpc_vote_up'                   => 0,
			'_wpc_vote_down'                 => 0,
		);

		$meta = wp_parse_args( $meta, apply_filters( 'st_default_coupon_metas', $default_meta ) );

		// Setup meta key as property
		foreach ( $meta as $key => $values ) {
			if ( ! is_array( $values ) ) {
				$this->$key = $values;
			} else {
				$this->$key = maybe_unserialize( $values[0] );
			}
		}

		if ( ! isset( $this->_wpc_coupon_type ) ) {
			$this->_wpc_coupon_type = 'code';
		}

		// Set first Store
		if ( is_array( $this->_wpc_store ) ) {
			$this->_wpc_store = intval( current( $this->_wpc_store ) );
		} else {
			$this->_wpc_store = intval( $this->_wpc_store );
		}

		// Support Yoat SEO
		if ( defined( 'WPSEO_FILE' ) ) {
			$primary_store = get_post_meta( $this->ID, '_yoast_wpseo_primary_coupon_store', true );
			if ( $primary_store ) {
				$this->_wpc_store = $primary_store;
			}
		}

		if ( ! $this->_wpc_store ) {
			$term_list = wp_get_post_terms( $this->ID, 'coupon_store', array( 'fields' => 'ids' ) );
			if ( $term_list && ! is_wp_error( $term_list ) ) {
				$this->_wpc_store = current( $term_list );
			}
		}

		/**
		 * Hook to change property
		 *
		 * @since 1.2.3
		 */
		do_action_ref_array( 'wpcoupon_after_setup_coupon', array( &$this ) );

		$this->store = new WPCoupon_Store( $this->_wpc_store );
		$this->current_url = ( $current_url ) ? $current_url : $this->store->get_url();

	}

	/**
	 * Check if coupon exists
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_coupon() {
		return ( isset( $this->ID ) );
	}



	/**
	 * Get excerpt of coupon
	 *
	 * @param int $num_words
	 * @return string
	 */
	public function get_excerpt( $num_words = null, $more = 'null', $has_thumb = true ) {

		if ( ! $has_thumb ) {
			if ( isset( $GLOBALS['coupon_num_words_no_thumb'] ) && intval( $GLOBALS['coupon_num_words_no_thumb'] ) > 0 ) {
				$num_words = $GLOBALS['coupon_num_words_no_thumb'];
			} else {
				if ( $num_words <= 0 ) {
					$num_words = 16;
				}
			}
		} else {
			if ( isset( $GLOBALS['coupon_num_words'] ) && intval( $GLOBALS['coupon_num_words'] ) > 0 ) {
				$num_words = $GLOBALS['coupon_num_words'];
			} else {
				if ( $num_words <= 0 ) {
					$num_words = 10;
				}
			}
		}

		$num_words = apply_filters( 'coupon_num_words_excerpt', wpcoupon_get_option( 'coupon_num_words_excerpt', $num_words ), $has_thumb );

		if ( $num_words <= 0 ) {
			$num_words = 10;
		}

		if ( $this->post_excerpt != '' ) {
			$text = wp_trim_words( $this->post_excerpt, $num_words, '' );
		} else {
			$text = wp_trim_words( $this->post_content, $num_words, '' );
		}

		if ( trim( $text ) != trim( $this->post_content ) ) {
			$this->has_more_content = true;
			$text .= $more;
		} else {
			$this->has_more_content = false;
		}
		return $text;
	}

	/**
	 * Check coupon has expired
	 *
	 * @return bool
	 */
	function has_expired() {
		if ( ! $this->_wpc_expires || $this->_wpc_expires == '' ) {
			return false;
		}
		if ( wpcoupon_get_option( 'coupon_time_zone_local' ) ) {
			$time = current_time( 'timestamp' );
		} else {
			$time = gmmktime();
		}

		return $time > $this->_wpc_expires ? true : false;
	}

	/**
	 * If is exclusive coupon
	 *
	 * @return bool
	 */
	public function is_exclusive() {
		return strtolower( $this->_wpc_exclusive ) == 'on' ? true : false;
	}

	/**
	 * Get expires
	 *
	 * @see get_date_from_gmt
	 * @since 1.0.0
	 * @param null $date_format
	 * @return bool|string
	 */
	public function get_expires( $date_format = null, $show_date = false ) {

		if ( wpcoupon_get_option( 'coupon_time_zone_local' ) ) {
			$time = current_time( 'timestamp' );
		} else {
			$time = time();
		}

		if ( wpcoupon_get_option( 'coupon_human_time', false ) && $this->_wpc_expires ) {
			if ( ! $this->has_expired() ) {
				return sprintf( _x( '%s left', '%s = human-readable time difference', 'wp-coupon' ), human_time_diff( $time, $this->_wpc_expires ) );
			}
		}

		if ( ! $date_format ) {
			$date_format = get_option( 'date_format' );
		}

		if ( $this->_wpc_expires ) {
			if ( $show_date ) {
				return sprintf( __( 'Expires %s', 'wp-coupon' ), date_i18n( $date_format, $this->_wpc_expires ) );
			}
			if ( ! $this->has_expired() && ! $show_date ) {
				return sprintf( __( 'Expires %s', 'wp-coupon' ), date_i18n( $date_format, $this->_wpc_expires ) );
			} else {
				return esc_html__( 'Expired', 'wp-coupon' );
			}
		}
		return esc_html__( 'No Expires', 'wp-coupon' );
	}

	/**
	 * Get coupon type
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_type() {
		return $this->_wpc_coupon_type;
	}

	/**
	 * Get coupon code
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_code( $number = 'full', $no_code = false ) {
		$code = false;
		if ( is_numeric( $number ) ) {
			$number = absint( $number );
			$code = substr( $this->_wpc_coupon_type_code, - $number );
		} else {
			$code = $this->_wpc_coupon_type_code;
		}

		if ( $no_code && ! $code ) {
			$code = apply_filters( 'wpcoupon_no_code', '&nbsp;' );
		}

		return $code;
	}

	/**
	 * Get Print coupon image
	 *
	 * @since 1.0.0
	 * @param string $size
	 * @return mixed
	 */
	function get_print_image( $size = 'full' ) {
		if ( intval( $this->_wpc_coupon_type_printable_id ) > 0 ) {
			$img = wp_get_attachment_image_src( $this->_wpc_coupon_type_printable_id, $size );
			if ( $img ) {
				return $img[0];
			} else {
				return $this->_wpc_coupon_type_printable;
			}
		} else {
			return $this->_wpc_coupon_type_printable;
		}
	}

	/**
	 * @return false|string
	 * @since 1.0.7
	 */
	function get_print_image_file() {
		if ( intval( $this->_wpc_coupon_type_printable_id ) > 0 ) {
			return get_attached_file( $this->_wpc_coupon_type_printable_id, $size );
		} else {
			return $this->_wpc_coupon_type_printable;
		}
	}

	/**
	 * Get store id
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_store_id() {
		return $this->_wpc_store;
	}

	/**
	 * Get store url
	 *
	 * @since 1.0.0
	 * @return bool|string
	 */
	public function get_store_url() {
		if ( $this->store->is_store() ) {
			return $this->store->get_url();
		}
		return '';
	}

	/**
	 *
	 * @return null|string
	 */
	function get_share_url() {
		return $this->get_href();
	}

	public function get_href() {
		global $wp_rewrite;
		$enable_single = wpcoupon_get_option( 'enable_single_coupon', false );
		if ( ! $enable_single ) {
			if ( ! $wp_rewrite->using_permalinks() ) {
				return add_query_arg( array( 'coupon_id' => $this->ID ), $this->get_store_url() );
			} else {
				return trailingslashit( trailingslashit( $this->get_store_url() ) . $this->ID );
			}
		} else {
			if ( $wp_rewrite->using_permalinks() ) {
				return trailingslashit( trailingslashit( $this->get_store_url() ) . $this->post->post_name );
			} else {
				return get_permalink( $this->post );
			}
		}

	}

	/**
	 * Get store website url
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_store_site_url() {
		return (string) $this->store->get_website_url();
	}

	/**
	 * Get coupon type add display text
	 *
	 * @since 1.0.0
	 * @return string|void
	 */
	public function get_coupon_type_text() {
		$type = $this->get_type();
		$supported_types = wpcoupon_get_coupon_types();

		if ( isset( $supported_types[ $type ] ) ) {
			return $supported_types[ $type ];
		} else {
			return esc_html__( 'Code', 'wp-coupon' );
		}
	}

	/**
	 * Get destination url
	 *
	 * @since 1.0.0
	 * @param bool $store_url_if_empty
	 * @return bool|string
	 */
	public function get_destination_url( $store_url_if_empty = true ) {

		$url = '';
		if ( ! $this->_wpc_destination_url && $store_url_if_empty ) {
			$url = $this->get_store_site_url();
		} else {
			$url = $this->_wpc_destination_url;
		}

		if ( ! $url ) {
			$url = $this->store->get_url();
		}

		return $url;
	}

	/**
	 * Display out url
	 *
	 * example: http://site.com/out/{coupon_id}
	 *
	 * @return string
	 */
	public function get_go_out_url() {
		$home = trailingslashit( home_url() );
		// If permalink enable
		if ( get_option( 'permalink_structure' ) != '' ) {
			$out_slug = wpcoupon_get_option( 'go_out_slug', 'out' );
			return $home . $out_slug . '/' . $this->ID;
		} else {
			return $home . '?out=' . $this->ID;
		}
	}


	/**
	 * Get Go Out store url
	 *
	 * @example: http://site.com/go-store/{coupon_id}
	 *
	 * @see: Class WPCoupon_Store
	 *
	 * @return string
	 */
	function get_go_store_url() {

		$home = trailingslashit( home_url() );
		// If permalink enable
		if ( get_option( 'permalink_structure' ) != '' ) {
			$go_store_slug = wpcoupon_get_option( 'go_out_slug', 'go-store' );
			return $home . $go_store_slug . '/' . $this->get_store_id();
		} else {
			return $home . '?go_store_id=' . $this->get_store_id();
		}
	}

	/**
	 * check if coupon has thumbnail
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_thumb() {
		if ( $this->_thumb_id > 0 ) {
			return true;
		}
		if ( has_post_thumbnail( $this->ID ) ) {
			$this->_thumb_id = get_post_thumbnail_id( $this->ID );
		} elseif ( has_post_thumbnail( $this->get_store_id() ) ) {
			$this->_thumb_id = get_post_thumbnail_id( $this->get_store_id() );
		} else {
			$this->_thumb_id = $this->store->has_thumbnail();
		}

		return $this->_thumb_id ? true : false;
	}

	/**
	 * Get Thumbnail HTML
	 *
	 * @since 1.0.0
	 * @param string $size
	 * @param bool   $placeholder
	 * @return bool|string
	 */
	public function get_thumb( $size = 'wpcoupon_medium-thumb', $placeholder = false, $url_only = false ) {
		$image_alt = get_post_meta( $this->_thumb_id, '_wp_attachment_image_alt', true );
		// check if have thumbnail
		if ( $this->has_thumb( $size ) && is_numeric( $this->_thumb_id ) ) {
			if ( $url_only ) {
				$image = wp_get_attachment_image_src( $this->_thumb_id, $size );

				if ( $image ) {
					return $image[0];
				} else {
					return false;
				}
			} else {
				$image_title = get_the_title( $this->_thumb_id );
				return wp_get_attachment_image(
					$this->_thumb_id,
					$size,
					false,
					array(
						'alt' => $image_alt,
						'title' => $image_title,
					)
				);
			}
		} elseif ( $this->has_thumb() && $this->_thumb_id != '' ) {
			if ( $url_only ) {
				return $this->_thumb_id;
			} else {
				return '<img src="' . esc_attr( $this->_thumb_id ) . '" alt="' . $image_alt . '">';
			}
		} else {
			return $this->store->get_thumbnail( $size, $url_only );
		}
	}

	/**
	 * Get store Thumbnail
	 *
	 * @param string $size
	 * @return bool|mixed|void
	 */
	public function get_store_thumb( $size = 'full' ) {
		return $this->store->get_thumbnail( $size );
	}

	/**
	 *  Get number used of coupon
	 *
	 * @since 1.0.0
	 * @changed 1.1.3
	 * @return int
	 */
	public function get_total_used() {
		return apply_filters( 'wpcoupon_coupon_total_used', intval( $this->_wpc_used ), $this->ID, $this );
	}

	/**
	 * Get used today
	 *
	 * @return int
	 * @changed 1.1.3
	 */
	public function get_used_today() {
		$data = wp_parse_args(
			$this->_wpc_today,
			array(
				'date' => current_time( 'Y-m-d' ),
				'used' => 0,
			)
		);

		if ( $data['date'] == current_time( 'Y-m-d' ) ) {
			$n = $data['used'];
		} else {
			$n = 0;
		}

		return apply_filters( 'wpcoupon_coupon_used_today', $n, $this->ID, $this );
	}

	/**
	 * Get percent success
	 *
	 * @since 1.0.0
	 * @return float|int
	 */
	public function percent_success() {
		$vote_up = intval( $this->_wpc_vote_up );
		$vote_down = intval( $this->_wpc_vote_down );

		$total = $vote_up + $vote_down;
		// Request minimum 3 vote to calc
		if ( $total <= apply_filters( 'st_minimum_votes', 3 ) ) {
			return 100;
		}
		$this->percent_success = 0;
		if ( $total <= 0 ) {
			$this->percent_success = 100;
		} else {
			$this->percent_success = ( $vote_up / $total ) * 100;
		}

		return $this->percent_success;
	}

	/**
	 *
	 * @param string $type  ids|array  ids return ids only, array: return array key is term_id  value is cat nams
	 * @return array
	 */
	function get_categories( $type = 'array' ) {
		if ( ! $this->is_coupon() ) {
			return false;
		}
		$terms = get_the_terms( $this->ID, 'coupon_category' );
		$cats = false;
		if ( $terms && ! is_wp_error( $terms ) ) {
			$cats = array();
			foreach ( $terms as $term ) {
				if ( $type == 'ids' ) {
					$cats[ $term->term_id ] = $term->term_id;
				} else {
					$cats[ $term->term_id ] = $term->name;
				}
			}
		}
		return $cats;
	}

}


/**
 * Short coupon by property
 *
 * @since 1.0.0
 *
 * @param $coupons array of object coupon
 * @param string                         $orderby property of WPCoupon_Coupon
 * @param string                         $sort
 * @return mixed
 */
function wpcoupon_sort_coupons( $coupons, $orderby = '_wpc_expires', $sort = 'desc' ) {
	$sortArray = array();
	foreach ( $coupons as $coupon ) {
		if ( ! isset( $sortArray[ $orderby ] ) ) {
			$sortArray[ $orderby ] = array();
		}
		if ( isset( $coupon->$orderby ) ) {
			$sortArray[ $orderby ][] = $coupon->$orderby;
		} else {
			$sortArray[ $orderby ][] = 0;
		}
	}
	if ( strtolower( $sort ) == 'desc' ) {
		array_multisort( $sortArray[ $orderby ], SORT_DESC, $coupons );
	} else {
		array_multisort( $sortArray[ $orderby ], SORT_ASC, $coupons );
	}
	return $coupons;
}



/**
 * Get coupon expires
 *
 * @since 1.0.0
 * @param null $coupon_id
 * @return bool|string
 */
function wpcoupon_get_coupon_expires( $coupon_id = null ) {
	if ( ! $coupon_id ) {
		global  $post;
		$coupon_id = $post->ID;
	}
	$date_format = get_option( 'date_format' );
	$time = get_post_meta( $coupon_id, '_wpc_expires', true );
	if ( $time ) {
		return date_i18n( $date_format, $time );
	}
	return false;
}

/**
 * Get coupon type
 *
 * @param null $coupon_id
 * @return mixed
 */
function wpcoupon_get_coupon_type( $coupon_id = null ) {
	if ( ! $coupon_id ) {
		global  $post;
		$coupon_id = $post->ID;
	}

	$type = get_post_meta( $coupon_id, '_wpc_coupon_type', true );
	return $type;
}

/**
 * Get coupon type as text
 *
 * @param $coupon_id
 * @return string|void
 */
function wpcoupon_get_coupon_type_text( $coupon_id = null ) {
	$type = wpcoupon_get_coupon_type( $coupon_id );
	$supported_types = wpcoupon_get_coupon_types();
	if ( ! isset( $supported_types[ $type ] ) ) {
		return $supported_types[ $type ];
	} else {
		return esc_html__( 'Code', 'wp-coupon' );
	}
}

/**
 * Get coupon code
 *
 * @param null $coupon_id
 * @return string
 */
function wpcoupon_get_coupon_code( $coupon_id = null ) {
	if ( ! $coupon_id ) {
		global  $post;
		$coupon_id = $post->ID;
	}

	return (string) get_post_meta( $coupon_id, '_wpc_coupon_type_code', true );
}

/**
 * Get coupon store ID
 *
 * @param null $coupon_id
 * @return int
 */
function wpcoupon_get_coupon_store_id( $coupon_id = null ) {
	if ( ! $coupon_id ) {
		global  $post;
		$coupon_id = $post->ID;
	}
	return (int) get_post_meta( $coupon_id, '_wpc_store', true );
}

/**
 * Get store website url
 *
 * @param null $coupon_id
 * @return mixed
 */
function wpcoupon_get_coupon_store_site_url( $coupon_id = null ) {
	if ( ! $coupon_id ) {
		global  $post;
		$coupon_id = $post->ID;
	}

	return get_post_meta( $coupon_id, '_wpc_store_url', true );
}


/**
 * A call back listing comments
 *
 * @see wp_list_comments()
 *
 * @param $comment
 * @param $args
 * @param $depth
 */
function wpcoupon_coupon_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	global $post;
	switch ( $comment->comment_type ) :
		case 'pingback':
		case 'trackback':
			// Display trackbacks differently than normal comments.
			?>
			<div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'wp-coupon' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'wp-coupon' ), '<span class="edit-link">', '</span>' ); ?></p>
			<?php
			break;
		default:
			// Proceed with normal comments.
			?>
		<div <?php comment_class(); ?> data-c-id="<?php comment_ID(); ?>">

			<a class="avatar">
				<?php echo get_avatar( $comment, 60 ); ?>
			</a>

			<div class="content">
				<a class="author" href="<?php echo get_comment_author_url(); ?>"><?php echo get_comment_author(); ?></a>
				<div class="metadata">
					<span class="date"><?php printf( esc_html__( ' %s ago', 'wp-coupon' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
				</div>
				<div class="text">
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'wp-coupon' ); ?></p>
					<?php endif; ?>
					<?php comment_text(); ?>
				</div>
			</div>

			<?php
			break;
	endswitch; // end comment_type check
}

/**
 * A call  en back listing comments
 *
 * @see wp_list_comments()
 *
 * @param $comment
 * @param $args
 * @param $depth
 */
function wpcoupon_coupon_comment_end() {
	echo '</div>';
}

/**
 * Custom walker coupon class
 *
 * Class WPCoupon_Walker_Coupon_Comment
 */
class WPCoupon_Walker_Coupon_Comment extends Walker_Comment {

	/**
	 * Start the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of comment.
	 * @param array  $args Uses 'style' argument for type of HTML list.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				$output .= '<div class="children comments">' . "\n";
				break;
			case 'ol':
				$output .= '<ol class="children">' . "\n";
				break;
			case 'ul':
			default:
				$output .= '<ul class="children">' . "\n";
				break;
		}
	}

	/**
	 * End the list of items after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of comment.
	 * @param array  $args   Will only append content if style argument value is 'ol' or 'ul'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				$output .= "</div><!-- .children -->\n";
				break;
			case 'ol':
				$output .= "</ol><!-- .children -->\n";
				break;
			case 'ul':
			default:
				$output .= "</ul><!-- .children -->\n";
				break;
		}
	}

}


/**
 * New comment for coupon
 *
 * Handle submit comment form ajax
 *
 * @since 1.0.0
 *
 * @return bool|int
 */
function wpcoupon_new_coupon_comments() {
	if ( ! isset( $_POST['c_comment'] ) ) {
		return 0;
	}
	$data = $_POST['c_comment'];
	// if is user logged in then use that user data
	if ( is_user_logged_in() ) {
		$data['user_id'] = get_current_user_id();
		$user = wp_get_current_user();
		$data['comment_author'] = $user->display_name;
		$data['comment_author_email'] = $user->user_email;
	}
	$data['comment_author_url'] = '';
	return wp_new_comment( $data );

}

/**
 * Class WPCoupon_Coupon_Tracking
 *
 * /*
 * Coupon meta keys for tracking:
 * '_wpc_used'                      => 0,
 * '_wpc_views'                     => 0,
 * '_wpc_today'                     => '' ,
 * '_wpc_vote_up'                   => 0,
 * '_wpc_vote_down'                 => 0,
 */
class WPCoupon_Coupon_Tracking {

	/**
	 * Tracking coupon used
	 *
	 * @since 1.0.0
	 * @param $coupon_id ID of coupon
	 * @return bool
	 */
	public static function update_used( $coupon_id ) {

		$coupon = new WPCoupon_Coupon( $coupon_id );
		if ( ! $coupon->is_coupon() ) {
			return false;
		}

		/**
		 * Update total used
		 */
		$used = $coupon->get_total_used() + 1;
		update_post_meta( $coupon->ID, '_wpc_used', $used );

		/**
		 * Update used to day
		 */
		$to_day = current_time( 'Y-m-d' );
		$used_today = $coupon->get_used_today();
		update_post_meta(
			$coupon->ID,
			'_wpc_today',
			array(
				'date' => $to_day,
				'used' => ( $used_today + 1 ),
			)
		);

	}

	/**
	 * Vote coupon
	 *
	 * @since 1.0.0
	 * @param $coupon_id ID of coupon
	 * @param int                    $vote -1 for vote down else for vote up
	 * @return bool
	 */
	public static function vote( $coupon_id, $vote = 1 ) {

		$coupon = new WPCoupon_Coupon( $coupon_id );
		if ( ! $coupon->is_coupon() ) {
			return false;
		}

		if ( $vote != -1 ) {
			$vote = 1;
		}

		if ( $vote == 1 ) {
			update_post_meta( $coupon->ID, '_wpc_vote_up', $coupon->_wpc_vote_up + 1 );
		} else {
			update_post_meta( $coupon->ID, '_wpc_vote_down', $coupon->_wpc_vote_down + 1 );
		}
		// re-update success percent
		$vote_up = intval( $coupon->_wpc_vote_up );
		$vote_down = intval( $coupon->_wpc_vote_down );

		$total = $vote_up + $vote_down;
		// Request minimum 3 vote to calc
		if ( $total <= apply_filters( 'st_minimum_votes', 3 ) ) {
			$coupon->percent_success = 100;
		} else {
			$coupon->percent_success = 0;
			if ( $total <= 0 ) {
				$coupon->percent_success = 100;
			} else {
				$coupon->percent_success = ( $vote_up / $total ) * 100;
			}
		}

		update_post_meta( $coupon->ID, '_wpc_percent_success', $coupon->percent_success );

	}


}



/**
 * Get Ajax coupon comments
 *
 * @since 1.0.0
 * @return string
 */
function wpcoupon_ajax_get_coupon_comments() {
	$coupon_id = $_REQUEST['coupon_id'];
	global $post;
	$post = get_post( $coupon_id );
	setup_postdata( $post );
	ob_start();
	get_template_part( 'coupon-comments' );
	$content = ob_get_clean();
	return $content;
}

function wpcoupon_get_share_email_template( $type = 'code' ) {
	switch ( $type ) {
		default:
			$message = array();
			$message[] = '<h3>{coupon_title}</h3>';
			$message[] = '<h3>' . esc_html__( 'Coupon Description:', 'wp-coupon' ) . '</h3>';
			$message[] = '<div>{coupon_description}</div>';
			$message[] = '<h3>' . esc_html__( 'Store:', 'wp-coupon' ) . ' {store_name}</h3>';

			switch ( $type ) {
				case 'sale':
					$message[] = '<h3>' . esc_html__( 'Click the link bellow to get deal', 'wp-coupon' ) . '</h3>';
					$message[] = '<a href="{coupon_destination_url}">' . esc_html__( 'Get deal now', 'wp-coupon' ) . '</a>';
					break;
				case 'print':
					$message[] = '<h3>' . esc_html__( 'Print this coupon and redeem it in-store', 'wp-coupon' ) . '</h3>';
					$message[] = '{coupon_print_image}<br/>';
					$message[] = '{coupon_print_image_url}';
					break;
				default:
					$message[] = '<h3>' . esc_html__( 'Copy coupon code and use at checkout:', 'wp-coupon' ) . '</h3>';
					$message[] = '<h3>{coupon_code}</h3>';
			}
			$message[] = '<h3>' . esc_html__( 'Store website:', 'wp-coupon' ) . '</h3>';
			$message[] = '{store_go_out_url}';
			$message[] = '<hr/>' . sprintf( esc_html__( 'This email was sent form %1$s', 'wp-coupon' ), '<a href="{home_url}">{home_url}</a>' );

	}

	return join( "\n", $message );
}

/**
 * Send coupon to friend email.
 *
 * @since 1.0.0
 * @param $email
 * @param $coupon_id
 * @return bool
 */
function wpcoupon_send_coupon_to_email( $email, $coupon_id ) {
	global $post;
	$post = get_post( $coupon_id );
	if ( ! $post ) {
		return false;
	}

	wpcoupon_setup_coupon( $post );

	// DOING
	$message = apply_filters( 'wpcoupon_send_coupon_to_email_mail_message', false, $email, $coupon_id );

	$image_print_url = '';
	$type = wpcoupon_coupon()->get_type();
	$attachment_print = '';
	switch ( $type ) {
		case 'sale':
			break;
		case 'print':
			$image_print_url = wpcoupon_coupon()->get_print_image();
			$attachment_print = wpcoupon_coupon()->get_print_image_file();
			break;
		default:
	}

	$args = array(
		'coupon_title' => get_the_title(),
		'coupon_description' => get_the_content(),
		'coupon_destination_url' => wpcoupon_coupon()->get_destination_url(),
		'coupon_print_image' => ( $image_print_url ) ? '<img src="' . esc_attr( $image_print_url ) . '" alt="' . esc_html__( 'Print coupon', 'wp-coupon' ) . '"/>' : '',
		'coupon_print_image_url' => $image_print_url,
		'coupon_code' => wpcoupon_coupon()->get_code(),
		'store_name' => wpcoupon_coupon()->store->get_display_name(),
		'store_image' => wpcoupon_coupon()->store->get_thumbnail(),
		'store_go_out_url' => wpcoupon_coupon()->get_go_out_url(),
		'store_url' => wpcoupon_coupon()->store->get_home_url(),
		'store_aff_url' => wpcoupon_coupon()->store->get_website_url(),
		'home_url' => home_url( '/' ),
		'share_email' => $email,
	);

	if ( ! $message ) {
		$message = wpcoupon_get_option( 'email_share_coupon_' . $type );
	}

	$mail_title = wpcoupon_get_option( 'email_share_coupon_title' );
	if ( ! trim( $mail_title ) ) {
		$mail_title = '{coupon_title}';
	}

	if ( $message || $mail_title ) {
		foreach ( $args as $k => $v ) {
			$message = str_replace( '{' . $k . '}', $v, $message );
			$mail_title = str_replace( '{' . $k . '}', $v, $mail_title );
		}
	}

	if ( empty( $message ) ) {

		$message = array();
		$message[] = '<h3>' . get_the_title() . '</h3>';
		$message[] = '<h3>' . esc_html__( 'Coupon Description:', 'wp-coupon' ) . '</h3>';
		$message[] = '<div>' . get_the_content() . '</div>';
		$message[] = '<h3>' . esc_html__( 'Store:', 'wp-coupon' ) . ' ' . wpcoupon_coupon()->store->get_display_name() . '</h3>';

		switch ( $type ) {
			case 'sale':
				$message[] = '<h3>' . esc_html__( 'Click the link bellow to get deal', 'wp-coupon' ) . '</h3>';
				$message[] = '<a href="' . esc_attr( wpcoupon_coupon()->get_destination_url() ) . '">' . esc_html__( 'Get deal now', 'wp-coupon' ) . '</a>';
				break;
			case 'print':
				$message[] = '<h3>' . esc_html__( 'Print this coupon and redeem it in-store', 'wp-coupon' ) . '</h3>';
				$image_url = wpcoupon_coupon()->get_print_image();
				$message[] = '<img src="' . esc_attr( $image_url ) . '" alt="Print coupon"/>';
				$message[] = $image_url;
				break;
			default:
				$message[] = '<h3>' . esc_html__( 'Copy coupon code and use at checkout:', 'wp-coupon' ) . '</h3>';
				$message[] = '<h3>' . wpcoupon_coupon()->get_code() . '</h3>';
		}

		$message[] = '<h3>' . esc_html__( 'Store website:', 'wp-coupon' ) . '</h3>';
		$message[] = wpcoupon_coupon()->get_go_out_url();
		// footer email
		$message[] = '<hr/>' . sprintf( esc_html__( 'This email was sent form %1$s', 'wp-coupon' ), '<a href="' . esc_url( home_url( '' ) ) . '">' . home_url( '' ) . '</a>' );
	}

	if ( is_array( $message ) ) {
		$message = join( ' ', $message );
	}

	$attachments = apply_filters( 'wpcoupon_send_coupon_to_email_mail_message', array(), $email, $coupon_id );
	if ( $attachment_print ) {
		$attachments[] = $attachment_print;
	}

	add_filter( 'wp_mail_content_type', 'wpcoupon_set_html_content_type' );
	$r = wp_mail( $email, $mail_title, $message, '', $attachments );
	wp_reset_postdata();
	// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
	remove_filter( 'wp_mail_content_type', 'wpcoupon_set_html_content_type' );
	do_action( 'after_sent_coupon_to_email', $r, $email, $coupon_id );
	return $r;
}



/**
 *
 * Get ajax coupons
 *
 * @return array
 */
function wpcoupon_ajax_coupons( $doing = '' ) {

	$paged = isset( $_REQUEST['next_page'] ) ? intval( $_REQUEST['next_page'] ) : 1;
	$max_pages = 0;
	global $wp_rewrite, $post;

	$instance = isset( $_REQUEST['args'] ) ? $_REQUEST['args'] : false;
	$instance = wp_parse_args(
		$instance,
		array(
			'layout'         => '',
			'posts_per_page' => '',
			'num_words'      => '',
			'hide_expired'    => '',
		)
	);

	$get_args = null;
	if ( $instance['posts_per_page'] > 0 ) {
		$get_args['posts_per_page'] = $instance['posts_per_page'];
	} else {
		$get_args['posts_per_page'] = apply_filters( 'st_coupons_number', get_option( 'posts_per_page' ) );
	}

	$tpl_name = null;
	if ( ! $instance['layout'] || $instance['layout'] == 'less' ) {
		$tpl_name = 'cat';
	}

	// set excerpt length
	if ( isset( $instance['num_words'] ) ) {
		$GLOBALS['coupon_num_words'] = $instance['num_words'];
	}

	$coupons = array();

	switch ( $doing ) {

		case 'load_popular_coupons':
			$coupons  = wpcoupon_get_popular_coupons( $get_args['posts_per_page'], $paged );
			break;
		case 'load_ending_soon_coupons':
			$coupons  = wpcoupon_get_ending_soon_coupons( apply_filters( 'wpcoupon_ending_soon_coupons_day_left', 3 ), $get_args['posts_per_page'], $paged );
			break;
		default:
			$cat_id = isset( $_REQUEST['cat_id'] ) ? intval( $_REQUEST['cat_id'] ) : false;
			$taxonomy = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : 'coupon_category';
			if ( $cat_id ) {
				$get_args['tax_query'] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => array( $cat_id ),
						'operator' => 'IN',
					),
				);
				$tpl_name = wpcoupon_get_option( 'coupon_cate_tpl', 'cat' );
			}

			if ( $instance['hide_expired'] ) {
				$get_args['hide_expired'] = $instance['hide_expired'];
			}

			$coupons = wpcoupon_get_coupons( $get_args, $paged, $max_pages );
	}

	ob_start();
	$next_page = 0;
	if ( $coupons ) {

		$current_link = isset( $_REQUEST['current_link'] ) ? $_REQUEST['current_link'] : '#';

		if ( $max_pages >= ( $paged + 1 ) ) {
			$next_page = ( $paged + 1 );
		} else {
			$next_page = 0;
		}

		if ( $wp_rewrite->using_permalinks() ) {
			$current_link = preg_replace( '/page\/([0-9]+)/', 'page/' . $paged, $current_link );
		} else {
			$current_link = preg_replace( '/paged=([0-9]+)/', 'paged=' . $paged, $current_link );
		}

		foreach ( $coupons as $post ) {
			wpcoupon_setup_coupon( $post, $current_link );
			get_template_part( 'loop/loop-coupon', $tpl_name );
		}
	}

	$content = ob_get_clean();

	return array(
		'content'   => $content,
		'next_page' => $next_page,
		'max_pages' => $max_pages,
		// 'coupons' => $coupons
	);
}

function wpcoupon_ajax_store_coupons() {
	$instance = isset( $_REQUEST['args'] ) ? $_REQUEST['args'] : false;
	$default_base_pagenum_url = trailingslashit( get_bloginfo( 'url' ) );
	$instance = wp_parse_args(
		$instance,
		array(
			'store_id' => '',
			'type' => '',
			'number' => 15,
			'coupon_type' => 'all',
			'base_pagenum_url' => $default_base_pagenum_url,
		)
	);

	$paged = isset( $_REQUEST['next_page'] ) ? intval( $_REQUEST['next_page'] ) : 1;
	global $post, $wp_query;

	$content = '';
	$next_page = 0;
	$max_pages = 0;

	$store = get_term( $instance['store_id'], 'coupon_store' );
	if ( $store && ! is_wp_error( $store ) ) {
		ob_start();
		$coupons = wpcoupon_get_store_coupons( $store->term_id, absint( $instance['number'] ), $paged, $instance['type'], $instance['coupon_type'] );
		$max_pages = $wp_query->max_num_pages;
		if ( $max_pages >= ( $paged + 1 ) ) {
			$next_page = ( $paged + 1 );
		} else {
			$next_page = 0;
		}

		$current_link = get_term_link( $store, 'coupon_store' );
		$loop_tpl = wpcoupon_get_option( 'store_loop_tpl', 'full' );
		foreach ( $coupons as $post ) {
			wpcoupon_setup_coupon( $post, $current_link );
			get_template_part( 'loop/loop-coupon', $loop_tpl );
		}
		$content = ob_get_clean();
	}

	$link = get_term_link( $store );

	return array(
		'content'   => $content,
		'next_page' => $next_page,
		'pagenum_url' => wpcoupon_get_store_page_num( $paged, $instance['base_pagenum_url'] ),
		'next_pagenum_url' => wpcoupon_get_store_page_num( $next_page, $instance['base_pagenum_url'] ),
		'max_pages' => $max_pages,
	);
}


/**
 * Get popular coupons
 *
 * @param int $posts_per_page
 * @param int $paged
 * @return array
 */
function wpcoupon_get_popular_coupons( $posts_per_page = 10, $paged = 1 ) {
	// _wpc_percent_success
	// _wpc_expires
	// _wpc_vote_up
	// _wpc_used
	global $wp_query;
	$current = current_time( 'timestamp' );
	$args = array(
		'post_type'      => 'coupon',
		'posts_per_page' => $posts_per_page,
		'meta_key'       => '_wpc_used',
		'paged'          => $paged,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'relation' => 'OR',
				array(
					'key'     => '_wpc_expires',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => '_wpc_expires',
					'value'   => $current,
					'compare' => '>=',
				),
			),

		),
		'orderby'         => 'menu_order meta_value_num',
		'order'           => 'desc',
	);

	$wp_query = new WP_Query( $args );
	return $wp_query->get_posts();

}

/**
 * Search SQL filter for matching against post title only.
 *
 * @link    http://wordpress.stackexchange.com/a/11826/1685
 *
 * @param   string   $search
 * @param   WP_Query $wp_query
 */
function wpcoupon_coupon_search_by_title( $search, $wp_query ) {
	if ( is_admin() ) {
		return $search;
	}
	if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
		global $wpdb;

		$q = $wp_query->query_vars;
		$n = ! empty( $q['exact'] ) ? '' : '%';

		$search = array();

		foreach ( (array) $q['search_terms'] as $term ) {
			$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
		}

		if ( ! is_user_logged_in() ) {
			$search[] = "$wpdb->posts.post_password = ''";
		}

		$search = ' AND ' . implode( ' AND ', $search );
	}
	return $search;
}

/**
 * Change page title when single coupon not enabled.
 *
 * @param $title_array
 * @return mixed
 */
function wpcoupon_sing_coupon_modal_title_parts( $title_array ) {
	if ( ! wpcoupon_is_single_enable() ) {
		if ( is_tax( 'coupon_store' ) ) {
			$coupon_id = get_query_var( 'coupon_id' );
			if ( $coupon_id ) {
				$post = get_post( $coupon_id );
				if ( $post ) {
					$title_array['title'] = $post->post_title;
				}
			}
		}
	}
	return $title_array;
}

add_filter( 'document_title_parts', 'wpcoupon_sing_coupon_modal_title_parts' );


/**
 * Custom query for custom tax
 *
 * @todo: fix page not found on coupon page tax
 *
 * @param $query
 * @return mixed
 */
function wpcoupon_coupon_cat_query( $query ) {
	if ( get_query_var( 'taxonomy' ) == 'coupon_category' && $query->is_main_query() ) {
		$cate_id = get_queried_object_id();

		$coupon_type = 'all';
		$available_coupon_type = wpcoupon_get_coupon_types();
		$all_coupon_in_cat = wpcoupon_get_all_coupon_from_cat( $cate_id );
		$get_coupon_var = ( isset( $_GET['coupon_type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['coupon_type'] ) ) : '';
		$filtered_sortby = ( isset( $_GET['sort_by'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : 'newest';

		$number_active = intval( wpcoupon_get_option( 'coupon_cate_number', 15 ) );
		$query->set( 'posts_per_page', $number_active );

		if ( ( ! isset( $_GET['coupon_type'] ) && ! isset( $_GET['sort_by'] ) ) || ( ( '' == $get_coupon_var || 'all' == $get_coupon_var ) && 'newest' == $filtered_sortby ) ) {

			if ( isset( $all_coupon_in_cat['not_expired'] ) && isset( $all_coupon_in_cat['expired'] ) ) {
				$post_in = array_merge( $all_coupon_in_cat['not_expired'], $all_coupon_in_cat['expired'] );

				$query->set( 'post__in', $post_in );
				$query->set( 'orderby', 'post__in' );
			}
		} else {
			if ( isset( $get_coupon_var ) && array_key_exists( $get_coupon_var, $available_coupon_type ) ) {
				$coupon_type = $get_coupon_var;
			}
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			
			$query->set( 'paged', $paged );
			$now = current_time( 'timestamp' );
			$meta_query = array();

			if ( 'all' != $coupon_type ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key' => '_wpc_coupon_type',
						'value' => $coupon_type,
						'compare' => '=',
					),
				);
			}

			switch ( $filtered_sortby ) {
				case 'popularity':
					$query->set( 'meta_key', '_wpc_used' );
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'desc' );
					$query->set( 'meta_type', 'NUMERIC' );
					break;
				case 'ending-soon':
					$query->set( 'meta_key', '_wpc_expires' );
					$query->set( 'meta_value', $now );
					$query->set( 'meta_compare', '>=' );
					$query->set( 'meta_type', 'NUMERIC' );
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'asc' );
					break;
				case 'expired':
					$meta_query[] = array(
						array(
							'key' => '_wpc_expires',
							'compare' => 'EXISTS',
						),
						array(
							'key' => '_wpc_expires',
							'type' => 'NUMERIC',
							'value' => 0,
							'compare' => '>',
						),
						array(
							'key' => '_wpc_expires',
							'value' => $now,
							'type' => 'NUMERIC',
							'compare' => '<=',
						),
					);

					$query->set( 'meta_key', '_wpc_expires' );
					$query->set( 'orderby', 'meta_value' );
					$query->set( 'meta_type', 'NUMERIC' );
					$query->set( 'order', 'desc' );
					break;
				default:
					$query->set( 'orderby', 'menu_order date' );
					$query->set( 'order', 'desc' );
					break;
			}
			if ( ! empty( $meta_query ) ) {
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

	return $query;
}
add_filter( 'pre_get_posts', 'wpcoupon_coupon_cat_query' );

/**
 * Render filter box for category
 *
 * @since 1.2.6
 */
function wpcoupon_coupon_cat_filter_box() {
	$is_enable_filter = wpcoupon_get_option( 'coupon_cate_sidebar_filter', true );
	$title = wpcoupon_get_option( 'coupon_cate_filter_title', 'Filter' );
	if ( ! $is_enable_filter ) {
		return;
	}
	$filtered_sortby = ( isset( $_GET['sort_by'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : 'newest';

	?>
	<aside class="widget widget_coupon_cat_filter">
		<h4 class="widget-title"><?php echo wp_kses_post( $title ); ?></h4> 
		<div class="shadow-box">
			<?php
				$current_url = wpcoupon_current_url();
				$filter_url = add_query_arg( array( 'coupon_type' => '' ), $current_url );
				$base_pagenum = get_pagenum_link( 1 );
				global $wp_query;
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$max_pages = $wp_query->max_num_pages;
			if ( $max_pages >= ( $paged + 1 ) ) {
				$next_page = ( $paged + 1 );
			} else {
				$next_page = $paged;
			}

			?>
			<input type="hidden" class="store_base_pagenum_url" name="store_base_pagenum_url" value="<?php echo get_pagenum_link( 1, false ); ?>"/>
			<input type="hidden" class="store_pagenum_url" name="store_pagenum_url" value="<?php echo get_pagenum_link( $paged ); ?>"/>
			<input type="hidden" class="store_next_pagenum_url" name="store_next_pagenum_url" value="<?php echo get_pagenum_link( $next_page ); ?>"/>

			<div class="couponcat-sortby-wrapper ui list">
				<h5><?php echo esc_html_e( 'Sort by', 'wp-coupon' ); ?></h5>
				<div class="item">
					<label for="store-sortby-newest">
						<input id="store-sortby-newest" <?php checked( 'newest', $filtered_sortby ); ?> type="radio" class="coupon-cat-sortby sortby-newest" name="coupon-cat-sortby" value="newest" />
						<span class="filter-sortby-name"><?php echo esc_html_e( 'Newest', 'wp-coupon' ); ?></span>
					</label>
				</div>

				<div class="item">
					<label for="store-sortby-popularity">
						<input id="store-sortby-popularity" <?php checked( 'popularity', $filtered_sortby ); ?> type="radio" class="coupon-cat-sortby sortby-popularity" name="coupon-cat-sortby" value="popularity" />
						<span class="filter-sortby-name"><?php echo esc_html_e( 'Popularity', 'wp-coupon' ); ?></span>
					</label>
				</div>
				
				<div class="item">
					<label for="store-sortby-endingsoon">
						<input id="store-sortby-endingsoon" <?php checked( 'ending-soon', $filtered_sortby ); ?> type="radio" class="coupon-cat-sortby sortby-ending-soon" name="coupon-cat-sortby" value="ending-soon" />
						<span class="filter-sortby-name"><?php echo esc_html_e( 'Ending Soon', 'wp-coupon' ); ?></span>
					</label>
				</div>

				<div class="item">
					<label for="store-sortby-expired">
						<input id="store-sortby-expired" <?php checked( 'expired', $filtered_sortby ); ?> type="radio" class="coupon-cat-sortby sortby-expired" name="coupon-cat-sortby" value="expired" />
						<span class="filter-sortby-name"><?php echo esc_html_e( 'Expired', 'wp-coupon' ); ?></span>
					</label>
				</div>

			</div>
		</div>
	</aside>
	<?php
}
add_action( 'wpcoupon_coupon_category_before_sidebar', 'wpcoupon_coupon_cat_filter_box', 10 );

/**
 * Render filter box for category
 *
 * @since 1.2.6
 */
function wpcoupon_coupon_cat_filter_bar() {
	$count = wpcoupon_store()->count_coupon();
	$total = array_sum( $count );

	$types = wpcoupon_get_coupon_types( true );
	$tabs = wpcoupon_get_option( 'coupon_filter_tabs', array() );
	if ( empty( $tabs ) || ! is_array( $tabs ) || ! isset( $tabs['enabled'] ) ) {
		return;
	}

	$coupon_type = 'all';
	$available_coupon_type = wpcoupon_get_coupon_types();
	$get_coupon_var = ( isset( $_GET['coupon_type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['coupon_type'] ) ) : '';
	if ( isset( $get_coupon_var ) && array_key_exists( $get_coupon_var, $available_coupon_type ) ) {
		$coupon_type = $get_coupon_var;
	}
	$filter_coupon_count = wpcoupon_coupon_category_count_coupon();

	$filtered_sortby = ( isset( $_GET['sort_by'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : '';
	$filtered_cat = ( isset( $_GET['coupon_cat'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET['coupon_cat'] ) ) : array();
	$cate_id = get_queried_object_id();
	?>
	<section class="coupon-filter" id="couponcat-filter-bar" >
		<div data-target="#coupon-listings-store" class="filter-coupons-by-type pointing filter-coupons-buttons">
			<div class="coupons-types-wrap">
				<div class="hide couponlist-smallscreen-info">
					<div class="ui floating dropdown labeled inline icon button tiny">
						<i class="sliders horizontal icon"></i>
						<span class="text store-filter-button">
							<?php echo wp_kses_post( $tabs['enabled'][ $coupon_type ] ); ?>
							<span class="offer-count <?php echo esc_attr( $coupon_type ); ?>-count">
								<?php
								if ( isset( $filter_coupon_count ) && ! empty( $filter_coupon_count ) ) {
									echo absint( $filter_coupon_count[ $coupon_type ] );
								} else {
									if ( $coupon_type == 'all' ) {
										echo $total;
									} else {
										echo absint( $count[ $coupon_type ] );
									}
								}
								?>
							</span>
						</span>
						<div class="menu">
							<?php
							foreach ( $tabs['enabled'] as $k => $text ) {
								if ( isset( $types[ $k ] ) || $k == 'all' ) {
									if ( $k == $coupon_type ) {
										$current_url = wpcoupon_current_url();
										$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
									} else {
										$current_url = get_term_link( $cate_id );
										$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
									}
									if ( '' != $filtered_sortby ) {
										$filter_url = add_query_arg( array( 'sort_by' => $filtered_sortby ), $filter_url );
									}

									if ( ! empty( $filtered_cat ) ) {
										$filter_url = add_query_arg( array( 'coupon_cat' => $filtered_cat ), $filter_url );
									}
									?>
										<a href="<?php echo esc_url( $filter_url ); ?>" class="store-filter-button item filter-nav" data-filter="<?php echo esc_attr( $k ); ?>"><?php echo wp_kses_post( $text ); ?> <span
													class="offer-count <?php echo esc_attr( $k ); ?>-count"><?php
													if ( isset( $filter_coupon_count ) && ! empty( $filter_coupon_count ) ) {
														echo absint( $filter_coupon_count[ $k ] );
													} else {
														if ( $k == 'all' ) {
															echo $total;
														} else {
															echo absint( $count[ $k ] );
														}
													}
													?></span></a>
								<?php }
							}
							?>
						</div>
					</div>	
				</div>	
				<div class="coupon-types-list">
					<?php
					foreach ( $tabs['enabled'] as $k => $text ) {
						if ( isset( $types[ $k ] ) || $k == 'all' ) {
							if ( $k == $coupon_type ) {
								$current_url = wpcoupon_current_url();
								$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
							} else {
								$current_url = get_term_link( $cate_id );
								$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
							}
							if ( '' != $filtered_sortby ) {
								$filter_url = add_query_arg( array( 'sort_by' => $filtered_sortby ), $filter_url );
							}

							if ( ! empty( $filtered_cat ) ) {
								$filter_url = add_query_arg( array( 'coupon_cat' => $filtered_cat ), $filter_url );
							}
							?>
							<a href="<?php echo esc_url( $filter_url ); ?>" class="store-filter-button ui button tiny filter-nav <?php echo $k == $coupon_type ? 'current' : ''; ?>" data-filter="<?php echo esc_attr( $k ); ?>"><?php echo wp_kses_post( $text ); ?> <span
										class="offer-count <?php echo esc_attr( $k ); ?>-count"><?php
										if ( isset( $filter_coupon_count ) && ! empty( $filter_coupon_count ) ) {
											echo absint( $filter_coupon_count[ $k ] );
										} else {
											if ( $k == 'all' ) {
												echo $total;
											} else {
												echo absint( $count[ $k ] );
											}
										}
										?></span></a>
					<?php }
					}
					?>
				</div>
			</div>
		</div>
	</section>
	<?php
}
add_action( 'wpcoupon_coupon_category_before_render_coupons', 'wpcoupon_coupon_cat_filter_bar', 10 );

/**
 * Count number of coupons in coupon category
 *
 * @since 1.2.6
 */
function wpcoupon_coupon_category_count_coupon() {
	$cate_id = get_queried_object_id();
	$now = current_time( 'timestamp' );
	$meta_query = array();
	$query = array(
		'post_type' => 'coupon',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'coupon_category',
				'field'    => 'term_id',
				'terms'    => array( $cate_id ),
				'operator' => 'IN',
			),
		),
	);

	$filtered_sortby = ( isset( $_GET['sort_by'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : '';

	if ( '' != $filtered_sortby || ! empty( $filtered_cat ) ) {
		if ( 'ending-soon' == $filtered_sortby ) {
			$query['meta_key'] = '_wpc_expires';
			$query['meta_value'] = $now;
			$query['meta_compare'] = '>=';
			$query['meta_type'] = 'NUMERIC';
			$query['orderby'] = 'meta_value_num';
			$query['order'] = 'asc';
		} elseif ( 'expired' == $filtered_sortby ) {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key' => '_wpc_expires',
					'compare' => 'EXISTS',
				),
				array(
					'key' => '_wpc_expires',
					'type' => 'NUMERIC',
					'value' => 0,
					'compare' => '>',
				),
				array(
					'key' => '_wpc_expires',
					'value' => $now,
					'type' => 'NUMERIC',
					'compare' => '<=',
				),
			);

			$query['meta_key'] = '_wpc_expires';
			$query['orderby'] = 'meta_value';
			$query['meta_type'] = 'NUMERIC';
			$query['order']  = 'desc';
		}
	}

	$available_coupon_type = wpcoupon_get_coupon_types();
	$return = array();

	foreach ( $available_coupon_type as $k => $v ) {
		$meta_query['query_by_type'] = array(
			'relation' => 'AND',
			array(
				'key' => '_wpc_coupon_type',
				'value' => $k,
				'compare' => '=',
			),
		);
		$query['meta_query'] = $meta_query;
		$count_type = new WP_Query( $query );
		$return[ $k ] = $count_type->found_posts;
		wp_reset_postdata();
	}
	$count_all = 0;
	if ( ! empty( $return ) ) {
		foreach ( $return as $value ) {
			$count_all += absint( $value );
		}
		$return['all'] = $count_all;
	}
	return $return;
}

/**
 * Get list expired coupon in coupon category
 *
 * @since 1.2.6
 */
function wpcoupon_get_all_coupon_from_cat( $cate_id ) {
	$now = current_time( 'timestamp' );
	$query = array(
		'post_type' => 'coupon',
		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'coupon_category',
				'field'    => 'term_id',
				'terms'    => array( $cate_id ),
				'operator' => 'IN',
			),
		),
	);
	$return = $all_ids = $expired_ids = array();
	$coupons = new WP_Query( $query );
	if ( $coupons->have_posts() ) {
		while ( $coupons->have_posts() ) {
			$coupons->the_post();
			$all_ids[] = get_the_ID();
		}
	}
	wp_reset_postdata();

	$query_2 = $query;
	$query_2['meta_query'] = array(
		'relation' => 'AND',
		array(
			'key' => '_wpc_expires',
			'compare' => 'EXISTS',
		),
		array(
			'key' => '_wpc_expires',
			'type' => 'NUMERIC',
			'value' => 0,
			'compare' => '>',
		),
		array(
			'key' => '_wpc_expires',
			'value' => $now,
			'type' => 'NUMERIC',
			'compare' => '<=',
		),
	);

	$query_2['orderby'] = 'ID';
	$query_2['order'] = 'desc';

	$coupons_all = new WP_Query( $query_2 );
	if ( $coupons_all->have_posts() ) {
		while ( $coupons_all->have_posts() ) {
			$coupons_all->the_post();
			$expired_ids[] = get_the_ID();
		}
	}
	wp_reset_postdata();
	$return = array(
		'all' => $all_ids,
		'expired' => $expired_ids,
		'not_expired' => array_diff( $all_ids, $expired_ids ),
	);
	return $return;
}
