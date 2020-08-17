<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP Coupon
 */
get_header();

$term = get_queried_object();
wpcoupon_setup_store( $term );
$current_link = get_permalink( $term );
$store_name = wpcoupon_store()->name;
$layout = wpcoupon_get_option( 'store_layout', 'left-sidebar' );

?>
<section class="custom-page-header single-store-header">
	<div class="container">
		<?php
		/**
		 * Hooked
		 *
		 * @see wpcoupon_breadcrumb() - 15
		 *
		 * @since 1.0.0
		 */
		do_action( 'wpcoupon_before_container' );
		?>
		<div class="inner shadow-box">
			<div class="inner-content clearfix">
				<div class="header-thumb">
					<div class="header-store-thumb">
						<a rel="nofollow" target="_blank" title="<?php esc_html_e( 'Shop ', 'wp-coupon' );
						echo wpcoupon_store()->get_display_name(); ?>" href="<?php echo wpcoupon_store()->get_go_store_url(); ?>">
							<?php
							echo wpcoupon_store()->get_thumbnail();
							?>
						</a>
					</div>
					<a class="add-favorite" data-id="<?php echo wpcoupon_store()->term_id; ?>" href="#"><i class="empty heart icon"></i><span><?php esc_html_e( 'Favorite This Store', 'wp-coupon' ); ?></span></a>
				</div>
				<div class="header-content">

					<h1><?php echo wpcoupon_store()->get_single_store_name(); ?></h1>
					<?php
					wpcoupon_store()->get_content( true, true );
					?>
					<?php
					/**
					 * Hooked
					 *
					 * @see wpcoupon_store_share() - 15
					 *
					 * @since 1.0.0
					 */
					do_action( 'wpcoupon_store_content' );
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<div id="content-wrap" class="container <?php echo esc_attr( $layout ); ?>">

	<div id="primary" class="content-area">
		<main id="main" class="site-main coupon-store-main" role="main">
			<?php
			/**
			 * Hooked
			 *
			 * @see: wpcoupon_store_coupons_filter -  15
			 * @see wpcoupon_store_coupons_filter
			 * @since 1.0.0
			 */
			do_action( 'wpcoupon_before_coupon_listings' );

			global $wp_query;
			$coupons = $wp_query->posts;
			$coupon_max_pages = $wp_query->max_num_pages;
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			if ( $coupon_max_pages >= ( $paged + 1 ) ) {
				$nextpage = ( $paged + 1 );
			} else {
				$nextpage = $paged;
			}

			$number_active = intval( wpcoupon_get_option( 'store_number_active', 15 ) );
			 /**
			 * get coupons of this store
			 */
			$term_id = get_queried_object_id();
			$loop_tpl = wpcoupon_get_option( 'store_loop_tpl', 'full' );

			if ( have_posts() ) {
				?>
				<section id="coupon-listings-store" class=" wpb_content_element">
					<div class="ajax-coupons">
						<div class="store-listings st-list-coupons couponstore-tpl-<?php echo esc_attr( $loop_tpl ); ?>">
							<?php
							while ( have_posts() ) {
								the_post();
								wpcoupon_setup_coupon( get_post( get_the_ID() ) );
								get_template_part( 'loop/loop-coupon', $loop_tpl );
							}
							?>
						</div>
						<!-- END .store-listings -->
						<?php
						$coupon_type = 'all';
						$available_coupon_type = wpcoupon_get_coupon_types();

						$get_coupon_var = ( isset( $_GET['coupon_type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['coupon_type'] ) ) : '';
						if ( isset( $get_coupon_var ) && array_key_exists( $get_coupon_var, $available_coupon_type ) ) {
							$coupon_type = $get_coupon_var;
						}
						if ( $coupon_max_pages > 1 && $paged < $coupon_max_pages ) { ?>
							<div class="store-load-more wpb_content_element">
								<a href="<?php echo get_pagenum_link( $nextpage ); ?>" class="ui button btn btn_primary btn_large"
								   data-loading-text="<?php esc_attr_e( 'Loading...', 'wp-coupon' ); ?>"><?php esc_html_e( 'Load More Coupons', 'wp-coupon' ); ?> <i class="arrow alternate circle down outline icon"></i></a>
							</div>
						<?php }

						?>
					</div><!-- /.ajax-coupons -->
				</section>
				<?php
			} else { // No coupon found
				?>
				<div id="coupon-listings-store">
					<div class="ajax-coupons">
						<div class="ui warning message">
							<i class="close icon"></i>
							<div class="header">
								<?php esc_html_e( 'Oops! No coupons found', 'wp-coupon' ); ?>
							</div>
							<p><?php esc_html_e( 'There are no coupons for this store, please come back later.', 'wp-coupon' ); ?></p>
						</div>
					</div>
				</div>
				<?php
			}
			do_action( 'st_after_coupon_listings' );
			echo wpcoupon_store()->get_extra_info();

			wp_reset_postdata();
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php get_sidebar( 'store' ); ?>

</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
