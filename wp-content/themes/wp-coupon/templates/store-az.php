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
                <?php
                if ( taxonomy_exists( 'coupon_store' ) ) {
                $args = array(
                    'orderby'                => 'name',
                    'order'                  => 'ASC',
                    'hide_empty'             => false,
                    'include'                => array(),
                    'exclude'                => array(),
                    'exclude_tree'           => array(),
                    'number'                 => '',
                    'offset'                 => '',
                    'fields'                 => 'all',
                    'name'                   => '',
                    'slug'                   => '',
                    'hierarchical'           => false,
                    'search'                 => '',
                    'name__like'             => '',
                    'description__like'      => '',
                    'pad_counts'             => false,
                    'get'                    => '',
                    'child_of'               => 0,
                    'parent'                 => wpcoupon_get_option( 'stores_listing_hide_child', 0 ) ?  0 : '',
                    'childless'              => false,
                    'cache_domain'           => 'core',
                    'update_term_meta_cache' => true,
                    'meta_query'             => ''
                );

                $stores = get_terms( 'coupon_store', $args );

                $_stores = array();
                $_stores_number = array();
                // Group stores by alphabet
                foreach (  $stores as $k => $store ) {
                    $first_char = mb_substr($store->name, 0, 1);
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
                                <a class="item" href="#character-0-9"><div class="ui mini label"><?php echo count( $_stores_number ); ?></div><?php esc_html_e( '0 - 9', 'wp-coupon' ); ?></a>
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
                                        <div class="ui grid">
                                            <?php
                                            foreach ( $featured_stores as $store ) {
                                                wpcoupon_setup_store( $store );
                                            ?>
                                            <div class="eight wide mobile eight wide tablet four wide computer column">
                                                <div class="store-thumb">
                                                    <a href="<?php echo get_term_link( $store, 'coupon_store' ) ?>" class="ui image middle aligned">
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
                                            <li><a href="<?php echo get_term_link( $store, 'coupon_store' ); ?>"><?php echo esc_html( $store->name ); ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php  } ?>

                                <?php if ( count( $_stores_number ) ) { ?>
                                    <div id="character-0-9" class="store-a store-listing-box">
                                        <div class="store-letter-heading">
                                            <h2 class="section-heading"><?php esc_html_e( 'Stores - 0-9', 'wp-coupon' ); ?></h2>
                                        </div>
                                        <div class="store-letter-content">
                                            <ul class="clearfix">
                                                <?php foreach ( $_stores_number as $store ) { ?>
                                                    <li><a href="<?php echo get_term_link( $store, 'coupon_store' ); ?>"><?php echo esc_html( $store->name ); ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php  } ?>

                            </div>
                        </div>
                    </section>

                </div>
                <?php } else { ?>
                    <div class="ui warning message">
                        <div class="header">
                            <?php esc_html_e( 'Oops! No stores found', 'wp-coupon' ); ?>
                        </div>
                        <p><?php esc_html_e( 'You must activate wpcoupons plugin to use this template.', 'wp-coupon' ); ?></p>
                    </div>
                <?php } ?>

            </main><!-- #main -->
        </div><!-- #primary -->

        <?php

        wp_reset_postdata();

        ?>

    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>