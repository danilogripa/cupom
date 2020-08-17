<?php
    /**
     * The template for displaying the footer.
     *
     * Contains the closing of the #content div and all content after
     *
     * @package WP Coupon
     */
    global $st_option;

        if( wpcoupon_get_option( 'before_footer', '' ) != '' ) {
            if( wpcoupon_get_option( 'before_footer_apply', 'home' ) != 'all' ) {
                if ( get_option( 'show_on_front' ) == 'page' && is_home() ) {
                    echo do_shortcode( wpcoupon_get_option( 'before_footer', '' ) );
                }
            } else {
                echo do_shortcode( wpcoupon_get_option( 'before_footer', '' ) );
            }

        }
        ?>
		</div> <!-- END .site-content -->

        <footer id="colophon" class="site-footer <?php echo ( $st_option['footer_widgets'] ) ? 'footer-widgets-on' : 'footer-widgets-off' ?>" role="contentinfo">
			<div class="container">

                <?php if ( wpcoupon_get_option( 'footer_widgets' ) ) {

                    $footer_columns = wpcoupon_get_option( 'footer_columns', 4 );
                    $layouts = 16;
                    if ( $footer_columns > 1 ){
                        $layouts = wpcoupon_get_option( 'footer_columns_layout_'.$footer_columns );
                    }
                    $layouts = explode( '+', $layouts );
                    foreach ( $layouts as $k => $v ) {
                        $v = absint( trim( $v ) );
                        $v =  $v >= 16 ? 16 : $v;
                        $layouts[ $k ] = $v;
                    }

                    ?>
                    <div class="footer-widgets-area">
                        <div class="sidebar-footer footer-columns stackable ui grid clearfix">
                            <?php
                            for ($count = 0; $count < $footer_columns; $count++) {
                                ?>
                                <div id="footer-<?php echo esc_attr( $count +1 ) ?>" class="<?php echo esc_attr( wpcoupon_number_to_column_class( $layouts[ $count ] ) ); ?> wide column footer-column widget-area" role="complementary">
                                    <?php dynamic_sidebar('footer-' . ( $count +1 ) ); ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                <?php } ?>

                <div class="footer_copy">
                    <p>
                        <?php
                        echo '<span>';
                        if ( wpcoupon_get_option('footer_copyright') == '' ) {
                            printf( esc_html__( 'Copyright &copy; %1$s %2$s. All Rights Reserved. ', 'wp-coupon' ), esc_attr(date('Y')), get_bloginfo('name') );
                        } else {
                            echo wp_kses_post( wpcoupon_get_option('footer_copyright') );
                        }
                        echo '</span>';

                        if ( wpcoupon_get_option('enable_footer_author') ) {
                            echo '<span>'.sprintf( esc_html__( 'WordPress Coupon Theme by %s', 'wp-coupon' ), '<a href="https://www.famethemes.com">FameThemes</a>' ).'</span>' ;
                        }
                        ?>
                    </p>
                    <nav id="footer-nav" class="site-footer-nav">
                        <?php wp_nav_menu( array( 'container' => false, 'theme_location' => 'footer', 'fallback_cb' => false ) ); ?>
                    </nav>
                </div>
            </div>
		</footer><!-- END #colophon-->

	</div><!-- END #page -->

    <?php wp_footer(); ?>
			<script type="text/javascript">
				//remover nav-user-action
			//	document.getElementsByClassName("nav-user-action")[0].innerHTML = "";
			//	</script>

    <script type="text/javascript" src="https://www.audiencegear.com/rtg?campaign_id=68&campaign_name=Cuponocity"></script>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KWLZVMC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</body>
</html>
