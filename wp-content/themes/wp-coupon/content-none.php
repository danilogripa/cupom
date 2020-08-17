
<h1 class="not-found-page-title"><?php esc_html_e( 'Nothing Found', 'wp-coupon' ); ?></h1>

<div class="page-content">
    <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

        <p><?php printf( esc_html__( 'Ready to publish your first post? %s.', 'wp-coupon' ), '<a href="'.esc_url( admin_url( 'post-new.php' ) ).'">Get started here</a>' ); ?></p>

    <?php elseif ( is_search() ) : ?>
        <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wp-coupon' ); ?></p>
        <?php get_search_form(); ?>

    <?php else : ?>

        <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wp-coupon' ); ?></p>
        <?php get_search_form(); ?>

    <?php endif; ?>
</div><!-- .page-content -->