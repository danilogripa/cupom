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
        <li><a title="<?php esc_attr_e( 'Coupon Comments', 'wp-coupon' ); ?>" data-reveal="reveal-comments" href="#"><i class="comments outline icon"></i> <?php
                printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'wp-coupon' ), number_format_i18n( get_comments_number() ) );
            ?></a></li>
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
    <div data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" class="reveal-content reveal-comments">
        <span class="close"></span>
        <?php if ( get_comments_number(  ) ) { ?>
        <h4 class="cm-heading"><?php esc_html_e( 'Showing most recent comments', 'wp-coupon' ) ?></h4>
        <?php } ?>
        <div data-id="<?php echo wpcoupon_coupon()->ID; ?>" class="comments-coupon-<?php echo wpcoupon_coupon()->ID; ?> ui threaded comments">
            <h4><?php esc_html_e( 'Loading comments....', 'wp-coupon' ) ?></h4>
        </div>

        <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <h4 class="cm-comment-closed"><?php esc_html_e( 'Comments are closed.', 'wp-coupon' ); ?></h4>
        <?php else: ?>
        <h4><?php esc_html_e( 'Let other know how much you saved', 'wp-coupon' ); ?></h4>
        <form class="coupon-comment-form" data-cf="<?php echo wpcoupon_coupon()->ID; ?>" action="<?php echo site_url('/'); ?>"  method="post">

            <div style="display: none;" class="ui success message">
                <?php esc_html_e( 'Your comment submitted.', 'wp-coupon' ); ?>
            </div>

            <div style="display: none;" class="ui negative message">
                <?php esc_html_e( 'Something wrong! Please try again later.', 'wp-coupon' ); ?>
            </div>

            <div class="ui form">
                <div class="field comment_content">
                    <textarea class="comment_content" name="c_comment[comment_content]" placeholder="<?php esc_attr_e( 'Add a comment', 'wp-coupon' ); ?>"></textarea>
                </div>
                <?php if ( ! is_user_logged_in() ) { ?>
                <div class="two fields">
                    <div class="field comment_author">
                        <input type="text" class="comment_author" name="c_comment[comment_author]" placeholder="<?php esc_attr_e( 'Your Name', 'wp-coupon' ); ?>">
                    </div>
                    <div class="field comment_author_email">
                        <input type="text" class="comment_author_email"  name="c_comment[comment_author_email]" placeholder="<?php esc_attr_e( 'Your Email', 'wp-coupon' ) ?>">
                    </div>
                </div>
                <?php } ?>
                <button type="submit" class="ui button btn btn_primary"><?php esc_html_e( 'Submit', 'wp-coupon' ); ?></button>
            </div>
            <input type="hidden" name="action" value="wpcoupon_coupon_ajax">
            <input type="hidden" name="st_doing" value="new_comment">
            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce(); ?>">
            <input type="hidden" name="c_comment[comment_parent]" class="comment_parent">
            <input type="hidden" name="c_comment[comment_post_ID]" value="<?php echo wpcoupon_coupon()->ID; ?>" class="comment_post_ID">
        </form>
        <?php endif; ?>
    </div>
</div>