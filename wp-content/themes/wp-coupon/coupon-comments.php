<?php
global $post;
// number comments per page
$per_page = apply_filters( 'st_number_coupon_comments', 10 );
//get total comments
$total = get_comments_number( $post->ID );
//calc total page
$total_page = ceil( $total/ $per_page );

$c_paged =  isset( $_REQUEST['c_paged'] ) ?  $_REQUEST['c_paged'] : 1;
if ( $c_paged <= 0 ) {
    $c_paged = 1;
}
// calc offset
$offset = ( $c_paged-1 )* $per_page;

$comments =  get_comments(  array(
    'post_id'   => $post->ID,
    'orderby'   => 'comment_date',
    'order'     => 'DESC',
    'number'    => $per_page,
    'offset'    => $offset,
) );

if ( $comments ) {
    $args = array(
        'walker'        =>  new  WPCoupon_Walker_Coupon_Comment,
        'style'         => 'div',
        'callback'      => 'wpcoupon_coupon_comment',
        'end-callback'  => 'wpcoupon_coupon_comment_end',
        'type'          => 'all',
        'reply_text'    => esc_html__( 'Reply', 'wp-coupon' ),
        'avatar_size'   => 32,
    );
    wp_list_comments( $args , $comments );

    if (  $c_paged < $total_page ) {
        ?>
        <div class="load-more wpb_content_element">
            <a class="load-more-btn ui button btn btn_primary btn_small" data-c-paged="<?php echo ( $c_paged + 1 ); ?>" data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>" href="#"><?php esc_html_e('Load More Comments', 'wp-coupon'); ?>
                <i class="arrow circle outline down icon"></i>
            </a>
        </div>
        <?php
    }
} else {
    if ( $c_paged <= 1 ) {
        ?>
        <h4 class="cm-no-comments"><?php esc_html_e( 'No comment, be the first.', 'wp-coupon' ) ?></h4>
        <?php
    }

}

?>

