<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WP Coupon
 */

/**
 * Display navigation to next/previous set of posts when applicable.
 */
function wpcoupon_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links(
		array(
			'wp-coupon'     => $pagenum_link,
			'format'    => $format,
			'total'     => $GLOBALS['wp_query']->max_num_pages,
			'current'   => $paged,
			'mid_size'  => 1,
			'add_args'  => array_map( 'urlencode', $query_args ),
			'prev_text' => esc_html__( '&larr; Previous', 'wp-coupon' ),
			'next_text' => esc_html__( 'Next &rarr;', 'wp-coupon' ),
		)
	);

	if ( $links ) :

		?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'wp-coupon' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo apply_filters( 'wpcoupon_paging_navigation_links', $links ); ?>
		</div><!--/ .pagination -->
	</nav><!--/ .navigation -->
		<?php
	endif;
}



if ( ! function_exists( 'wpcoupon_get_archive_title' ) ) :
	/**
	 * Shim for `the_archive_title()`.
	 *
	 * Display the archive title based on the queried object.
	 *
	 * @todo Remove this function when WordPress 4.3 is released.
	 *
	 * @param string $before Optional. Content to prepend to the title. Default empty.
	 * @param string $after  Optional. Content to append to the title. Default empty.
	 */
	function wpcoupon_get_archive_title( $before = '', $after = '' ) {
		if ( is_category() ) {
			$title = sprintf( esc_html__( 'Category: %s', 'wp-coupon' ), single_cat_title( '', false ) );
		} elseif ( is_tag() ) {
			$title = sprintf( esc_html__( 'Tag: %s', 'wp-coupon' ), single_tag_title( '', false ) );
		} elseif ( is_author() ) {
			$title = sprintf( esc_html__( 'Author: %s', 'wp-coupon' ), '<span class="vcard">' . get_the_author() . '</span>' );
		} elseif ( is_year() ) {
			$title = sprintf( esc_html__( 'Year: %s', 'wp-coupon' ), get_the_date( _x( 'Y', 'yearly archives date format', 'wp-coupon' ) ) );
		} elseif ( is_month() ) {
			$title = sprintf( esc_html__( 'Month: %s', 'wp-coupon' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'wp-coupon' ) ) );
		} elseif ( is_day() ) {
			$title = sprintf( esc_html__( 'Day: %s', 'wp-coupon' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'wp-coupon' ) ) );
		} elseif ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'wp-coupon' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'wp-coupon' );
		} elseif ( is_post_type_archive() ) {
			$title = sprintf( esc_html__( 'Archives: %s', 'wp-coupon' ), post_type_archive_title( '', false ) );
		} elseif ( is_tax() ) {
			$tax = get_taxonomy( get_queried_object()->taxonomy );
			/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
			$title = sprintf( esc_html__( '%1$s: %2$s', 'wp-coupon' ), $tax->labels->singular_name, single_term_title( '', false ) );
		} else {
			$title = esc_html__( 'Archives', 'wp-coupon' );
		}

		/**
		 * Filter the archive title.
		 *
		 * @param string $title Archive title to be displayed.
		 */
		$title = apply_filters( 'get_the_archive_title', $title );

		if ( ! empty( $title ) ) {
			return $before . $title . $after;
		}
		return false;
	}
endif;


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function wpcoupon_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'wpcoupon_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,

				// We only need to know if there is more than one category.
				'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'wpcoupon_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so wpcoupon_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so wpcoupon_categorized_blog should return false.
		return false;
	}
}





if ( ! function_exists( 'wpcoupon_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own wp-coupon_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @return void
	 */
	function wpcoupon_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		global $post;

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
		} else {
			$tag = 'li';
		}
		switch ( $comment->comment_type ) {
			case 'pingback':
			case 'trackback':
				// Display trackbacks differently than normal comments.
				?>
				<<?php echo esc_attr( $tag ); ?> <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', 'wp-coupon' ); ?><?php comment_author_link(); ?><?php edit_comment_link( esc_html__( '(Edit)', 'wp-coupon' ), '<span class="edit-link">', '</span>' ); ?></p>
				<?php
				break;
			default:
				// Proceed with normal comments.
				?>
				<<?php echo esc_attr( $tag ); ?> <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

				<a class="avatar">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</a>

				<div class="content shadow-box">
					<a class="author" href="<?php echo get_comment_author_url(); ?>"><?php echo get_comment_author(); ?></a>

					<div class="metadata">
						<span class="date"><?php printf( esc_html__( ' %s ago', 'wp-coupon' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
					</div>
					<div class="c-text text">
						<?php if ( '0' == $comment->comment_approved ) : ?>
							<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'wp-coupon' ); ?></p>
						<?php endif; ?>
						<?php comment_text(); ?>
					</div>
					<div class="actions">
						<?php
						$onclick = sprintf(
							'return addComment.moveForm( "%1$s-%2$s", "%2$s", "%3$s", "%4$s" )',
							'comment',
							$comment->comment_ID,
							'respond',
							$post->ID
						);
						?>
						<a class="reply" onclick='<?php echo esc_js( $onclick ); ?>'><?php esc_html_e( 'Reply', 'wp-coupon' ); ?></a>
					</div>
				</div>

				<?php
				break;
		}; // end comment_type check
	}
};
/**
 * A call  en back listing comments
 *
 * @see wp_list_comments()
 *
 * @param $comment
 * @param $args
 * @param $depth
 */
function wpcoupon_comment_end() {
	echo '</div>';
}

/**
 * Output html5 js file for ie9.
 */
function wpcoupon_theme_html5() {
	echo '<!--[if lt IE 9]>';
	echo '<script src="' . esc_url( get_template_directory_uri() ) . '/assets/js/libs/html5.min.js"></script>';
	echo '<![endif]-->';
}
add_action( 'wp_head', 'wpcoupon_theme_html5' );


/**
 * Display breadcrumb
 *
 * @since 1.0.0
 */
function wpcoupon_breadcrumb( $return = false ) {
	$html = '';
	if ( function_exists( 'bcn_display' ) ) {
		$html .= bcn_display( true );
		if ( $html != '' ) {
			$html = '<div class="ui breadcrumb breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . $html . ' </div>';
		}
	}
	if ( ! $return ) {
		echo $html;
	} else {
		return $html;
	}

}


/**
 * Display like and share buttons
 *
 * @since 1.0.0
 */
function wpcoupon_store_share() {
	if ( wpcoupon_get_option( 'store_socialshare' ) ) {
		?>
		<div class="entry-share">
			<div class="skin skin_flat">
				<div class="social-likes social-likes_single">
					<div class="facebook" title="<?php esc_html_e( 'Share link on Facebook', 'wp-coupon' ); ?>"><?php esc_html_e( 'Facebook', 'wp-coupon' ); ?></div>
					<div class="twitter" title="<?php esc_html_e( 'Share link on Twitter', 'wp-coupon' ); ?>"><?php esc_html_e( 'Twitter', 'wp-coupon' ); ?></div>
					<div class="plusone" title="<?php esc_html_e( 'Share link on Google+', 'wp-coupon' ); ?>"><?php esc_html_e( 'Google+', 'wp-coupon' ); ?></div>
					<div class="pinterest" title="<?php esc_html_e( 'Share image on Pinterest', 'wp-coupon' ); ?>" data-media=""><?php esc_html_e( 'Pinterest', 'wp-coupon' ); ?></div>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Return current url
 *
 * @since 1.2.6
 */
function wpcoupon_current_url( $remove_page_arg = false ) {
	global $wp;
	$request = $wp->request;
	if ( $remove_page_arg ) {
		$request = remove_query_arg( 'paged', $request );
	}
	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	return trailingslashit( $current_url );
}


/**
 * Display coupon filter
 *
 * @since 1.0.0
 * @since 1.2.6 Improve filter links
 */
function wpcoupon_store_coupons_filter() {
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
	$filter_coupon_count = wpcoupon_get_filter_coupon_count();
	$filtered_sortby = ( isset( $_GET['sort_by'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : '';
	$filtered_cat = ( isset( $_GET['coupon_cat'] ) ) ? sanitize_text_field( wp_unslash( $_GET['coupon_cat'] ) ) : '';
	
	?>
	<section class="coupon-filter" id="coupon-filter-bar">
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
										$current_url = get_term_link( wpcoupon_store() );
										$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
									}
									if( '' != $filtered_sortby ) {
										$filter_url = add_query_arg( array( 'sort_by' => $filtered_sortby ), $filter_url );
									}
		
									if( '' != $filtered_cat ) {
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
								$current_url = get_term_link( wpcoupon_store() );
								$filter_url = add_query_arg( array( 'coupon_type' => $k ), $current_url );
							}
							if( '' != $filtered_sortby ){
								$filter_url = add_query_arg( array( 'sort_by' => $filtered_sortby ), $filter_url );
							}

							if( '' != $filtered_cat ){
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
			<?php
			if ( class_exists( 'WPCoupon_Front_Coupon_Edit' ) ) {
				?>
					<a href="#" class="tiny button ui btn_primary submit-coupon-button"><?php esc_html_e( 'Submit a coupon', 'wp-coupon' ); ?></a>
				<?php
			}

			?>
		</div>
	</section>
	<?php
}

/**
 * Submit coupon block in coupon store
 */
function wpcoupon_store_submit_coupon() {
	if ( class_exists( 'WPCoupon_Front_Coupon_Edit' ) ) {
		?>
		<section id="store-submit-coupon" class="hide shadow-box store-listing-item ">
			<?php echo do_shortcode( '[wpcoupon_submit]' ); ?>
		</section>
		<?php
	}
}
add_action( 'wpcoupon_before_coupon_listings', 'wpcoupon_store_submit_coupon', 20 );
/**
 * Display page header
 */
function wpcoupon_page_header() {
	// Default settings
	$default = array(
		'_wpc_show_header' => wpcoupon_get_option( 'page_header', 'on' ),
		'_wpc_custom_title' => '',
		'_wpc_show_breadcrumb' => ( wpcoupon_get_option( 'page_header_breadcrumb', true ) ? 'on' : 'off' ),
		'_wpc_show_cover' => ( wpcoupon_get_option( 'page_header_cover', true ) ? 'on' : 'off' ),
		'_wpc_cover_image' => '',
		'_wpc_cover_image_id' => wpcoupon_get_option( 'page_header_cover_img', 0, 'id' ),
		'_wpc_cover_color' => '',
	);

	$custom_meta = array();
	$is_shop = false;
	if ( wpcoupon_is_wc() ) {
		$is_shop = is_woocommerce();
	}

	if ( is_page() || is_home() || $is_shop ) {

		if ( $is_shop ) {
			$post_id = wc_get_page_id( 'shop' );
			$title = woocommerce_page_title( false );
		} else {
			$post_id = get_the_ID();
			$title = '';
			if ( is_home() ) {
				if ( get_option( 'page_for_posts' ) > 0 ) {
					$post_id = get_option( 'page_for_posts' );
					$title = get_the_title( $post_id );
				}
			} else {
				$title = get_the_title();
			}
		}

		foreach ( $default as $key => $v ) {
			$custom_meta[ $key ] = get_post_meta( $post_id, $key, true );
		}

		if ( get_post_meta( $post_id, '_wpc_hide_breadcrumb', true ) == 'on' ) {
			$custom_meta['_wpc_show_breadcrumb'] = 'off';
		}

		if ( get_post_meta( $post_id, '_wpc_hide_cover', true ) == 'on' ) {
			$custom_meta['_wpc_show_cover'] = 'off';
		}

		$custom_meta['_wpc_custom_title'] = $title;
		if ( ! isset( $custom_meta['_wpc_show_cover'] ) ) {
			$custom_meta['_wpc_show_cover'] = 'off';
		}
		if ( ! $custom_meta['_wpc_cover_image_id'] ) {
			$custom_meta['_wpc_cover_image_id'] = $default['_wpc_cover_image_id'];
		}
		if ( ! $custom_meta['_wpc_cover_image'] ) {
			$custom_meta['_wpc_cover_image'] = $default['_wpc_cover_image'];
		}

		// This should apply blog settings
		if ( is_home() ) {
			$custom_meta['_wpc_cover_image_id'] = wpcoupon_get_option( 'blog_header_cover_img', 0, 'id' );
			$custom_meta['_wpc_show_cover'] = ( wpcoupon_get_option( 'blog_header_cover', true ) ? 'on' : 'off' );
		}
	} elseif ( is_singular( 'post' ) || is_post_type_archive( 'post' ) || is_archive() ) {

		$default = array(
			'_wpc_show_header'       => wpcoupon_get_option( 'blog_header', 'on' ),
			'_wpc_custom_title'      => wpcoupon_get_option( 'blog_header_title', '' ),
			'_wpc_show_breadcrumb'   => ( wpcoupon_get_option( 'blog_header_breadcrumb', true ) ? 'on' : 'off' ),
			'_wpc_show_cover'        => ( wpcoupon_get_option( 'blog_header_cover', true ) ? 'on' : 'off' ),
			'_wpc_cover_image'       => '',
			'_wpc_cover_image_id'    => wpcoupon_get_option( 'blog_header_cover_img', 0, 'id' ),
			'_wpc_cover_color'       => '',
		);

		if ( is_archive() ) {
			$custom_meta['_wpc_custom_title'] = wpcoupon_get_archive_title();
		}
	} elseif ( is_404() ) {

		$custom_meta = $default;
		$custom_meta['_wpc_custom_title'] = esc_html__( 'Oops! That page can&rsquo;t be found.', 'wp-coupon' );
		$custom_meta['_wpc_show_breadcrumb'] = 'off';

	}
	// Search page title
	if ( is_search() ) {
		$custom_meta['_wpc_show_cover'] = 'off';
		$custom_meta['_wpc_custom_title'] = sprintf( esc_html__( 'Search Results for: %s', 'wp-coupon' ), get_search_query() );
	}

	if ( is_singular( 'coupon' ) ) {
		$custom_meta['_wpc_show_cover'] = 'off';
	}

	$custom_meta = wp_parse_args( array_filter( $custom_meta ), $default );
	// Hook to change header settings
	$custom_meta = apply_filters( 'wpcoupon_page_header_settings', $custom_meta );
	$title = $custom_meta['_wpc_custom_title'];
	$style = array();
	if ( $custom_meta['_wpc_show_header'] == 'on' ) {

		$image_url = $custom_meta['_wpc_cover_image'];
		if ( ! empty( $custom_meta['_wpc_cover_image_id'] ) ) {
			if ( is_array( $custom_meta['_wpc_cover_image_id'] ) ) {
				$image_url = wp_get_attachment_url( $custom_meta['_wpc_cover_image_id']['id'] );
			} else {
				$image_url = wp_get_attachment_url( $custom_meta['_wpc_cover_image_id'] );
			}
		}

		$class = 'page-header';
		if ( $custom_meta['_wpc_show_cover'] == 'on' && ( $image_url != '' || $custom_meta['_wpc_cover_color'] != '' ) ) {
			$class = 'page-header-cover';
			if ( $image_url ) {
				$style[] = 'background-image: url("' . $image_url . '");';
				$style[] = '
                        background-repeat: no-repeat;
                        -webkit-background-size: cover;
                        -moz-background-size: cover;
                        -o-background-size: cover;
                        background-size: cover;
                        background-position: center;
                        ';
			} else {
				$style[] = 'background-image: none;';
			}

			if ( $custom_meta['_wpc_cover_color'] != '' ) {
				$style[] = 'background-color: ' . $custom_meta['_wpc_cover_color'] . ';';
			}
		}
		$breadcrumb = '';
		if ( $custom_meta['_wpc_show_breadcrumb'] == 'on' ) {
			$breadcrumb = wpcoupon_breadcrumb( true );
			/*
			if ( $is_shop ) {
				$breadcrumb = wpcoupon_get_wc_breadcrumb();
			} else {
				$breadcrumb = wpcoupon_breadcrumb( true );
			}
			*/
		}

		if ( $title || $breadcrumb ) {
			?>
			<section class="<?php echo esc_attr( $class ); ?>" style="<?php echo esc_attr( join( ' ', $style ) ); ?>">
				<div class="container">
					<div class="inner">
						<div class="inner-content clearfix">
							<div class="header-content">
								<?php
								if ( $title ) {
									echo '<h1>' . wp_kses_post( $title ) . '</h1>';
								}
								?>
							</div>
							<?php
							echo wp_kses_post( $breadcrumb );
							?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
}
add_action( 'wpcoupon_after_header', 'wpcoupon_page_header' );


/**
 * Get sidebar layout
 *
 * @return array
 */
function wpcoupon_get_site_layout() {

	$layout  = wpcoupon_get_option( 'layout', 'right-sidebar' );

	$is_shop = false;
	if ( wpcoupon_is_wc() ) {
		$is_shop = is_woocommerce();
	}

	if ( $is_shop ) {
		$_l = get_post_meta( wc_get_page_id( 'shop' ), '_wpc_layout', true );
		$layout  = ( $_l != '' ) ? $_l : $layout;
		$layout = apply_filters( 'wpcoupon_get_site_layout_single', $layout, wc_get_page_id( 'shop' ) );
	} if ( is_singular() ) {
		global $post;
		$_l = get_post_meta( $post->ID, '_wpc_layout', true );
		$layout  = ( $_l != '' ) ? $_l : $layout;
		$layout = apply_filters( 'wpcoupon_get_site_layout_single', $layout, $post->ID );
	}
	$layout = str_replace( '_', '-', $layout );

	return apply_filters( 'wpcoupon_get_site_layout', $layout );
}



/**
 * Retrieve paginated link for archive post pages.
 *
 * Technically, the function can be used to create paginated link list for any
 * area. The 'base' argument is used to reference the url, which will be used to
 * create the paginated links. The 'format' argument is then used for replacing
 * the page number. It is however, most likely and by default, to be used on the
 * archive post pages.
 *
 * The 'type' argument controls format of the returned value. The default is
 * 'plain', which is just a string with the links separated by a newline
 * character. The other possible values are either 'array' or 'list'. The
 * 'array' value will return an array of the paginated link list to offer full
 * control of display. The 'list' value will place all of the paginated links in
 * an unordered HTML list.
 *
 * The 'total' argument is the total amount of pages and is an integer. The
 * 'current' argument is the current page number and is also an integer.
 *
 * An example of the 'base' argument is "http://example.com/all_posts.php%_%"
 * and the '%_%' is required. The '%_%' will be replaced by the contents of in
 * the 'format' argument. An example for the 'format' argument is "?page=%#%"
 * and the '%#%' is also required. The '%#%' will be replaced with the page
 * number.
 *
 * You can include the previous and next links in the list by setting the
 * 'prev_next' argument to true, which it is by default. You can set the
 * previous text, by using the 'prev_text' argument. You can set the next text
 * by setting the 'next_text' argument.
 *
 * If the 'show_all' argument is set to true, then it will show all of the pages
 * instead of a short list of the pages near the current page. By default, the
 * 'show_all' is set to false and controlled by the 'end_size' and 'mid_size'
 * arguments. The 'end_size' argument is how many numbers on either the start
 * and the end list edges, by default is 1. The 'mid_size' argument is how many
 * numbers to either side of current page, but not including current page.
 *
 * It is possible to add query vars to the link by using the 'add_args' argument
 * and see {@link add_query_arg()} for more information.
 *
 * The 'before_page_number' and 'after_page_number' arguments allow users to
 * augment the links themselves. Typically this might be to add context to the
 * numbered links so that screen reader users understand what the links are for.
 * The text strings are added before and after the page number - within the
 * anchor tag.
 *
 * @since 2.1.0
 * @see paginate_links()
 *
 * @param string|array $args {
 *     Optional. Array or string of arguments for generating paginated links for archives.
 *
 *     @type string $base               Base of the paginated url. Default empty.
 *     @type string $format             Format for the pagination structure. Default empty.
 *     @type int    $total              The total amount of pages. Default is the value WP_Query's
 *                                      `max_num_pages` or 1.
 *     @type int    $current            The current page number. Default is 'paged' query var or 1.
 *     @type bool   $show_all           Whether to show all pages. Default false.
 *     @type int    $end_size           How many numbers on either the start and the end list edges.
 *                                      Default 1.
 *     @type int    $mid_size           How many numbers to either side of the current pages. Default 2.
 *     @type bool   $prev_next          Whether to include the previous and next links in the list. Default true.
 *     @type bool   $prev_text          The previous page text. Default '« Previous'.
 *     @type bool   $next_text          The next page text. Default '« Previous'.
 *     @type string $type               Controls format of the returned value. Possible values are 'plain',
 *                                      'array' and 'list'. Default is 'plain'.
 *     @type array  $add_args           An array of query args to add. Default false.
 *     @type string $add_fragment       A string to append to each link. Default empty.
 *     @type string $before_page_number A string to appear before the page number. Default empty.
 *     @type string $after_page_number  A string to append after the page number. Default empty.
 * }
 * @return array|string String of page links or array of page links.
 */
function wpcoupon_paginate_links( $args = '' ) {
	global $wp_query, $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base' => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format' => $format, // ?page=%#% : %#% is replaced by the page number
		'total' => $total,
		'current' => $current,
		'show_all' => false,
		'prev_next' => true,
		'prev_text' => esc_html__( '&laquo; Previous', 'wp-coupon' ),
		'next_text' => esc_html__( 'Next &raquo;', 'wp-coupon' ),
		'end_size' => 1,
		'mid_size' => 2,
		'type' => 'plain',
		'add_args' => array(), // array of query args to add
		'add_fragment' => '',
		'before_page_number' => '',
		'after_page_number' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format_query = parse_url( str_replace( '%_%', $args['format'], $args['base'] ), PHP_URL_QUERY );
		wp_parse_str( $format_query, $format_arg );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		wp_parse_str( remove_query_arg( array_keys( $format_arg ), $url_parts[1] ), $query_args );
		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $query_args ) );
	}

	// Who knows what else people pass in $args
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}
	$add_args = $args['add_args'];
	$r = '';
	$page_links = array();
	$dots = false;

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		/**
		 * Filter the paginated links for the given archive pages.
		 *
		 * @since 3.0.0
		 *
		 * @param string $link The paginated link URL.
		 */
		$page_links[] = '<a class="item prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
	endif;
	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = "<div class='item page-numbers active current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . '</div>';
			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link .= $args['add_fragment'];

				/** This filter is documented in wp-includes/general-template.php */
				$page_links[] = "<a class='item page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . '</a>';
				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<div class="item page-numbers dots">' . esc_html__( '&hellip;', 'wp-coupon' ) . '</div>';
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $args['prev_next'] && $current && ( $current < $total || -1 == $total ) ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		/** This filter is documented in wp-includes/general-template.php */
		$page_links[] = '<a class="item next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
	endif;
	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		case 'list':
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= join( "</li>\n\t<li>", $page_links );
			$r .= "</li>\n</ul>\n";
			break;

		default:
			$r = join( "\n", $page_links );
			break;
	}
	return $r;
}


/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * @since 1.2.0
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string       $before           HTML or text to prepend to each link. Default is `<p> Pages:`.
 *     @type string       $after            HTML or text to append to each link. Default is `</p>`.
 *     @type string       $link_before      HTML or text to prepend to each link, inside the `<a>` tag.
 *                                          Also prepended to the current item, which is not linked. Default empty.
 *     @type string       $link_after       HTML or text to append to each Pages link inside the `<a>` tag.
 *                                          Also appended to the current item, which is not linked. Default empty.
 *     @type string       $next_or_number   Indicates whether page numbers should be used. Valid values are number
 *                                          and next. Default is 'number'.
 *     @type string       $separator        Text between pagination links. Default is ' '.
 *     @type string       $nextpagelink     Link text for the next page link, if available. Default is 'Next Page'.
 *     @type string       $previouspagelink Link text for the previous page link, if available. Default is 'Previous Page'.
 *     @type string       $pagelink         Format string for page numbers. The % in the parameter string will be
 *                                          replaced with the page number, so 'Page %' generates "Page 1", "Page 2", etc.
 *                                          Defaults to '%', just the page number.
 *     @type int|bool     $echo             Whether to echo or not. Accepts 1|true or 0|false. Default 1|true.
 * }
 * @return string Formatted output in HTML.
 */
function wpcoupon_wp_link_pages( $args = '' ) {
	global $page, $numpages, $multipage, $more;

	if ( $numpages < 2 ) {
		return false;
	}

	$defaults = array(
		'pagelink'         => '%',
		'echo'             => 1,
	);

	$params = wp_parse_args( $args, $defaults );

	/**
	 * Filter the arguments used in retrieving page links for paginated posts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params An array of arguments for page links for paginated posts.
	 */
	$r = apply_filters( 'wpcoupon_wp_link_pages_args', $params );

	$html = '<div class="ui pagination menu">';

	for ( $i = 1; $i <= $numpages; $i++ ) {

		if ( $i == $page ) {
			$link = '<span class="item page-numbers active current">' . $i . '</span>';
		} else {
			$link = str_replace( '%', $i, $r['pagelink'] );
			$link = wpcoupon_wp_link_page( $i ) . $link . '</a>';

		}

		$html .= $link;
	}

	$html .= '</div>';
	if ( $r['echo'] ) {
		echo wp_kses_post( $html );
	}
	return $html;
}


/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @param int $i Page number.
 * @return string Link.
 */
function wpcoupon_wp_link_page( $i ) {
	global $wp_rewrite;
	$post = get_post();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ) ) ) {
			$url = add_query_arg( 'page', $i, get_permalink() );
		} elseif ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
		} else {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( $i, 'single_paged' );
		}
	}

	if ( is_preview() ) {
		$url = add_query_arg(
			array(
				'preview' => 'true',
			),
			$url
		);

		if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
			$url = add_query_arg(
				array(
					'preview_id'    => wp_unslash( $_GET['preview_id'] ),
					'preview_nonce' => wp_unslash( $_GET['preview_nonce'] ),
				),
				$url
			);
		}
	}

	return '<a class="item" href="' . esc_url( $url ) . '">';
}



/**
 *
 * Display widget sidebar
 *
 * @param $sidebar
 */
function wpcoupon_sidebar( $sidebar ) {
	if ( ! $sidebar ) {
		return;
	}
	?>
	<div id="secondary" class="widget-area sidebar" role="complementary">
		<?php
		dynamic_sidebar( $sidebar );
		?>
	</div>
	<?php
}

function wpcoupon_thumb( $thumb_text = false ) {
	?>
	<div class="store-thumb <?php echo ( $thumb_text ) ? 'text-thumb' : 'thumb-img'; ?>">
		<?php if ( ! $thumb_text ) { ?>
			<?php if ( ! is_tax( 'coupon_store' ) ) { ?>
				<a class="thumb-padding" href="<?php echo esc_attr( wpcoupon_coupon()->get_store_url() ); ?>">
					<?php echo wpcoupon_coupon()->get_thumb( 'wpcoupon_medium-thumb' ); ?>
				</a>
			<?php } else { ?>
				<span class="thumb-padding" >
					<?php echo wpcoupon_coupon()->get_thumb( 'wpcoupon_medium-thumb' ); ?>
				</span>
			<?php } ?>
		<?php } else { ?>
			<span class="thumb-padding">
				<?php
				$text = get_post_meta( wpcoupon_coupon()->ID, '_wpc_coupon_save', true );
				$is_free_shipping = get_post_meta( wpcoupon_coupon()->ID, '_wpc_free_shipping', true );
				$a = explode( '|', $text );
				if ( count( $a ) > 1 ) {
					$text = $a[0];
					if ( $is_free_shipping ) {
						esc_html_e( 'Free Shipping', 'wp-coupon' );
					} elseif ( $text ) {
						echo wp_kses_post( $text );
					}
					if ( $a[1] ) {
						echo '<span>' . esc_html( $a[1] ) . '</span>';
					}
				} else {
					if ( $is_free_shipping ) {
						esc_html_e( 'Free Shipping', 'wp-coupon' );
					} elseif ( $text ) {
						echo wp_kses_post( $text );
					}
				}

				?>
			</span>
		<?php } ?>
	</div>
	<?php
}
