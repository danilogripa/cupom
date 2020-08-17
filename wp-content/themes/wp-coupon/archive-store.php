<?php
/**
 * Template Name: Store Listing by Alphabet
 *
 * Display Store by alphabet
 *
 * @package ST-Coupon
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

?>
    <div id="content-wrap" class="container no-sidebar">

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php

                if ( ! is_search() ) {

                $stores = get_posts( array(
                    'posts_per_page'   => -1,
                    'orderby'          => 'title',
                    'order'            => 'ASC',
                    'post_type'        => 'store',
                    'post_status'      => 'publish',
                    'suppress_filters' => true
                ));

                $_stores = array();
                $_stores_number = array();
                // Group stores by alphabet
                foreach (  $stores as $k => $store ) {
                    $first_char = $store->post_title{0};
                    $first_char = strtoupper( $first_char );
                    if( is_numeric( $first_char ) ) {
                        $_stores_number[] = $store;
                    } else {
                        if ( ! isset( $_stores[ $first_char ] ) ) {
                            $_stores[ $first_char ]  =  array();
                        }
                        $_stores[ $first_char ][] = $store;
                    }
                }

                ?>
                <div class="content-box shadow-box">

                    <section class="browse-store stackable ui grid">
                        <div class="four wide column store-listing-left">
                            <div class="ui fluid vertical menu">
                                <?php
                                 foreach (  $_stores as $k => $list_stores ) {
                                     echo '<a class="item" href="#character-'.esc_attr( $k ).'"><div class="ui mini label">'.count( $list_stores ).'</div>'.esc_html( $k ).'</a>';
                                 }
                                if (  count( $_stores_number ) ) {
                                ?>
                                <a class="item href="#character-0-9"><div class="ui mini label"><?php echo count( $_stores_number ); ?></div><?php esc_html_e( '0 - 9', 'wp-coupon' ); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="twelve wide column">
                            <div class="store-listing">

                                <?php

                                // get featured stores
                                $featured_stores =  wpcoupon_get_featured_stores( );

                                if ( count( $featured_stores ) ) { ?>
                                <div class="store-listing-box store-popular-listing">
                                    <div class="store-letter-heading">
                                        <h2 class="section-heading"><?php esc_html_e( 'Featured Stores', 'wp-coupon' ); ?></h2>
                                    </div>
                                    <div class="store-letter-content">
                                        <div class="ui four column grid">
                                            <?php
                                            foreach ( $featured_stores as $store ) {
                                                wpcoupon_setup_store( $store );
                                            ?>
                                            <div class="column">
                                                <div class="store-thumb">
                                                    <a href="<?php echo get_permalink( $store ); ?>" class="ui image middle aligned">
                                                       <?php echo wpcoupon_store()->get_thumbnail(); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <?php foreach (  $_stores as $k => $list_stores ) { ?>
                                <div id="character-<?php echo esc_attr( $k ); ?>" class="store-a store-listing-box">
                                    <div class="store-letter-heading">
                                        <h2 class="section-heading"><?php printf( esc_html__( 'Stores - %s', 'wp-coupon' ), $k ); ?></h2>
                                    </div>
                                    <div class="store-letter-content">
                                        <ul class="clearfix">
                                            <?php foreach ( $list_stores as $store ) { ?>
                                            <li><a href="<?php echo get_permalink( $store ); ?>"><?php echo esc_html( $store->post_title ); ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php  } ?>

                                <?php if ( count( $_stores_number ) ) { ?>
                                    <div id="character-0-9" class="store-a store-listing-box">
                                        <div class="store-letter-heading">
                                            <h2 class="section-heading"><?php printf( esc_html__( 'Stores - 0-9', 'wp-coupon' ), $k ); ?></h2>
                                        </div>
                                        <div class="store-letter-content">
                                            <ul class="clearfix">
                                                <?php foreach ( $_stores_number as $store ) { ?>
                                                    <li><a href="<?php echo get_page_link( $store ); ?>"><?php echo esc_html( $store->post_title ); ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php  } ?>

                            </div>
                        </div>
                    </section>

                </div>

                <?php
                } else {

                   if ( have_posts() ){
                       get_template_part( 'search-store' );
                       get_template_part( 'content', 'paging' );
                   } else {
                       get_template_part( 'content', 'none' );
                   }

                }
                ?>
            </main><!-- #main -->
        </div><!-- #primary -->

        <?php

        wp_reset_postdata();

        ?>

    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>