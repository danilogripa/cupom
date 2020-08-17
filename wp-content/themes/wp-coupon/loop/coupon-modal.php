<!-- Coupon Modal -->
<div data-modal-id="<?php echo wpcoupon_coupon()->ID; ?>" class="ui modal coupon-modal coupon-code-modal">
    <div class="scrolling content">
        <div class="coupon-header clearfix">
            <div class="coupon-store-thumb">
                <?php
                echo wpcoupon_coupon()->get_thumb( );
                ?>
            </div>
            <div class="coupon-title" title="<?php echo esc_attr( get_the_title( wpcoupon_coupon()->ID ) ) ?>"><?php echo get_the_title( wpcoupon_coupon()->ID ); ?></div>
            <span class="close icon"></span>
        </div>
        <div class="coupon-content">
            <p class="coupon-type-text">
                <?php
                switch ( wpcoupon_coupon()->get_type() ) {
                    case 'sale':
                        esc_html_e( 'Deal Activated, no coupon code required!', 'wp-coupon' );
                        break;
                    case 'print':
                        esc_html_e( 'Print this coupon and redeem it in-store', 'wp-coupon' );
                        break;
                    default:
                        esc_html_e( 'Copy this code and use at checkout', 'wp-coupon' );
                }
                ?>
            </p>
            <div class="modal-code">
                <?php
                switch ( wpcoupon_coupon()->get_type() ) {

                    case 'sale':
                        ?>
                        <a class="ui button btn btn_secondary deal-actived" target="_blank" rel="nofollow" href="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>"><?php esc_html_e( 'Go To Store', 'wp-coupon' ); ?><i class="angle right icon"></i></a>
                        <?php
                        break;
                    case 'print':
                        $image_url = esc_url( wpcoupon_coupon()->get_print_image() );
                        ?>
                        <a class="btn-print-coupon" target="_blank" href="<?php echo esc_attr( $image_url ); ?>"><img alt="" src="<?php echo esc_attr( $image_url ); ?>"/></a>
                        <?php
                        break;
                    default:
                        ?>
                        <div class="coupon-code">
                            <div class="ui fluid action input massive">
                                <input  type="text" class="code-text" autocomplete="off" readonly value="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>">
                                <button class="ui right labeled icon button btn btn_secondary">
                                    <i class="copy icon"></i>
                                    <span><?php esc_html_e( 'Copy', 'wp-coupon' ); ?></span>
                                </button>
                            </div>
                        </div>

                    <?php
                }
                ?>
            </div>
            <div class="clearfix">
                <div class="user-ratting ui icon basic buttons">
                    <div class="ui button icon-popup coupon-vote" data-vote-type="up" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( 'This worked', 'wp-coupon' ); ?>"><i class="smile outline icon"></i></div>
                    <div class="ui button icon-popup coupon-vote" data-vote-type="down" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( "It didn't work", 'wp-coupon' ); ?>"><i class="frown outline icon"></i></div>
                    <div class="ui button icon-popup coupon-save" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( "Save this coupon", 'wp-coupon' ); ?>"><i class="outline star icon"></i></div>
                </div>

                <?php if ( wpcoupon_coupon()->get_type() !== 'sale' ) { ?>
                    <?php if ( wpcoupon_coupon()->get_type() == 'print' ) { ?>
                        <a class="ui button btn btn_secondary go-store btn-print-coupon"  href="<?php echo esc_attr( $image_url ); ?>"><?php esc_html_e( 'Print Now', 'wp-coupon' ); ?> <i class="print icon"></i></a>
                    <?php } else { ?>
                        <a href="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>" rel="nofollow" target="_blank" class="ui button btn btn_secondary go-store"><?php esc_html_e( 'Go To Store', 'wp-coupon' ); ?><i class="angle right icon"></i></a>
                    <?php } ?>
                <?php } ?>

            </div>
            <div class="clearfixp">
                <span class="user-ratting-text"><?php esc_html_e( 'Did it work?', 'wp-coupon' ); ?></span>
                <span class="show-detail"><a href="#"><?php esc_html_e( 'Coupon Detail', 'wp-coupon' ) ?><i class="angle down icon"></i></a></span>
            </div>
            <div class="coupon-popup-detail">
                <div class="coupon-detail-content"><?php
                    echo str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', wpcoupon_coupon()->post_content ) );  ;
                    ?></div>
                <p><strong><?php esc_html_e( 'Expires', 'wp-coupon' ); ?></strong>: <?php echo wpcoupon_coupon()->get_expires( null, true ); ?></p>
                <p><strong><?php esc_html_e( 'Submitted', 'wp-coupon' ); ?></strong>:
                    <?php printf( esc_html__( '%s ago', 'wp-coupon' ), human_time_diff( get_the_time('U'), current_time('timestamp') ) ); ?>
                </p>
            </div>
        </div>
        <div class="coupon-footer">
            <ul class="clearfix">
                <li><span><i class="wifi icon"></i> <?php printf( esc_html__( '%1$s Used - %2$s Today', 'wp-coupon' ), wpcoupon_coupon()->get_total_used(), wpcoupon_coupon()->get_used_today() ); ?></span></li>
                <li class="modal-share">
                    <a class="" href="#"><i class="share alternate icon"></i> <?php esc_html_e( 'Share', 'wp-coupon' ); ?></a>
                    <div class="share-modal-popup ui popup top right transition hidden---">
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
                </li>
            </ul>

        </div>
    </div>
</div>