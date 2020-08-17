<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package WP Coupon
 */

global $st_option;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KWLZVMC');</script>
<!-- End Google Tag Manager -->

    <meta name="verify-admitad" content="2a898bdef3" />
    <meta name="verification" content="173ddb3a265f840a15abe953bef059a0" />	    <meta name="lomadee-verification" content="22928558" />
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<script type="text/javascript">
var lmdimgpixel=document.createElement('img');
lmdimgpixel.src='//secure.lomadee.com/pub.png?pid=22825434';
lmdimgpixel.id='lmd-verification-pixel-22825434';
lmdimgpixel.style='display:none';

var elmt = document.getElementsByTagName('body')[0];
elmt.appendChild(lmdimgpixel);
</script>

    <div id="page" class="hfeed site">
    	<header id="masthead" class="ui page site-header" role="banner">
            <?php do_action('wpcoupon_before_header_top'); ?>
            <div class="primary-header">
                <div class="container">
                    <div class="logo_area fleft">
                        <?php if ( wpcoupon_get_option('site_logo', false, 'url') != '' ) { ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                            <img src="https://cuponocity.com/wp-content/uploads/2019/08/logo-vert-nova.png" alt="<?php echo get_bloginfo( 'name' ) ?>" />
                        </a>
                        <?php } else { ?>
                        <div class="title_area">
                            <?php if ( is_home() || is_front_page() ) { ?>
                                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                            <?php } else {  ?>
                                <h2 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
                            <?php } ?>
                            <p class="site-description"><?php  bloginfo( 'description' ); ?></p>
                        </div>
                        <?php } ?>
                    </div>

                    <?php
                    $header_icons = wpcoupon_get_option( 'header_icons' );
                    if ( $header_icons ) {
                    ?>
                    <div class="header-highlight fleft">
                        <?php
                        foreach( $header_icons as $icon ){
                        ?>
                        <a href="<?php echo ( $icon['url'] ) ? esc_attr( $icon['url'] ) : '#'; ?>">
                            <div class="highlight-icon"><?php echo balanceTags( $icon['description'] ); ?></div>
                            <div class="highlight-text"><?php echo esc_html( $icon['title'] ); ?></div>
                        </a>
                        <?php } ?>

                    </div>
                    <?php } ?>

                    <div class="header_right fright">
                        <form action="<?php echo home_url( '/' ); ?>" method="get" id="header-search">
                            <div class="header-search-input ui search large action left icon input">
                                <input autocomplete="off" class="prompt" name="s" placeholder="<?php esc_attr_e( 'Search stores for coupons, deals ...', 'wp-coupon' ); ?>" type="text">
                                <i class="search icon"></i>
                                <button class="header-search-submit ui button"><?php esc_html_e( 'Search', 'wp-coupon' ); ?></button>
                                <div class="results"></div>
                            </div>
                            <div class="clear"></div>
                            <?php

                            $store_ids =  wpcoupon_get_option( 'top_search_stores' );

                            if ( ! empty( $store_ids ) ) {
                                $stores = wpcoupon_get_stores( array(  'include'=> $store_ids ) );
                                if ( $stores ) {

                                    $links = array();
                                    foreach ( $stores as $store ){
                                        $links[] = '<a href="'.get_term_link( $store, 'coupon_store' ).'">'.esc_html( $store->name ).'</a>';
                                    }

                                    $links = join( ', ', $links );
                                    if ( $links ) {
                                        ?>
                                        <div class="search-sample">
                                            <?php
                                            printf('<span>'.esc_html__( 'Top Searches:', 'wp-coupon' ).'</span>%1$s,...', $links);
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div> <!-- END .header -->

            <?php do_action('wpcoupon_after_header_top'); ?>

            <div id="site-header-nav" class="site-navigation">
                <div class="container">
                    <nav class="primary-navigation clearfix fleft" role="navigation">
                        <a href="#content" class="screen-reader-text skip-link"><?php esc_html_e( 'Skip to content', 'wp-coupon' ); ?></a>
                        <div id="nav-toggle"><i class="content icon"></i></div>
                        <ul class="st-menu">
                           <?php wp_nav_menu( array('theme_location' => 'primary', 'container' => '', 'items_wrap' => '%3$s' ) ); ?>
                        </ul>
                    </nav> <!-- END .primary-navigation -->

                    <div class="nav-user-action fright clearfix">
                        <?php

                        if ( class_exists( 'WPCoupon_User' ) ) {
                            WPCoupon_User::nav();
                        }
                        ?>
                    </div> <!-- END .nav_user_action -->
                </div> <!-- END .container -->
            </div> <!-- END #primary-navigation -->
    	</header><!-- END #masthead -->
        <div id="content" class="site-content">
<?php
