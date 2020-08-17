<?php
get_header();
$cate_title = single_cat_title( '', false );
$cate_id =  get_queried_object_id();

$layout = wpcoupon_get_option( 'coupon_cate_layout', 'right-sidebar' );
?>

<div id="content-wrap" class="container <?php echo esc_attr( $layout ); ?>">

    <div id="primary" class="content-area">
        <main id="main" class="site-main ajax-coupons" role="main">

            <section id="store-listings-wrapper" class="st-list-coupons wpb_content_element">
				<?php the_archive_title( '<h2 class="section-heading">', '</h2>' ); ?>
				<?php
				global $wp_rewrite;
				$paged =  wpcoupon_get_paged();
				$args = array(
					'tax_query' => array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'coupon_tag',
							'field'    => 'term_id',
							'terms'    => array( $cate_id ),
							'operator' => 'IN',
						),
					),
					//'meta_value' => '',
					//'orderby' => 'meta_value_num',
				);

				$coupons =  wpcoupon_get_coupons( $args, $paged , $max_pages );
				$current_link = $_SERVER['REQUEST_URI'];

				$tpl = wpcoupon_get_option( 'coupon_cate_tpl', 'cat' );

				if (  $coupons )  {
					foreach ( $coupons as $post ) {
						wpcoupon_setup_coupon( $post , $current_link );
						get_template_part( 'loop/loop-coupon', $tpl );
					}
				}

				?>
            </section>
			<?php
			if ( 'ajax_loadmore' ==  wpcoupon_get_option( 'coupon_cate_paging', 'ajax_loadmore' ) ) {
				if ($max_pages > $paged) { ?>
                    <div class="load-more wpb_content_element">
                        <a href="<?php next_posts($max_pages); ?>" class="ui button btn btn_primary btn_large" data-next-page="<?php echo($paged + 1); ?>"
                           data-link="<?php echo esc_attr($current_link); ?>" data-cat-id="<?php echo esc_attr( $cate_id ); ?>"
                           data-type="coupon_tag"
                           data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i class="arrow circle outline down icon"></i></a>
                    </div>
				<?php }
			} else {
				get_template_part( 'content', 'paging' );
			}
			?>
        </main><!-- #main -->
    </div><!-- #primary -->

    <div id="secondary" class="widget-area sidebar" role="complementary">
		<?php
		dynamic_sidebar( 'sidebar-coupon-category' );
		?>
    </div>

	<?php
	$ads = wpcoupon_get_option( 'coupon_cate_ads', '' );
	if ( $ads ) {
		echo '<div class="clear"></div>';
		echo balanceTags( $ads );
	}
	?>

</div> <!-- /#content-wrap -->

<?php
get_footer();
?>
