<?php
global $post, $authordata;

if ( ! is_object( $authordata ) ) {
    $authordata = get_user_by( 'ID', $post->post_author );
}

?>
<div <?php post_class( 'post-entry shadow-box content-box' ); ?>>
    <?php if ( has_post_thumbnail( ) ) { ?>
        <div class="shadow-box post-thumbnail">
            <?php the_post_thumbnail( 'wpcoupon_blog_medium' ); ?>
        </div>

    <?php }  ?>

    <?php  if ( $authordata ) { ?>
    <div class="post-meta">
        <?php
        echo '<div class="author-avatar">';
            echo get_avatar(get_the_author_meta('email', $authordata->ID), 50);
        echo '</div>';

        echo '<div class="post-meta-data">';

            the_title('<h2 class="post-title"><a title="'.esc_attr( get_the_title() ).'" href="'.get_permalink().'"> ', '</a></h2>');
            echo '<p class="meta-line-2">';
            if ( is_object( $authordata ) ) {
                echo '<span class="author-name">';
                printf(
                    esc_html__('Posts by %s', 'wp-coupon' ),
                    sprintf( '<a href="'.esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ).'" title="'.esc_attr( sprintf( esc_html__( 'Posts by %s', 'wp-coupon' ), get_the_author() ) ).'" rel="author">'.get_the_author().'</a>' )
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

        echo '</div>'; // .post-meta-data

        ?>
    </div>
    <?php } ?>

   <div class="post-content">
       <?php the_excerpt(); ?>
       <a class="read-more tiny ui button btn_primary" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'wp-coupon' ) ?></a>
   </div>

</div>
