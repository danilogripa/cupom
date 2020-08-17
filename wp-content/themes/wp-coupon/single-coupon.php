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

global $post;
wpcoupon_setup_coupon( $post );
the_post();
?>
    <div id="content-wrap" class="single-coupon-container container <?php echo esc_attr( $layout ); ?>">

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php
                $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                $has_expired = wpcoupon_coupon()->has_expired();
                ?>
                <div data-id="<?php echo wpcoupon_coupon()->ID; ?>"
                     class="coupon-item <?php echo $has_thumb ? 'has-thumb' : 'no-thumb'; ?> store-listing-item c-type-<?php echo esc_attr( wpcoupon_coupon()->get_type() ); ?> coupon-listing-item shadow-box <?php echo ( $has_expired ) ? 'coupon-expired' : 'coupon-live'; ?>">
                    <?php if ( $has_thumb ) { ?>
                        <div class="store-thumb-link">
                            <div class="store-thumb">
                                <?php if ( ! is_tax( 'coupon_store' ) ) { ?>
                                    <a href="<?php echo esc_attr( wpcoupon_coupon()->get_store_url() ); ?>">
                                        <?php echo wpcoupon_coupon()->get_thumb('wpcoupon_medium-thumb'); ?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo wpcoupon_coupon()->get_thumb('wpcoupon_medium-thumb'); ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="latest-coupon">
                        <h1 class="coupon-title">
                            <?php
                            edit_post_link('<i class="edit icon"></i>', '', '', wpcoupon_coupon()->ID );
                            ?>
                            <a title="<?php echo esc_attr( get_the_title( wpcoupon_coupon()->ID ) ) ?>"
                               class="coupon-link"
                               data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                               data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                               data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>"
                               data-code="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>"
                               href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php echo get_the_title( wpcoupon_coupon()->ID ); ?></a>
                        </h1>
                        <div class="c-type">
                            <span class="c-code c-<?php echo esc_attr( wpcoupon_coupon()->get_type() ); ?>"><?php echo wpcoupon_coupon()->get_coupon_type_text( ); ?></span>
                            <?php if ( ! wpcoupon_coupon()->has_expired() ) { ?>
                                <span class="exp"><?php echo wpcoupon_coupon()->get_expires(); ?></span>
                            <?php } else { ?>
                                <span class="exp has-expired"><?php echo wpcoupon_coupon()->get_expires() ; ?></span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="coupon-detail coupon-button-type">
                        <?php
                        switch ( wpcoupon_coupon()->get_type() ) {

                            case 'sale':
                                ?>
                                <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>" class="coupon-deal coupon-button single-coupon-btn" href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php esc_html_e( 'Get Deal', 'wp-coupon' ); ?> <i class="shop icon"></i></a>
                                <?php
                                break;
                            case 'print':
                                ?>
                                <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>" class="coupon-print coupon-button single-coupon-btn" href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php esc_html_e( 'Print Coupon', 'wp-coupon' ); ?> <i class="print icon"></i></a>
                                <?php
                                break;
                            default:
                                ?>
                                <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                                   data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                   href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"
                                   class="coupon-button coupon-code single-coupon-btn"
                                   data-tooltip="<?php echo esc_attr_e( 'Click to copy & open site', 'wp-coupon' ); ?>"
                                   data-position="top center"
                                   data-inverted=""
                                   data-code="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>"
                                   data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>">
                                    <span class="code-text" rel="nofollow"><?php echo esc_html( wpcoupon_coupon()->get_code( 8 ) ); ?></span>
                                    <span class="get-code"><?php  esc_html_e( 'Get Code', 'wp-coupon' ); ?></span>
                                </a>
                                <?php
                        }
                        ?>
                        <div class="clear"></div>
                        <div class="user-ratting ui icon basic buttons">
                            <div class="ui button icon-popup coupon-vote" data-vote-type="up" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"  data-position="top center" data-inverted="" data-tooltip="<?php esc_attr_e( 'This worked', 'wp-coupon' ); ?>"><i class="smile outline icon"></i></div>
                            <div class="ui button icon-popup coupon-vote" data-vote-type="down" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"  data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( "It didn't work", 'wp-coupon' ); ?>"><i class="frown outline icon"></i></div>
                            <div class="ui button icon-popup coupon-save" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( "Save this coupon", 'wp-coupon' ); ?>" ><i class="empty star icon"></i></div>
                        </div>
                        <span class="voted-value"><?php printf( esc_html__( '%s%% Success', 'wp-coupon' ), round( wpcoupon_coupon()->percent_success() ) ); ?></span>
                    </div>
                    <div class="clear"></div>


                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    if ( wpcoupon_coupon()->is_exclusive() ) {
                        if ( wpcoupon_get_option('coupon_item_exclusive') != '' ) {
                            echo '<div class="coupon-exclusive">'. wpcoupon_get_option('coupon_item_exclusive') .'</div>';
                        }
                    }
                    ?>
                    <div class="coupon-footer coupon-listing-footer">
                        <ul class="clearfix">
                            <li><span><i class="wifi icon"></i> <?php printf( esc_html__( '%1$s Used - %2$s Today', 'wp-coupon' ), wpcoupon_coupon()->get_total_used(), wpcoupon_coupon()->get_used_today() ); ?></span></li>
                            <li><a title="<?php esc_attr_e( 'Share it with your friend', 'wp-coupon' ); ?>" data-reveal="reveal-share" href="#"><i class="share alternate icon"></i> <?php esc_html_e( 'Share', 'wp-coupon' ); ?></a></li>
                            <li><a title="<?php esc_attr_e( 'Send this coupon to an email', 'wp-coupon' ); ?>" data-reveal="reveal-email" href="#"><i class="mail outline icon"></i> <?php esc_html_e( 'Email', 'wp-coupon' ); ?></a></li>
                        </ul>
                        <div data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" class="reveal-content reveal-share">
                            <span class="close"></span>
                            <h4><?php esc_html_e( 'Share it with your friends', 'wp-coupon' ); ?></h4>
                            <div class="ui fluid left icon input">
                                <input value="<?php echo wpcoupon_coupon()->get_share_url() ?>" type="text">
                                <i class="linkify icon"></i>
                            </div>
                            <br>
                            <div class="coupon-share">
                                <?php
                                $args =  array(
                                    'title'     => get_the_title( wpcoupon_coupon()->ID ),
                                    'summary'   => wpcoupon_coupon()->get_excerpt(140),
                                    'url'       => wpcoupon_coupon()->get_share_url()
                                );
                                echo WPCoupon_Socials::facebook_share( $args );
                                echo WPCoupon_Socials::twitter_share( $args );

                                do_action('loop_coupon_more_share_buttons');

                                ?>
                            </div>
                        </div>
                        <div data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" class="reveal-content reveal-email">
                            <span class="close"></span>
                            <h4 class="send-mail-heading"><?php esc_html_e( 'Send this coupon to an email', 'wp-coupon' ); ?></h4>
                            <div class="ui fluid action left icon input">
                                <input class="email_send_to" placeholder="<?php esc_attr_e( 'Email address ...', 'wp-coupon' ); ?>" type="text">
                                <i class="mail outline icon"></i>
                                <div class="email_send_btn ui button btn btn_primary"><?php esc_html_e( 'Send', 'wp-coupon' ); ?></div>
                            </div><br>
                            <p><?php esc_html_e( 'This is not a email subscription service. Your email (or your friend\'s email) will only be used to send this coupon.', 'wp-coupon' ); ?></p>
                        </div>

                    </div>

                    <?php

                    get_template_part('loop/coupon-modal');

                    ?>
                </div>
                <?php

                if ( wpcoupon_get_option( 'enable_single_popular', true ) ) {
                    // in this stores
                    $number = wpcoupon_get_option( 'single_popular_number', 3 );
                    $number = absint( $number );

                    $custom_text = wpcoupon_get_option( '' );
                    if (! $custom_text ) {
                        $custom_text = esc_html__('Most popular {store} coupons.', 'wp-coupon');
                    }
                    $terms = get_the_terms($post->ID, 'coupon_store');
                    if ($terms) {
                        $current = current_time('timestamp');
                        $tag_ids = wp_list_pluck($terms, 'term_id');

                        $first_store = current( $terms );
                        $custom_text = str_replace( '{store}', $first_store->name, $custom_text );
                        $args = array(
                            'tax_query' => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'coupon_store',
                                    'field' => 'term_id',
                                    'terms' => $tag_ids,
                                    'operator' => 'IN',
                                ),
                            ),
                            /*
                            'meta_query'     => array(
                                'relation' => 'AND',
                                array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => '_wpc_expires',
                                        'value'   => '',
                                        'compare' => '=',
                                    ),
                                    array(
                                        'key'     => '_wpc_expires',
                                        'value'   => $current,
                                        'compare' => '>=',
                                    ),
                                )
                            ),
*/
                            'post__not_in' => array($post->ID),
                            'posts_per_page' => $number,
                            'meta_key' => '_wpc_used',
                            'orderby' => 'meta_value_num',
                            'order' => 'desc'
                        );

                        $args = apply_filters( 'wpcoupon_single_popular_coupons_args', $args );

                        $wp_query = new WP_Query( $args );
                        $max_pages =  $wp_query->max_num_pages;
                        $coupons = $wp_query->get_posts();

                        if ($coupons) {
                            if ( $custom_text ) {
                                ?>
                                <h2 class="section-heading coupon-status-heading"><?php echo wp_kses_post($custom_text); ?></h2>
                                <?php
                            }
                            $tpl_name = 'cat';
                            foreach ($coupons as $post) {
                                wpcoupon_setup_coupon($post);
                                get_template_part('loop/loop-coupon', $tpl_name);
                            }
                            ?>

                            <?php
                        }
                    }

                    wp_reset_query();
                }
                ?>

                <?php echo get_the_term_list( $post->ID, 'coupon_tag', '<p class="coupon-tags"><strong>'.esc_html__( 'Tags:', 'wp-coupon' ).' </strong>', ', ', '</p>' ); ?>

                <div class="single-coupon-comments shadow-box content-box">
                    <?php
                    comments_template();
                    ?>
                </div>

            </main><!-- #main -->
        </div><!-- #primary -->

        <?php

        if ( $layout != 'no-sidebar' ) {
            get_sidebar();
        }

        ?>

    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>
