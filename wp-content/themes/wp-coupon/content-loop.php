<?php
global $post;
while ( have_posts() ) {
    the_post();
    if ( 'coupon' == get_post_type() ) {
        wpcoupon_setup_coupon( $post );
        get_template_part( 'loop/loop','coupon-cat' );
    } else {
        get_template_part( 'loop/loop' );
    }

}
