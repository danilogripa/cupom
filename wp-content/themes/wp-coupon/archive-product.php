<?php
/**
* @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
* @package 	WooCommerce/Templates
* @version     3.4.0
*/

get_header( 'shop' );

/**
* Hooks wpcoupon_after_header
* @see woocommerce_content
* @see wpcoupon_page_header();
*
*/
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();
?>
<div id="content-wrap" class="container wc-archive-container <?php echo esc_attr( $layout ); ?>">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            /**
             * woocommerce_before_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action( 'woocommerce_before_main_content' );

            /**
             * woocommerce_archive_description hook.
             *
             * @hooked woocommerce_taxonomy_archive_description - 10
             * @hooked woocommerce_product_archive_description - 10
             */
            do_action( 'woocommerce_archive_description' );


            if ( woocommerce_product_loop() ) {

	            /**
	             * Hook: woocommerce_before_shop_loop.
	             *
	             * @hooked wc_print_notices - 10
	             * @hooked woocommerce_result_count - 20
	             * @hooked woocommerce_catalog_ordering - 30
	             */
	            do_action( 'woocommerce_before_shop_loop' );

	            woocommerce_product_loop_start();

	            if ( wc_get_loop_prop( 'total' ) ) {
		            while ( have_posts() ) {
			            the_post();

			            /**
			             * Hook: woocommerce_shop_loop.
			             *
			             * @hooked WC_Structured_Data::generate_product_data() - 10
			             */
			            do_action( 'woocommerce_shop_loop' );

			            wc_get_template_part( 'content', 'product' );
		            }
	            }

	            woocommerce_product_loop_end();

	            /**
	             * Hook: woocommerce_after_shop_loop.
	             *
	             * @hooked woocommerce_pagination - 10
	             */
	            do_action( 'woocommerce_after_shop_loop' );
            } else {
	            /**
	             * Hook: woocommerce_no_products_found.
	             *
	             * @hooked wc_no_products_found - 10
	             */
	            do_action( 'woocommerce_no_products_found' );
            }


            /**
             * woocommerce_after_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action( 'woocommerce_after_main_content' );
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->

    <?php

    if ( $layout != 'no-sidebar' ) {
        ?>
        <div id="secondary" class="widget-area sidebar" role="complementary">
            <?php dynamic_sidebar( 'sidebar-woo' ); ?>
        </div><!-- #secondary -->
        <?php
    }

    ?>
</div> <!-- /#content-wrap -->

<?php get_footer( 'shop' ); ?>
