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

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();

?>
<div id="content-wrap" class="container container-index <?php echo esc_attr( $layout ); ?>">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
			woocommerce_content();
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php

	if ( $layout != 'no-sidebar' ) {
	?>

	<div id="secondary" class="widget-area sidebar" role="complementary">
	    <?php
	    dynamic_sidebar( 'sidebar-woo' );
	    ?>
	</div>

	<?php
	}

	?>
</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
