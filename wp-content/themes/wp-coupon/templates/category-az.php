<?php
/**
 * Template Name: Coupon Categories Listing
 *
 * Display Coupon Categories
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
                    'type'                     => 'post',
                    'child_of'                 => 0,
                    'orderby'                  => 'name',
                    'order'                    => 'ASC',
                    'hide_empty'               => 0,
                    'hierarchical'             => 1,
                    'taxonomy'                 => 'coupon_category',
                    'pad_counts'               => false
                );

                $categories = get_categories( $args );
                $_categories = array();
                foreach ( $categories as $k => $c ) {
                    if ( $c->parent == 0 ) {
                        $_categories[ $c->term_id ] = array();
                        $_categories[ $c->term_id ]['data'] = $c;
                        unset( $categories[ $k ] );
                        if ( ! empty ( $categories ) ) {
                            foreach ( $categories as $ck => $cc) {
                                if ( $cc->parent == $c->term_id ) {
                                    if (!isset($_categories[ $c->term_id ]['child'])) {
                                        $_categories[ $c->term_id ]['child'] = array();
                                    }
                                    $_categories[ $c->term_id ]['child'][] = $cc;
                                    unset( $categories[$ck] );
                                }
                            }
                        }
                    }
                }

                ?>
                <div class="content-box shadow-box">
                    <div class="three column stackable ui grid cate-az">
                        <?php
                        foreach ( $_categories as $cat_id => $c ) {
                        ?>
                            <ul class="cate-item column">
                                <li class="cate-parent">
                                    <a href="<?php echo get_term_link( $c['data'], 'coupon_category' ); ?>" class="category-name category-parent">
                                        <?php

                                        $image_id = get_term_meta( $cat_id, '_wpc_cat_image_id', true );
                                        $thumb = false;
                                        if ( $image_id > 0 ) {
                                            $image= wp_get_attachment_image_src( $image_id, 'medium' );
                                            if ( $image ) {
                                                $thumb = '<span class="cat-az-thumb"><img src="'.esc_attr( $image[0] ).'" alt=" "></span>';
                                            }
                                        }

                                        if ( ! $thumb ) {
                                            $icon = get_term_meta( $cat_id, '_wpc_icon', true );
                                            if ( trim( $icon ) !== '' ){
                                                $thumb = '<i class="circular '.esc_attr( $icon ).'"></i>';
                                            }
                                        }

                                         echo $thumb;

                                        ?>
                                        <?php echo esc_html( $c['data']->name ); ?>
                                    </a>
                                    <?php if ( isset( $c['child'] ) ) { ?>
                                    <ul class="cate-child">
                                        <?php foreach ( $c['child'] as $cc ) { ?>
                                        <li><a href="<?php echo get_term_link( $cc, 'coupon_category' ); ?>"><?php echo esc_html( $cc->name ); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                                </li>
                            </ul>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <?php } else { ?>
                    <div class="ui warning message">
                        <div class="header">
                            <?php esc_html_e( 'Oops! No categories found', 'wp-coupon' ); ?>
                        </div>
                        <p><?php esc_html_e( 'You must activate wpcoupons plugin to use this template.', 'wp-coupon' ); ?></p>
                    </div>
                <?php } ?>


            </main><!-- #main -->
        </div><!-- #primary -->
        
    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>
