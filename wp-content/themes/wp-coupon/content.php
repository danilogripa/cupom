<?php
the_post();
global $post;
if ( get_post_meta( $post->ID, '_wpc_shadow_box' , true ) == 'yes' ) {
    ?>
    <div <?php post_class( 'post-entry shadow-box content-box' ); ?>>
       <?php  the_content(); ?>
    </div>
    <?php
} else {
    the_content();
}
