<?php
global $post;
?>
<div class="search-stores shadow-box box-item">
    <div class="ui <?php echo wpcoupon_number_to_html_class(4); ?> column grid">
        <?php
        while ( have_posts() ) {
            the_post();
            wpcoupon_setup_store( $post );
            ?>
            <div class="column">
                <div class="store-thumb">
                    <a class="ui image middle aligned" href="<?php echo get_permalink($post); ?>">
                        <?php echo wpcoupon_store()->get_thumbnail() ?>
                    </a>
                </div>
                <a class="middle aligned" href="<?php echo get_permalink($post); ?>" class="store-ename"><?php the_title(); ?></a>
            </div>
        <?php }  ?>
    </div>
</div>