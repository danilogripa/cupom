<?php
/**
 * The page template file.
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

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();
?>
    <div id="content-wrap" class="container container-page <?php echo esc_attr( $layout ); ?>">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php
                get_template_part('content');

                wpcoupon_wp_link_pages( );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </main><!-- #main -->
        </div><!-- #primary -->

        <?php

        if ( $layout != 'no-sidebar' ) {
            echo '<div id="secondary" class="widget-area sidebar" role="complementary">';
            dynamic_sidebar('sidebar-2');
            echo "</div>";
        }

        ?>
    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>
