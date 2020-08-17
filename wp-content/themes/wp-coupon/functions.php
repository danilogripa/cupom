<?php
/**
 * WP Coupon functions and definitions
 *
 * @package WP Coupon
 */

/**
 * Define theme constants
 */
$theme_data   = wp_get_theme();
if ( $theme_data->exists() ) {
	define( 'ST_THEME_NAME', $theme_data->get( 'Name' ) );
	define( 'ST_THEME_VERSION', $theme_data->get( 'Version' ) );
}

/**
 * Check if WooCommerce is active
 * @return bool
 */
function wpcoupon_is_wc(){
	return class_exists( 'WooCommerce' );
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 950; /* pixels */
}

//remove_filter('template_redirect', 'redirect_canonical');

if ( ! function_exists( 'wpcoupon_theme_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wpcoupon_theme_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on wp-coupon, use a find and replace
	 * to change 'wp-coupon' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'wp-coupon', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Use shortcodes in text widgets.
	add_filter( 'widget_text', 'do_shortcode' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'wpcoupon_small_thumb', 200, 115, false );
	add_image_size( 'wpcoupon_medium-thumb', 480, 480, false );
	add_image_size( 'wpcoupon_blog_medium', 620, 300, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'wp-coupon' ),
		'footer' => esc_html__( 'Footer', 'wp-coupon' ),
	) );

	// This theme styles the visual editor to resemble the theme style.
	//add_editor_style( 'assets/css/editor-style.css' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	//woocommerce support
	add_theme_support( 'woocommerce' );

}
endif; // wpcoupon_theme_setup
add_action( 'after_setup_theme', 'wpcoupon_theme_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function wpcoupon_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'wp-coupon' ),
		'id'            => 'sidebar',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'wp-coupon' ),
		'id'            => 'sidebar-2',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Coupon Category Sidebar', 'wp-coupon' ),
		'id'            => 'sidebar-coupon-category',
		'description'   => esc_html__( 'The sidebar will display on coupon category, tag page.', 'wp-coupon' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

    register_sidebar( array(
        'name'          => esc_html__( 'Store Sidebar', 'wp-coupon' ),
        'id'            => 'sidebar-store',
        //'description'   => esc_html__( 'The sidebar will display on single store, coupon category.', 'wp-coupon' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );

	register_sidebar( array(
		'name'          => esc_html__( 'WooCommerce Sidebar', 'wp-coupon' ),
		'id'            => 'sidebar-woo',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'wp-coupon' ),
		'id'            => 'footer-1',
		'description'   => wpcoupon_sidebar_desc( 'footer-1' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'wp-coupon' ),
		'id'            => 'footer-2',
		'description'   => wpcoupon_sidebar_desc( 'footer-2' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'wp-coupon' ),
		'id'            => 'footer-3',
		'description'   => wpcoupon_sidebar_desc( 'footer-3' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'wp-coupon' ),
		'id'            => 'footer-4',
		'description'   => wpcoupon_sidebar_desc( 'footer-4' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Frontpage sidebar
	register_sidebar( array(
		'name'          => esc_html__( 'Frontpage Before Main Content', 'wp-coupon' ),
		'id'            => 'frontpage-before-main',
		'description'   => esc_html__( 'This sidebar display on frontpage template', 'wp-coupon' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Frontpage Main Content', 'wp-coupon' ),
		'id'            => 'frontpage-main',
		'description'   => esc_html__( 'This sidebar display on frontpage template', 'wp-coupon' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Frontpage Main Sidebar', 'wp-coupon' ),
		'id'            => 'frontpage-sidebar',
		'description'   => esc_html__( 'This sidebar display on frontpage template', 'wp-coupon' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Frontpage After Main Content', 'wp-coupon' ),
		'id'            => 'frontpage-after-main',
		'description'   => esc_html__( 'This sidebar display on frontpage template', 'wp-coupon' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

}
add_action( 'widgets_init', 'wpcoupon_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function wpcoupon_theme_scripts() {

    $theme = wp_get_theme();
    $version =  $theme->get( 'Version' );
    $version = apply_filters( 'wpcoupon_script_version', $version );

	// Stylesheet
    wp_enqueue_style( 'wpcoupon_style', get_template_directory_uri().'/style.css', false, $version );
	wp_enqueue_style( 'wpcoupon_semantic', get_template_directory_uri() .'/assets/css/semantic.min.css', array(), '4.2.0' );

	if ( is_rtl() ){
		wp_enqueue_style( 'wpcoupon_rtl', get_template_directory_uri() .'/rtl.css', array(), $version );
	}

	// jQuery & Scripts
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'wpcoupon_libs', get_template_directory_uri() . '/assets/js/libs.js', array( 'jquery' ), $version, true );
	wp_enqueue_script( 'wpcoupon_semantic', get_template_directory_uri() . '/assets/js/libs/semantic.js', array( 'jquery'  ), $version, false );
	wp_enqueue_script( 'wpcoupon_global', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery', 'wpcoupon_semantic', 'wpcoupon_libs'  ), $version, true );

    $localize =  array(
        'ajax_url'        => admin_url( 'admin-ajax.php' ),
        'home_url'        => home_url('/'),
        'enable_single'   =>  wpcoupon_is_single_enable(),
        'auto_open_coupon_modal'   =>  wpcoupon_get_option( 'auto_open_coupon_modal' ) ?  1 : '',
        'vote_expires'    => apply_filters( 'st_coupon_vote_expires', 7 ), // 7 days
        '_wpnonce'        => wp_create_nonce(),
        '_coupon_nonce'   => wp_create_nonce('_coupon_nonce'),
        'user_logedin'    => is_user_logged_in(),
        'added_favorite'  => esc_html__( 'Favorited', 'wp-coupon' ),
        'add_favorite'    => esc_html__( 'Favorite This Store', 'wp-coupon' ),
        'login_warning'   => esc_html__( 'Please login to continue...', 'wp-coupon' ),
        'save_coupon'     => esc_html__( 'Save this coupon', 'wp-coupon' ),
        'saved_coupon'    => esc_html__( 'Coupon Saved', 'wp-coupon' ),
        'no_results'      => esc_html__( 'No results...', 'wp-coupon' ),
        'copied'          => esc_html__( 'Copied', 'wp-coupon' ),
        'copy'            => esc_html__( 'Copy', 'wp-coupon' ),
        'print_prev_tab'  => wpcoupon_get_option( 'print_prev_tab', false ) ?  1 : 0, // open store website in previous tab.
        'sale_prev_tab'   => wpcoupon_get_option( 'sale_prev_tab', true ) ?  1 : 0, // open store website in previous tab.
        'code_prev_tab'   => wpcoupon_get_option( 'code_prev_tab', true ) ?  1 : 0, // open store website in previous tab.
        'coupon_click_action'   => wpcoupon_get_option( 'coupon_click_action', 'prev' ),
        'share_id'        => 0, // open store website in previous tab.
        'header_sticky'   => wpcoupon_get_option( 'header_sticky', false ), // open store website in previous tab.
    ) ;
    $list = '';
    if ( is_user_logged_in() ){
        $user = wp_get_current_user();
        $list  = get_user_meta( $user->ID, '_wpc_saved_coupons' , true );
        $stores = get_user_meta( $user->ID, '_wpc_favorite_stores' , true );
        $localize['my_saved_coupons'] =  explode( ',', $list );
        $localize['my_favorite_stores'] =  explode( ',', $stores );
    } else {
        $localize['my_saved_coupons'] = array();
        $localize['my_favorite_stores'] = array();
    }

    if ( is_tax( 'coupon_store' ) ) {
        global $wp_rewrite;
        if ( $wp_rewrite->using_permalinks() ){
            $share_id = get_query_var( 'share_id' );
            $coupon_id = get_query_var( 'coupon_id' );
        } else {
            $share_id = absint( $_GET['share_id'] );
            $coupon_id = absint( $_GET['coupon_id'] );
        }
        $localize['share_id'] = $share_id;
        $localize['coupon_id'] = $coupon_id;
    }

    if(  $localize['enable_single'] ) {
        if ( is_singular( 'coupon' ) ) {
            global $post;
            $localize['current_coupon_id'] = $post->ID;
        }
    }

	$localize['my_saved_coupons'] = explode( ',', $list );
    wp_localize_script( 'wpcoupon_global', 'ST', apply_filters( 'wp_coupon_localize_script', $localize ) );

}
add_action( 'wp_enqueue_scripts', 'wpcoupon_theme_scripts' );


/**
 * Helper lib
 */
require_once get_template_directory() . '/inc/core/helper.php';

/**
 * Theme Options
 */
if ( class_exists( 'ReduxFramework' ) ) {
    require_once( get_template_directory() . '/inc/config/option-config.php' );
}

/**
 * Theme one click to import demo content
 * Check if plugin https://wordpress.org/plugins/one-click-demo-import/
 *
 * @see https://wordpress.org/plugins/one-click-demo-import/faq/
 */
if ( class_exists( 'PT_One_Click_Demo_Import' ) ) {
    require_once( get_template_directory() . '/inc/config/demo-config.php' );
}

// Retrieve theme option values
if ( ! function_exists('wpcoupon_get_option') ) {
	function wpcoupon_get_option($id, $fallback = false, $key = false ) {
		global $st_option;
		if ( ! $st_option ) {
			$st_option = get_option('st_options');
		}
        if ( ! is_array( $st_option ) ) {
            return $fallback;
        }
		if ( $fallback == false ) $fallback = '';
		$output = ( isset($st_option[$id]) && $st_option[$id] !== '' ) ? $st_option[$id] : $fallback;
		if ( !empty($st_option[$id]) && $key ) {
			$output = $st_option[$id][$key];
		}
		return $output;
	}
}


/**
 * Support coupon type
 * @return array
 */
function wpcoupon_get_coupon_types( $plural = false ){

    $deal = wpcoupon_get_option( 'use_deal_txt', false );

    if ( $plural ) {
        $types = array(
            'code'       => esc_html__( 'Codes', 'wp-coupon' ),
            'sale'       => esc_html__( 'Sales', 'wp-coupon' ),
            'print'      => esc_html__( 'Printable', 'wp-coupon' ),
        );
        if ( $deal ) {
            $types[ 'sale' ] = esc_html__( 'Deals', 'wp-coupon' );
        }
    } else {
        $types = array(
            'code'       => esc_html__( 'Code', 'wp-coupon' ),
            'sale'       => esc_html__( 'Sale', 'wp-coupon' ),
            'print'      => esc_html__( 'Printable', 'wp-coupon' ),
        );
        if ( $deal ) {
            $types[ 'sale' ] = esc_html__( 'Deal', 'wp-coupon' );
        }
    }
    return apply_filters( 'wpcoupon_get_coupon_types', $types, $plural );
}

/**
 * Recommend plugins via TGM activation class
 */
require_once get_template_directory() . '/inc/tgmpa/tgmpa-config.php';


/**
 * Post type
 */
require_once get_template_directory() . '/inc/post-type.php';


/**
 * Coupon functions.
 */
require_once get_template_directory() . '/inc/core/coupon.php';

/**
 * Coupon functions.
 */
require_once get_template_directory() . '/inc/core/store.php';


/**
 * Coupon functions.
 */
require_once get_template_directory() . '/inc/core/sharing.php';

/**
 * Search functions.
 */
require_once get_template_directory() . '/inc/core/search.php';


/**
 * Ajax handle
 */
require_once get_template_directory() . '/inc/core/ajax.php';

/**
 * Schedule event
 */
require_once get_template_directory() . '/inc/core/schedule-event.php';

/**
 * Auto update
 */
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/core/admin-update.php';
}



/**
 * Load user functions
 */
require_once get_template_directory() . '/inc/user/user.php';


/**
 *Theme Hooks
 */
require_once get_template_directory() . '/inc/core/hooks.php';

/**
 * Custom template tags for this theme.
 */
require_once get_template_directory() . '/inc/template-tags.php';

/**
 * Custom CSS, JS, .. code
 */
require_once get_template_directory() . '/inc/custom-code.php';


/**
 * Custom functions that act independently of the theme templates.
 */
require_once get_template_directory() . '/inc/extras.php';

/**
 * Load custom metaboxes config.
 */
require_once get_template_directory() . '/inc/config/metabox-config.php';

/**
 * The theme fully support WooCommerce, Awesome huh?.
 */
add_theme_support( 'woocommerce' );
require_once get_template_directory() . '/inc/config/woocommerce-config.php';


/**
 * Widgets
 */
require_once get_template_directory() . '/inc/widgets/_assets.php';
require_once get_template_directory() . '/inc/widgets/popular-stores.php';
require_once get_template_directory() . '/inc/widgets/categories.php';
require_once get_template_directory() . '/inc/widgets/newsletter.php';
require_once get_template_directory() . '/inc/widgets/carousel.php';
require_once get_template_directory() . '/inc/widgets/coupons.php';
require_once get_template_directory() . '/inc/widgets/sidebar.php';
require_once get_template_directory() . '/inc/widgets/headline.php';
require_once get_template_directory() . '/inc/widgets/slider.php';

if ( wpcoupon_is_wc() ) {
	/**
	 * WooCommerce Helpers
	 */
	require_once get_template_directory() . '/inc/woocomerce/woocomerce.php';
}

if ( defined( 'SITEORIGIN_PANELS_BASE_FILE' ) ) {
	/**
	 * Siteorigin Helpers
	 */
	require_once get_template_directory() . '/inc/siteorigin.php';
}
