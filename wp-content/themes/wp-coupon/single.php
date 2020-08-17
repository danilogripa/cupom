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
    <div id="content-wrap" class="container <?php echo esc_attr( $layout ); ?>">

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <div <?php post_class( 'post-entry shadow-box content-box' ); ?>>
                    <?php

                    the_post();

                    ?>
                    <?php if ( has_post_thumbnail( ) ) { ?>
                        <div class="shadow-box post-thumbnail">
                            <?php the_post_thumbnail( 'wpcoupon_blog_medium' ); ?>
                        </div>

                    <?php }  ?>

                    <div class="post-meta">
                        <?php
                        global $authordata;

                        echo '<div class="author-avatar">';
                        echo get_avatar( get_the_author_meta( 'email', $authordata->ID ), 50 );
                        echo '</div>';

                        echo '<div class="post-meta-data">';

                            the_title('<h1 class="post-title">','</h1>');
                            echo '<p class="meta-line-2">';
                            if ( is_object( $authordata ) ) {
                                echo '<span class="author-name">';
                                    printf(
                                        esc_html__( 'Posts by %s' , 'wp-coupon' ),
                                        '<a href="'.esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ).'" title="'.esc_attr( sprintf( esc_html__( 'Posts by %s', 'wp-coupon' ), get_the_author() ) ).'" rel="author">'. get_the_author().'</a>'
                                    );
                                echo '</span>';
                            }
                            echo '<span class="comment-number">';
                                comments_number(
                                    esc_html__( '0 Comments', 'wp-coupon' ),
                                    esc_html__( '1 Comment', 'wp-coupon' ),
                                    esc_html__( '% Comments', 'wp-coupon' )
                                );
                            echo '</span>';
                            echo '</p>';


                        echo '</div>';

                        ?>
                    </div>
                    <div class="post-content">
                    <?php the_content(); ?>
                    </div>
                    <?php the_tags( '<div class="entry-tags">', ', ', '</div>' ); ?>
                </div>
                <?php

                wpcoupon_wp_link_pages(  );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>


            </main><!-- #main -->
        </div><!-- #primary -->

        <?php

        if ( $layout != 'no-sidebar' ) {
            get_sidebar();
        }

        ?>

    </div> <!-- /#content-wrap -->

<?php get_footer(); ?>
