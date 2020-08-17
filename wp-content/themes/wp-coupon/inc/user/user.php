<?php

class WPCoupon_User {

    function __construct(){
        add_action( 'wp_users_init', array( $this, 'user_init' ) );
    }

    public function user_init(){
        // This is filter of ST-User plugin
        add_filter( 'st_get_site_layout_single', array( $this, 'full_width_account_page' ) , 15, 2 );
        // Remove default ST user sidebar
        remove_action( 'wp_users_profile_before_form_body', array( 'WP_Users_Public', 'profile_sidebar' ), 15 );
        remove_action( 'wp_users_profile_form_body', array( 'WP_Users_Public', 'profile_content' ), 15 );
        remove_action( 'wp_users_profile_meta', array( 'WP_Users_Public', 'socials' ), 15  );

        // Add new sidebar
        add_action( 'wp_users_profile_before_form_body', array( __CLASS__, 'profile_sidebar' ), 15, 3 );
        add_action( 'wp_users_profile_form_body', array( __CLASS__, 'profile_content' ), 15, 3 );
        add_action( 'wp_users_profile_meta', array( __CLASS__, 'socials' ), 15  );
    }


    /**
     * Make the account page always full width
     *
     * @param $layout
     * @param $page_id
     * @return string
     */
    function full_width_account_page( $layout ,  $page_id ){
        if ( $page_id ==  WP_Users()->settings['account_page'] ) {
            return 'no-sidebar';
        }
        return $layout;
    }

    /**
     * Save a coupon
     *
     * @since 1.0.0
     * @param $coupon_id ID of coupon
     * @return bool
     */
    public static function save_coupon( $coupon_id ) {
        // get current user
        $user = wp_get_current_user();
        // check user
        if ( ! $user->exists() ) {
            return false;
        }

        $coupon = new WPCoupon_Coupon( $coupon_id );
        // check coupon
        if ( ! $coupon->is_coupon() ) {
            return false;
        }

        //get the user favorite coupon
        $list  = get_user_meta( $user->ID, '_wpc_saved_coupons' , true );
        $list  = explode( ',', $list );
        // check if coupon is already added to favorite
        if ( in_array( $coupon->ID , $list ) ) {
            return true;
        } else {
            $list[ $coupon->ID ] = $coupon->ID;
            $list = array_filter( $list );
            update_user_meta( $user->ID, '_wpc_saved_coupons',  join( ',', array_unique( $list ) ) );
        }

        return true;
    }

    /**
     * Remove a saved coupon
     *
     * @since 1.0.0
     * @param $coupon_id ID of coupon
     * @return bool
     */
    public static function remove_saved_coupon( $coupon_id ) {
        // get current user
        $user = wp_get_current_user();
        // check user
        if ( ! $user->exists() ) {
            return false;
        }

        $coupon = new WPCoupon_Coupon( $coupon_id );
        // check coupon
        if ( ! $coupon->is_coupon() ) {
            return false;
        }

        //get the user favorite coupon
        $list  = get_user_meta( $user->ID, '_wpc_saved_coupons' , true );
        $list  = explode( ',', $list );
        foreach( $list as $k=> $v ){
            if ( $v ==$coupon_id ) {
                unset( $list[ $k ] );
            }
        }

        update_user_meta( $user->ID, '_wpc_saved_coupons',  join( ',', array_unique( $list ) ) );

        return true;
    }


    /**
     * Add store to favorite
     *
     * @since 1.0.0
     * @param $coupon_id ID of coupon
     * @return bool
     */
    public static function add_to_favorite( $store_id ) {
        // get current user
        $user = wp_get_current_user();
        // check user
        if ( ! $user->exists() ) {
            return false;
        }

        $store = new WPCoupon_Store( $store_id );
        if ( ! $store->is_store() ) {
            return false;
        }

        //get the user favorite coupon
        $list = get_user_meta( $user->ID, '_wpc_favorite_stores' , true );
        $list  = explode( ',',$list );

        // check if coupon is already added to favorite
        if ( in_array( $store->term_id , $list ) ) {
            return true;
        } else {
            $list[ ] = $store->term_id;
            $list = array_filter( $list );
            update_user_meta( $user->ID, '_wpc_favorite_stores',  join( ',', $list ) );
        }

        return true;
    }

    static function delete_favorite( $store_id ){
        // get current user
        $user = wp_get_current_user();
        // check user
        if ( ! $user->exists() ) {
            return false;
        }

        //get the user favorite coupon
        $list = get_user_meta( $user->ID, '_wpc_favorite_stores' , true );
        $list  = explode( ',',$list );

        foreach ( $list as $k=> $v ) {
            if ( $v == $store_id ) {
                unset( $list[ $k ] );
            }
        }
        update_user_meta( $user->ID, '_wpc_favorite_stores',  join( ',', $list ) );

        return true;
    }

    /**
     * Display profile sidebar
     * @param $user
     */
    public static function profile_sidebar( $user, $current_user, $action = false ){

        $link =  WP_Users()->get_profile_link( $user );

        ?>
        <ul class="wpu-form-sidebar stuser-form-sidebar">
            <?php if ( WP_Users()->is_current_user( $user, $current_user )  ){ ?>
            <li class="<?php echo $action == '' ? 'active' : ''; ?>"><a class="st-profile-link" href="<?php echo WP_Users()->get_profile_link( $user ); ?>"><?php esc_html_e( 'Dashboard', 'wp-coupon' ); ?></a></li>
            <li class="<?php echo $action == 'saved_coupons' ? 'active' : ''; ?>"><a class="st-profile-link" href="<?php echo add_query_arg( array( 'wpu_action' => 'saved_coupons' ), $link ) ; ?>"><?php esc_html_e( 'Saved Coupons', 'wp-coupon' ); ?></a></li>
            <li class="<?php echo $action == 'favorites_stores' ? 'active' : ''; ?>"><a class="st-profile-link" href="<?php echo add_query_arg( array( 'wpu_action' => 'favorites_stores' ), $link ) ; ?>"><?php esc_html_e( 'Favorites Stores', 'wp-coupon' ); ?></a></li>
            <li class="<?php echo $action == 'edit' ? 'active' : ''; ?>"><a class="st-edit-link" href="<?php echo WP_Users()->get_edit_profile_link( $user ); ?>"><?php esc_html_e( 'Settings', 'wp-coupon' ); ?></a></li>
            <?php } else { ?>
            <li class="<?php echo $action == '' ? 'active' : ''; ?>"><a class="st-profile-link" href="<?php echo WP_Users()->get_profile_link( $user ); ?>"><?php esc_html_e( 'Public profile', 'wp-coupon' ); ?></a></li>
            <?php } ?>
        </ul>
        <?php
    }

    public static function socials( $user ){
        ?>
        <div class="st-user-socials">

            <?php if (  get_user_meta( $user->ID, 'facebook', true )   != '' ) {  ?>
            <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook', true ) ); ?>" class="ui circular facebook icon button">
                <i class="facebook icon"></i>
            </a>
            <?php } ?>
            <?php if (  get_user_meta( $user->ID, 'twitter', true )   != '' ) {  ?>
            <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter', true ) ); ?>" class="ui circular twitter icon button">
                <i class="twitter icon"></i>
            </a>
            <?php } ?>
            <?php if (  get_user_meta( $user->ID, 'google', true )   != '' ) {  ?>
            <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'google', true ) ); ?>" class="ui circular google plus icon button">
                <i class="google plus icon"></i>
            </a>
            <?php } ?>

        </div>
        <?php
    }

    /**
     * Display recent saved coupons in nav menu
     *
     * @param $user
     */
    public static function recent_saved_coupons_box( $user ){
        $link = function_exists( 'WP_Users' ) ?  WP_Users()->get_profile_link( $user ) : '';
        $saved_link =  add_query_arg(  array( 'wpu_action' => 'saved_coupons' ),  $link );
        $coupons = self::get_save_coupons( apply_filters( 'wpcoupon_nav_number_saved_coupons', 3 )  );
        if ( $coupons ) {
            ?>
            <h4 class="menu-box-title"><?php esc_html_e('Recently Saved Coupons', 'wp-coupon'); ?></h4>
            <div class="saved-coupons">
                <?php

                if ($coupons) {
                    foreach ($coupons as $coupon) {
                        wpcoupon_setup_coupon( $coupon, $saved_link );
                        ?>
                        <div class="saved-coupon clearfix">
                            <div class="store-thumb coupon-link">
                                <a href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"
                                    data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>">
                                    <?php
                                    if ( is_tax() ) {
                                        echo wpcoupon_coupon()->get_store_thumb('wpcoupon_small_thumb');
                                    } else {
                                        echo wpcoupon_coupon()->get_thumb('wpcoupon_small_thumb');
                                    }
                                    ?>
                                </a>
                            </div>
                            <div class="coupon-text">
                                <a rel="nofollow"
                                   class="coupon-link"
                                   title="<?php echo esc_attr( get_the_title( wpcoupon_coupon()->ID ) ) ?>"
                                   data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                   data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>"
                                   href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php echo wpcoupon_coupon()->post_title; ?></a>
                                <span class="exp-text">
                                    <?php echo wpcoupon_coupon()->get_expires(); ?>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <a class="more-links" href="<?php echo esc_url( $saved_link ); ?>"><?php esc_html_e( 'See all your saved coupons', 'wp-coupon'); ?> <i class="angle right icon"></i></a>
            <?php
        } else {
            ?>
            <div class="nothing-box">
                <div class="thumb">
                    <i class="frown icon"></i>
                </div>
                <p><?php esc_html_e( 'It\'s looks like you didn\'t add any coupons yet.', 'wp-coupon' ); ?></p>
            </div>
            <?php
            $page_id = wpcoupon_get_option('coupons_listing_page');
            if ( $page_id && $page = get_post( $page_id ) ) {
                ?>
                <a class="browse-more" href="<?php echo get_permalink( $page_id ); ?>"><?php esc_html_e('Browse Coupons to add', 'wp-coupon'); ?> <i class="angle right icon"></i></a>
                <?php
            }
        }

        wp_reset_postdata();
    }

    /**
     * Display recent favorite stores in nav menu
     *
     * @param $user
     */
    public static function recent_favorite_stores_box( $user ) {

        $link = function_exists( 'WP_Users' ) ?  WP_Users()->get_profile_link( $user ) : '';
        $favorite_link =  add_query_arg(  array( 'wpu_action' => 'favorites_stores' ),  $link );
        $stores =  self::get_stores( apply_filters( 'wpcoupon_nav_number_favorite_stores', 6 ) );
        global $post;
        if ( $stores ){
            ?>
            <h4 class="menu-box-title"><?php esc_html_e( 'Recent Favorite Stores', 'wp-coupon' ); ?></h4>
            <div class="ui two column grid">
                <?php
                if ( $stores ) {
                    foreach ( $stores as $store ){
                        wpcoupon_setup_store( $store );
                        ?>
                        <div class="column">
                            <div class="store-thumb">
                                <a href="<?php echo wpcoupon_store()->get_url(); ?>" title="<?php echo esc_attr( wpcoupon_store()->name ); ?>" class="ui image middle aligned">
                                    <?php echo wpcoupon_store()->get_thumbnail(); ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }

                ?>
            </div>
            <a class="more-links" href="<?php echo esc_url( $favorite_link ); ?>"><?php esc_html_e( 'See all your favorite stores', 'wp-coupon' ); ?> <i class="angle right icon"></i></a>
            <?php
        } else { ?>

            <div class="nothing-box">
                <div class="thumb">
                    <i class="frown icon"></i>
                </div>
                <p><?php esc_html_e( 'It\'s looks like you didn\'t add any stores yet.', 'wp-coupon' ); ?></p>
            </div>
            <?php

            $page_id = wpcoupon_get_option('stores_listing_page');

            if ( $page_id && $page = get_post( $page_id ) ) {
                ?>
                <a class="browse-more" href="<?php echo get_permalink( $page ); ?>"><?php esc_html_e('Browse Stores to add', 'wp-coupon'); ?> <i class="angle right icon"></i></a>
                <?php
            }
        } ?>
        <?php

        // Reset post data
        $GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
        wp_reset_postdata();
    }

    /**
     * Display user nav
     */
    public static function nav(){

        if ( ! function_exists( 'WP_Users' ) ) {
            return ;
        }

        $is_logged_in = is_user_logged_in();
        $user =  wp_get_current_user();
        $link =  WP_Users()->get_profile_link( $user );

        $favorite_link = '#';
        $saved_link = '#';

        if ( $is_logged_in ){
            $favorite_link =  add_query_arg(  array( 'wpu_action' => 'favorites_stores' ),  $link );
            $saved_link =  add_query_arg(  array( 'wpu_action' => 'saved_coupons' ),  $link );
        }

        // Check if WP_Users Plugin is actived
        if ( function_exists( 'WP_Users' ) ) {
            ?>
            <ul class="st-menu">
                <li class="">
                    <a href="<?php echo esc_url( $saved_link ); ?>">
                        <i class="outline star icon"></i> <span class="hide-on-tiny"><?php esc_html_e( 'Saved', 'wp-coupon' ); ?></span>
                    </a>

                    <div class="menu-box ajax-saved-coupon-box">
                        <?php
                        if ( $is_logged_in ) {
                            self::recent_saved_coupons_box( $user );
                        } else {
                            ?>
                            <div class="nothing-box stuser-login-btn">
                                <div class="thumb">
                                    <i class="frown icon"></i>
                                </div>
                                <p><?php esc_html_e( 'Please login to see your saved coupons', 'wp-coupon' ); ?></p>
                            </div>
                            <?php
                        }
                        ?>
                        </div>
                </li>
                <li class="">
                    <a href="<?php echo esc_url( $favorite_link );  ?>">
                        <i class="empty heart icon"></i> <span class="hide-tiny-screen"><?php esc_html_e( 'Favorites', 'wp-coupon' ); ?></span>
                    </a>

                    <div class="menu-box ajax-favorite-stores-box">
                        <?php
                        if ( $is_logged_in ) {
                            self::recent_favorite_stores_box( $user );
                        } else {
                            ?>
                            <div class="nothing-box stuser-login-btn">
                                <div class="thumb">
                                    <i class="frown icon"></i>
                                </div>
                                <p><?php esc_html_e( 'Please login to see your favorite stores', 'wp-coupon' ); ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </li>

                <li class="menu-item-has-children">
                    <a data-is-logged="<?php echo is_user_logged_in() ? 'true' : 'false'; ?>" class="wpu-login-btn" href="<?php echo WP_Users()->get_profile_link(); ?>"><i class="power icon"></i><?php echo ( $is_logged_in ) ?  esc_html__( 'Dashboard', 'wp-coupon' ) :  esc_html__( 'Login', 'wp-coupon' ); ?></a>
                    <?php if ( $is_logged_in ) { ?>
                    <ul class="sub-menu">
                        <li><a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Dashboard', 'wp-coupon' ); ?></a></li>
                        <li><a href="<?php echo WP_Users()->get_edit_profile_link( $user ); ?>"><?php esc_html_e( 'Account Settings', 'wp-coupon' ); ?></a></li>
                        <li><a href="<?php echo wp_logout_url(); ?>"><?php esc_html_e( 'Sign Out', 'wp-coupon' ); ?></a></li>
                    </ul>
                    <?php } ?>
                </li>

            </ul>
            <?php
        }
    }

    /**
     *
     * Display public profile
     *
     * @param $user
     * @param $current_user
     */
    private static function public_profile( $user , $current_user ){
        ?>
        <div class="wpu-form-profile wpu-form form ui clear-fix"  >

            <div class="stuser-form-fields viewing-info">
                <p class="fieldset stuser_input st-username">
                    <label class=""><?php esc_html_e( 'User Name:', 'wp-coupon' ); ?></label>
                    <span>
                        <?php echo esc_html( $user->user_login ); ?>
                    </span>
                </p>
                <?php if ( WP_Users()->is_current_user( $user, $current_user ) ){ ?>
                    <p class="fieldset stuser_input st-email">
                        <label class=""><?php esc_html_e( 'E-mail:', 'wp-coupon' ); ?></label>
                        <span>
                            <?php echo esc_html( $user->user_email ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  get_user_meta( $user->ID, 'first_name', true ) != '' ){ ?>
                    <p class="fieldset stuser_input st-firstname">
                        <label class=""><?php esc_html_e( 'First Name:', 'wp-coupon' ); ?></label>
                        <span class="">
                            <?php
                            echo esc_html( get_user_meta( $user->ID, 'first_name', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  get_user_meta( $user->ID, 'last_name', true ) != '' ){ ?>
                    <p class="fieldset stuser_input st-lastname">
                        <label class=""><?php esc_html_e( 'Last Name:', 'wp-coupon' ); ?></label>
                        <span class="">
                            <?php echo  esc_html( get_user_meta( $user->ID, 'last_name', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  $user->display_name!= '' ){ ?>
                    <p class="fieldset stuser_input">
                        <label class=""><?php esc_html_e( 'Display Name:', 'wp-coupon' ); ?></label>
                        <span><?php  echo esc_html( $user->display_name );  ?></span>
                    </p>
                <?php } ?>

                <?php if ( $user->user_url  != '' ){ ?>
                    <p class="fieldset stuser_input st-website">
                        <label class="" for="signin-password"><?php esc_html_e( 'Website:', 'wp-coupon' ); ?></label>
                        <span class="">
                            <?php echo esc_html( $user->user_url ); ?>
                        </span>
                    </p>
                <?php } ?>
                <?php if (  get_user_meta( $user->ID, 'description', true ) != '' ){ ?>
                    <p class="fieldset stuser_input">
                        <label class=""><?php esc_html_e( 'Bio:', 'wp-coupon' ); ?></label>
                        <span>
                            <?php echo  esc_html( get_user_meta( $user->ID, 'description', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

            </div>
        </div>
        <?php
    }

    /**
     * Get save coupons
     *
     * @param int $number_per_page
     * @param string $oder_by best|recent
     * @return array
     */
    public static  function get_save_coupons( $number_per_page = 2, $oder_by = 'best'  ){
        global $wp_query;
        $args = array(
            'post_type'      => 'coupon',
            'posts_per_page' => $number_per_page,
        );

        $user = wp_get_current_user();
        $coupons_in  = get_user_meta( $user->ID, '_wpc_saved_coupons' , true );
        $coupons_in  = explode( ',', $coupons_in );

        $args[ 'orderby' ]   = 'post__in';
        $args['post__in'] = array_reverse( $coupons_in );

        $wp_query = new WP_Query( $args );
        return $wp_query->get_posts();
    }

    public static function best_coupons_from_stores( $number_per_page = 2, $paged = 1 ){
       // $user = wp_get_current_user();
        //$stores = get_user_meta( $user->ID, '_wpc_favorite_stores' , true );
        $stores = get_user_meta( get_current_user_id(), '_wpc_favorite_stores' , true );
        $stores  = explode( ',',$stores );
        $stores = array_map( 'absint', $stores );

        global $wp_query;
        $args = array(
            'post_type'      => 'coupon',
            'paged'          => $paged,
            'posts_per_page' => $number_per_page,
        );

        $current_time = current_time( 'timestamp' );

        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'coupon_store',
                'field'    => 'term_id',
                'terms'    => $stores,
                'operator' => 'IN',
            ),
        );

        $args['meta_query'] = array(
            array(
                'relation' => 'OR',
                array(
                    'key' => '_wpc_expires',
                    'value' => $current_time,
                    'type'    => 'numeric',
                    'compare' => '>',
                ),
                array(
                    'key' => '_wpc_expires',
                    'value' => '',
                    'compare' => '=',
                ),
            ),

        );

        $args[ 'meta_key' ]         = '_wpc_percent_success';
        $args[ 'orderby' ]          = 'meta_value_num';
        $args[ 'meta_value_num' ]   = 0;
        $args[ 'meta_compare' ]     = '>=';
        $args[ 'order' ]            = 'DESC';

        $wp_query = new WP_Query( $args );
        return $wp_query->get_posts();
    }

    public static function get_stores( $number_per_page =  8 ){
        $stores = get_user_meta( get_current_user_id(), '_wpc_favorite_stores' , true );
        if ( is_string( $stores ) && trim( $stores ) != '' ) {
           $stores =  explode( ',', $stores );
        }
        if ( ! is_array( $stores ) || empty ( $stores ) ) {
            return array();
        }

        if ( $number_per_page < 0 ) {
            $number_per_page =  '';
        }

        $args = array(
            'orderby'                => 'include',
            // 'order'                  => 'DESC',
            'hide_empty'             => false,
            'include'                => array_map( 'intval', $stores ),
            'exclude'                => array(),
            'exclude_tree'           => array(),
            'number'                 => $number_per_page,
            'hierarchical'           => false,
            'pad_counts'             => false,
            'child_of'               => 0,
            'childless'              => false,
            'cache_domain'           => 'core',
            'update_term_meta_cache' => true,
        );

        return get_terms( 'coupon_store', $args );

    }

    private static function dashboard( $user ){

        $current_link = $_SERVER['REQUEST_URI'];

        $link =  WP_Users()->get_profile_link( $user );
        $coupons =  self::best_coupons_from_stores( apply_filters( 'wp_coupon_dashboard_number_best_coupons', 4 ) );
        ?>
        <div class="wpu-form-profile wpu-form clear-fix" >
            <div class="best-coupons">
                <h2 class="section-heading"><?php esc_html_e( 'Best coupons from your Favorite Stores', 'wp-coupon' ); ?>
                    <?php if ( $coupons ) { ?>
                    <a href="<?php echo add_query_arg( array( 'wpu_action' =>'favorites_stores', 'view'=>'best_coupons' ), $link ); ?>" class="right mini ui button"><?php esc_html_e( 'View all', 'wp-coupon' ); ?></a>
                    <?php } ?>
                </h2>
                <?php
                if ( count( $coupons ) ) {
                    foreach ( $coupons as $post ) {
                        wpcoupon_setup_coupon( $post , $current_link );
                        get_template_part( 'loop/loop-coupon', 'cat' );
                    }
                } else {
                    ?>
                    <div class="ui warning message">
                        <p><?php esc_html_e( 'No Coupons found! Please add more favorite stores to see this.', 'wp-coupon' ); ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="saved-coupons">
                <?php
                $coupons = self::get_save_coupons( apply_filters( 'wp_coupon_dashboard_number_recent_coupons', 4 ) );
                ?>
                <h2 class="section-heading"><?php esc_html_e( 'Recent Saved Coupons', 'wp-coupon' ); ?>
                    <?php if ( $coupons ) { ?>
                        <a href="<?php echo add_query_arg( array( 'wpu_action' =>'saved_coupons' ), $link ); ?>" class="right mini ui button"><?php esc_html_e( 'View all', 'wp-coupon' ); ?></a>
                    <?php } ?>
                </h2>
                <?php
                if ( $coupons ) {
                    if ( $coupons ) {
                        foreach ( $coupons as $post ) {
                            wpcoupon_setup_coupon( $post, $current_link );
                            get_template_part( 'loop/loop-coupon', 'cat' );
                        }
                    }
                } else {
                    ?>
                    <div class="ui warning message">
                        <p><?php esc_html_e( 'No Coupons found! Please add more see this.', 'wp-coupon' ); ?></p>
                    </div>
                    <?php
                }

                ?>
            </div>

            <div class="saved-stores">
                <?php
                $stores = self::get_stores( apply_filters( 'wp_coupon_dashboard_number_recent_stores', 8 ) );
                ?>
                <h2 class="section-heading"><?php esc_html_e( 'Recent Favorite Stores', 'wp-coupon' ); ?>
                    <?php if ( $stores ) { ?>
                    <a href="<?php echo add_query_arg( array( 'wpu_action' =>'favorites_stores' ), $link ); ?>" class="right mini ui button"><?php esc_html_e( 'View all', 'wp-coupon' ); ?></a>
                    <?php } ?>
                </h2>
                <?php
                if ( $stores ) {
                ?>
                <div class="store-letter-content fav-stores-box shadow-box">
                    <div class="ui four column grid stackable">
                        <?php
                        foreach ( $stores as $store ) {
                            wpcoupon_setup_store( $store );
                            $url = wpcoupon_store()->get_home_url( false );
                            $name = wpcoupon_store()->get_display_name();
                            if ( $url != '' && ! empty( wpcoupon_store()->_wpc_store_url ) ) {
                                $parse = parse_url( $url );
                                if( $parse && isset( $parse['host'] ) ) {
                                    $name = str_replace( 'www.', '', strtolower( $parse['host'] ) );
                                }
                            }
                            ?>
                            <div class="column ">
                                <div class="text-center">
                                <div class="store-thumb">
                                    <a href="<?php echo wpcoupon_store()->get_url(); ?>" class="ui image middle aligned">
                                        <?php echo wpcoupon_store()->get_thumbnail(); ?>
                                    </a>
                                </div>
                                <a href="#" data-id="<?php echo wpcoupon_store()->term_id; ?>" class="add-favorite icon-popup added"><i class="heart icon"></i></a>
                                <a href="<?php echo wpcoupon_store()->get_url(); ?>">
                                    <span><?php echo esc_html( $name ); ?></span>
                                </a>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
                <?php
                } else {
                    ?>
                    <div class="ui warning message">
                        <p><?php esc_html_e( 'No Stores found! Please add more see this.', 'wp-coupon' ); ?></p>
                    </div>
                    <?php
                }

                ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    }// end dashboard

    private static function saved_coupons(){
        $coupons = self::get_save_coupons( -1 );
        $current_link = $_SERVER['REQUEST_URI'];
        ?>
        <div class="wpu-form-profile wpu-form your-saved-coupons clear-fix" >
            <div class="saved-coupons">
                <h2 class="section-heading"><?php esc_html_e( 'Your Saved Coupons', 'wp-coupon' ); ?></h2>
                <?php
                if ( $coupons ) {

                    if ( $coupons ) {
                        foreach ( $coupons as $post ) {
                            wpcoupon_setup_coupon( $post, $current_link );
                            get_template_part( 'loop/loop-coupon', 'cat' );
                        }
                    }
                } else {
                    ?>
                    <div class="ui warning message">
                        <p><?php esc_html_e( 'No saved coupons found ! Please add more.', 'wp-coupon' ); ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    }// end saved_coupons

    private static function favorites_stores(){
        global $wp_query;
        $view =  isset( $_REQUEST['view'] ) ?  strtolower( ( string ) $_REQUEST['view'] ) : '';
        $current_link = $_SERVER['REQUEST_URI'];

        ?>
        <div class="wpu-form-profile wpu-form best-store-coupons clear-fix" >
            <?php
            if ( 'best_coupons' ==  $view ) {
                $paged =  isset( $wp_query->query_vars['st_paged'] ) ?  intval( $wp_query->query_vars['st_paged'] ) : 1;
                $coupons =  self::best_coupons_from_stores( 5, $paged );
                ?>
                <div class="best-coupons">
                    <h2 class="section-heading"><?php esc_html_e('Best coupons from your Favorite Stores', 'wp-coupon'); ?></h2>
                    <?php
                    if ( $coupons ) {
                        if ($coupons) {
                            foreach ($coupons as $post) {
                                wpcoupon_setup_coupon($post, $current_link);
                                get_template_part('loop/loop-coupon', 'cat');
                            }
                        }
                        get_template_part('content', 'paging');
                    } else {
                        ?>
                        <div class="ui success message">
                            <p><?php esc_html_e( 'No coupons found ! Please add more favorite store to see this.', 'wp-coupon' ); ?></p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <div class="saved-stores">
                    <?php
                    $stores = self::get_stores( -1 );
                    ?>
                    <h2 class="section-heading"><?php esc_html_e( 'Favorite Stores', 'wp-coupon' ); ?></h2>
                    <?php
                    if ( $stores ) {
                        ?>

                        <div class="store-letter-content fav-stores-box shadow-box">
                            <div class="ui four column grid stackable">
                                <?php
                                foreach ( $stores as $store ) {
                                    wpcoupon_setup_store( $store );

                                    $url = wpcoupon_store()->get_home_url( false );
                                    $name = wpcoupon_store()->get_display_name();
                                    if ( $url != '' && ! empty( wpcoupon_store()->_wpc_store_url ) ) {
                                        $parse = parse_url( $url );
                                        if( $parse && isset( $parse['host'] ) ) {
                                            $name = str_replace( 'www.', '', strtolower( $parse['host'] ) );
                                        }
                                    }
                                    ?>
                                    <div class="column ">
                                        <div class="text-center">
                                            <div class="store-thumb">
                                                <a href="<?php echo wpcoupon_store()->get_url(); ?>" class="ui image middle aligned">
                                                    <?php echo wpcoupon_store()->get_thumbnail(); ?>
                                                </a>
                                            </div>
                                            <a href="#" data-id="<?php echo wpcoupon_store()->term_id; ?>"  class="add-favorite icon-popup added"><i class="heart icon"></i></a>
                                            <a href="<?php echo wpcoupon_store()->get_url(); ?>">
                                                <span><?php echo esc_html( $name ); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="ui warning message">
                            <p><?php esc_html_e( 'No saved stores found ! Please add more.', 'wp-coupon' ); ?></p>
                        </div>
                        <?php
                    }

                ?>
                </div>
                <?php

            }// end if view
            ?>
        </div>
        <?php

        wp_reset_postdata();

    }// end favorites_stores

    /**
     * Display profile content
     *
     * @param $user
     * @param $current_user
     * @param bool|false $action
     */
    public static function profile_content( $user, $current_user,  $action =  false ){
        $is_edit =  false;
        $is_current_user =  WP_Users()->is_current_user( $user, $current_user );
        if ( 'edit' == $action && $is_current_user ) {
            $is_edit =  true;
        }
        if ( ! $is_edit && $action == 'edit' ) {
            $action = '';
        }

        if ( $is_current_user ) {

            switch( $action ) {
                case 'favorites_stores':
                    self::favorites_stores( $user );
                    break;
                case 'saved_coupons':
                    self::saved_coupons( $user );
                    break;
                case 'edit':
                    WP_Users_Public::settings( $user );
                    break;
                default :
                    self::dashboard( $user );
                    break;
            }

        } else {
            self::public_profile( $user, $current_user );

        }

    }
}


$GLOBALS['WPCoupon_User'] =  new WPCoupon_User();

function WPCoupon_User(){
    if ( ! isset( $GLOBALS['WPCoupon_User'] ) ) {
        $GLOBALS['WPCoupon_User'] =  new WPCoupon_User();
    }
    return $GLOBALS['WPCoupon_User'];
}
