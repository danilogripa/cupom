<?php
/**
 * Template Name: Coupon Submit
 *
 * Display submit coupon page
 *
 * @package WP-Coupon
 * @since 1.0.
 */


get_header();
the_post();

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );

?>
<div id="content-wrap" class="container no-sidebar">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <div class="entry-content content-box shadow-box">
                <div class="stackable ui grid">
                    <div class="eight wide column">
                        <?php
                        if (  class_exists( 'WPCoupon_Coupon_Submit_ShortCode' ) ) {
                            echo WPCoupon_Coupon_Submit_ShortCode::submit_coupon_form();
                        }
                        ?>
                    </div>
                    <div class="eight wide column">
                        <?php
                        the_content(); ?>
                    </div>
                </div>
            </div>

        </main><!-- #main -->
    </div><!-- #primary -->
</div> <!-- /#content-wrap -->

<?php
get_footer();
?>