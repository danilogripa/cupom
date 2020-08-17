<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h3 class="comments-title ui dividing header">
            <?php
            printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'wp-coupon' ),
                number_format_i18n( get_comments_number() ), get_the_title() );
            ?>
        </h3>

        <div class="ui comments comment-list">
            <?php
            wp_list_comments( array(
                'style'        => 'div',
                'short_ping'   => true,
                'avatar_size'  => 50,
                'callback'     => 'wpcoupon_comment',
                'end-callback' => 'wpcoupon_comment_end',
            ) );
            ?>
        </div><!-- .comment-list -->

        <?php
        // Comment Nav
        // Are there comments to navigate through?
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
            ?>
            <nav class="navigation comment-navigation" role="navigation">
                <h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'wp-coupon' ); ?></h2>
                <div class="nav-links">
                    <?php
                    if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'wp-coupon' ) ) ) :
                        printf( '<div class="nav-previous">%s</div>', $prev_link );
                    endif;

                    if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'wp-coupon' ) ) ) :
                        printf( '<div class="nav-next">%s</div>', $next_link );
                    endif;
                    ?>
                </div><!-- .nav-links -->
            </nav><!-- .comment-navigation -->
            <?php
        endif;

        ?>

    <?php endif; // have_comments() ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'wp-coupon' ); ?></p>
    <?php endif; ?>

    <?php


    $post_id = get_the_ID();

    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';


    $args['format'] = 'html5';

    $req      = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $html_req = ( $req ? " required='required'" : '' );
    $html5    = true;

    $required_text = sprintf( ' ' . esc_html__('Required fields are marked %s', 'wp-coupon'), '<span class="required">*</span>' );

    $comment_args = array(
        'fields'  =>  array(
            'author'  => '<div class="ui fluid left icon input comment-form-author">' .
                '<i class="user icon"></i>'.
                ( $req ? ' <span class="required">*</span>' : '' ) .
                '<input id="author" placeholder="'.esc_attr__( 'Name', 'wp-coupon' ).'" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' />
                        </div>',
            'email'   => '<div class="ui fluid left icon input comment-form-email">
                            <i class="mail outline icon"></i>'.
                ( $req ? ' <span class="required">*</span>' : '' ) .
                '<input id="email" placeholder="'.esc_attr__( 'Email', 'wp-coupon' ).'" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' />
                        </div>',
            'url'       => '<div class="ui fluid left icon input comment-form-url">
                                <i class="world icon"></i>
                                <input id="url" placeholder="'.esc_attr__( 'Website', 'wp-coupon' ).'" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />
                            </div>',
        ),
        'comment_field' => '<div class="field comment-form-comment">
                                <label for="comment">' . _x( 'Comment', 'noun' ,'wp-coupon' ) . '</label>
                                <textarea id="comment" name="comment" rows="6" aria-describedby="form-allowed-tags" aria-required="true" required="required"></textarea>
                           </div>',
        /** This filter is documented in wp-includes/link-template.php */
        'must_log_in'          => '<p class="must-log-in">' .
            sprintf(
                esc_html__( 'You must be %s to post a comment.', 'wp-coupon' ),
                '<a href="'.esc_url( wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ).'">'.esc_html__( 'logged in', 'wp-coupon' ).'</a>'
            ) .
            '</p>',
        /** This filter is documented in wp-includes/link-template.php */
        'logged_in_as'         => '<p class="logged-in-as">' .
            sprintf(
                esc_html__( 'Logged in as %1$s. %2$s', 'wp-coupon' ),
                '<a href="'.get_edit_user_link().'">'.$user_identity.'</a>',
                '<a href="'. wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ).'" title="'.esc_attr__( 'Log out of this account', 'wp-coupon' ).'">Log out?</a>'
            ) . '</p>',
        'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . esc_html__( 'Your email address will not be published.', 'wp-coupon' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>',
        'comment_notes_after'  => '',
        'id_form'              => 'commentform',
        'id_submit'            => 'submit',
        'class_submit'         => 'submit',
        'class_form'           => 'ui form comment-form',
        'name_submit'          => 'submit',
        'submit_button'        => '<button name="%1$s" id="%2$s" class="ui button btn_primary %3$s">%4$s</button>',
        'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
        'format'               => 'xhtml',
    );

    if ( is_singular( 'coupon' ) ) {
        $comment_args['title_reply'] = esc_html__( 'Let other know how much you saved', 'wp-coupon' );
    }

    comment_form( $comment_args );
    ?>

</div><!-- .comments-area -->
