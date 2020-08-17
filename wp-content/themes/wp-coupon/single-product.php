<?php
/**
 * The Template for displaying all single products
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'shop' );

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();
?>
<div id="content-wrap" class="container wc-single-container <?php echo esc_attr( $layout ); ?>">
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
            ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <?php wc_get_template_part( 'content', 'single-product' ); ?>

            <?php endwhile; // end of the loop. ?>

            <?php
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
