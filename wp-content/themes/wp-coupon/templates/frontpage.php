<?php
/**
 * Template Name: Front Page
 *
 * @package WP-Coupon
 * @since 1.0.
 */


get_header();

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();
if ( ! is_active_sidebar( 'frontpage-sidebar' ) ) {
    $layout = 'no-sidebar';
}

?>
<div id="content-wrap" class="frontpage-container container <?php echo esc_attr( $layout ); ?>">

    <?php
    if ( is_active_sidebar( 'frontpage-before-main' ) ){
        echo '<div class="content-widgets frontpage-before-main">';
        dynamic_sidebar( 'frontpage-before-main' );
        echo '</div>';
    }

    if ( is_active_sidebar( 'frontpage-main' ) ) {
        ?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main content-widgets" role="main">
                <?php
                dynamic_sidebar('frontpage-main');
                ?>
            </main>
            <!-- #main -->
        </div><!-- #primary -->
        <?php
    }

    if ( $layout != 'no-sidebar' && is_active_sidebar( 'frontpage-sidebar' ) ) {
        ?>
        <div id="secondary" class="widget-area sidebar" role="complementary">
            <?php
            dynamic_sidebar( 'frontpage-sidebar' );
            ?>
        </div>
        <div class="clear"></div>
        <?php
    }
    ?>

    <?php
    if ( is_active_sidebar( 'frontpage-after-main' ) ){
        echo '<div class="content-widgets frontpage-after-main">';
        dynamic_sidebar( 'frontpage-after-main' );
        echo '</div>';
    }
    ?>
</div> <!-- /#content-wrap -->


<?php get_footer(); ?>
