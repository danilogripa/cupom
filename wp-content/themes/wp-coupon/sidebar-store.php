<div id="secondary" class="widget-area sidebar" role="complementary">
	<?php 
		/**
		 * Hook: wpcoupon_coupon_store_before_sidebar
		 * @since 1.2.6
		 * hooked wpcoupon_store_cat_filter - 10
		 */
		do_action( 'wpcoupon_coupon_store_before_sidebar' );

	?>
    <?php dynamic_sidebar( 'sidebar-store' ); ?>
	<?php 
		/**
		 * Hook: wpcoupon_coupon_store_after_sidebar
		 * @since 1.2.6
		 */
		do_action( 'wpcoupon_coupon_store_after_sidebar' );

		
	?>
</div><!-- #secondary -->