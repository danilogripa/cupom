<?php
/**
 * Hook actions
 */
add_action( 'wpcoupon_before_container', 'wpcoupon_breadcrumb', 15 );
add_action( 'wpcoupon_store_content', 'wpcoupon_store_share', 15 );
add_action( 'wpcoupon_cat_content', 'wpcoupon_store_share', 15 );
add_action( 'wpcoupon_before_coupon_listings', 'wpcoupon_store_coupons_filter', 15 );
