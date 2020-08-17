<?php

/**
 * Class WPCoupon_Socials
 *
 * Socials class
 *
 */
class WPCoupon_Socials {

    /**
     * Display facebook share button
     *
     * @since 1.0.0
     * @param array $args
     * @return string
     */
    public static function facebook_share ( $args = array() ) {

        $args =  wp_parse_args( $args, array(
            'title'     => '',
            'url'       => '',
            'summary'   => '',
            'image'     => '',
            'class'     => 'tiny ui facebook button',
            'icon'      => '<i class="facebook icon"></i>',
            'label'     => esc_html__( 'Facebook', 'wp-coupon' )
        ) );

        extract( $args );

        $title   = urlencode( esc_js( $title ) );
        $url     = urlencode( esc_js( $url ) );
        $summary = urlencode( esc_js( $summary ) );
        $image   = urlencode( esc_js( $image ) );

        // <a class="tiny ui facebook button"><i class="facebook icon"></i>Facebook</a>

        return  "<a class='{$class}' onClick=\"window.open('https://www.facebook.com/sharer/sharer.php?u={$url}','sharer','toolbar=0,status=0,width=548,height=325'); return false;\" href=\"javascript: void(0)\">{$icon} {$label}</a>";
    }

    /**
     * Display Twitter button
     *
     * @since 1.0.0
     * @param $args
     * @return string
     */
    public static function twitter_share( $args ){
        $args =  wp_parse_args( $args, array(
            'title'     => '',
            'url'       => '',
            //'summary'   => '',
            'image'     => '',
            'class'     => 'tiny ui twitter button',
            'icon'      => '<i class="twitter icon"></i>',
            'label'     => esc_html__( 'Twitter', 'wp-coupon' ),
            'via'       => ''
        ) );

        extract( $args );

        $t = $title.' '.$url;
        $t = urlencode( esc_js( $t ) );
        return "<a class='{$class}' target=\"_blank\" onClick=\"window.open('https://twitter.com/intent/tweet?text={$t}','sharer','toolbar=0,status=0,width=548,height=325'); return false;\" href=\"javascript: void(0)\">{$icon} {$label}</a>";

    }

    function google_plus_share( ){
        return '<a class="tiny ui google plus button"><i class="google plus icon"></i>Google Plus</a>';
    }

}

// add_action( 'wpseo_head', array( $this, 'opengraph' ), 30 ); // YOAT SEO OG
// add_action('su_head', array(&$this, 'head_tag_output')); /. Utilmate seo plugin OG


/*

$options = WPSEO_Options::get_option( 'wpseo_social' );
	if ( $options['twitter'] === true ) {
		add_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );
	}

	if ( $options['opengraph'] === true ) {
		$GLOBALS['wpseo_og'] = new WPSEO_OpenGraph;
	}
 */

function wp_coupon_remove_YOAT_SEO_twitter_og(){
    if ( is_tax('coupon_store') || is_single( 'coupon' ) ) {
        remove_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );
    }
}



add_action( 'wp_enqueue_scripts', 'wp_coupon_remove_YOAT_SEO_twitter_og' );

function wp_coupon_remove_YOAT_SEO_og(){
    if ( is_tax('coupon_store') || is_single( 'coupon' ) ) {
        remove_all_actions('wpseo_opengraph');
    }
}



/**
 *  http://wordpress.stackexchange.com/questions/36013/remove-action-or-remove-filter-with-external-classes
 */
function wp_coupon_remove_seo_ultimate_og(){
    if ( is_tax('coupon_store') || is_tax( 'coupon_category' ) ) {
        wp_coupon_remove_class_action('su_head', 'SU_OpenGraph', 'head_tag_output');

        add_filter( 'wpseo_canonical', '__return_false' );
    }
}
add_action( 'wp', 'wp_coupon_remove_seo_ultimate_og' );

/**
 * @see redirect_canonical
 */

function wp_coupon_stop_redirect_canonical(){

    remove_filter('template_redirect', 'redirect_canonical');
    add_action( 'redirect_canonical', '__return_false' );

}
//add_action( 'wp', 'wp_coupon_stop_redirect_canonical' );

add_filter( 'get_canonical_url', 'wp_coupon_open_graph_canonical_url' );
function wp_coupon_open_graph_canonical_url( $canonical_url ){
    if ( is_tax('coupon_store') ) {
        if ( get_query_var( 'coupon_id' ) > 0 ) {
            $post = get_post( get_query_var( 'coupon_id' ) );
            if ( $post->post_type == 'coupon' ) {
                return wpcoupon_coupon()->get_href();
            }
        }
    }
    return $canonical_url;
}



function wp_coupon_open_graph(){
    $data = array();
    if ( is_tax( 'coupon_store' ) ) {
        $term = get_queried_object();
        wpcoupon_setup_store( $term  );
        $data['og:title'] = wp_strip_all_tags( wpcoupon_store()->get_single_store_name(), true );
        $data['og:description'] = wp_trim_words( wpcoupon_store()->get_content( false ), 30, '...' );
        $image = wpcoupon_store()->get_thumbnail( 'full', true );
        if ( $image ) {
            $data['og:image'] = $image;
        }
        $data['og:type'] = 'article';
        $url = wpcoupon_store()->get_url();
        if ( get_query_var( 'coupon_id' ) > 0 ) {
            $post = get_post( get_query_var( 'coupon_id' ) );
            if ( $post->post_type == 'coupon' ) {
                wpcoupon_coupon( $post  );
                $url = wpcoupon_coupon()->get_href();
                $data['og:type'] = 'article';
                $data['og:url'] = $url;
                $data['og:title'] = $post->post_title;
                $data['og:description'] = get_the_excerpt( $post );
                $image = wpcoupon_coupon()->get_thumb('full',true, true );
                if ( $image ) {
                    $data['og:image'] = $image;
                }
                wp_reset_postdata();
            }
        }
        $data['og:url'] = $url;
    } else if ( is_singular('coupon') ) {
        global $post;
        wp_reset_query();
        wpcoupon_coupon( $post  );
        $url = wpcoupon_coupon()->get_href();
        $data['og:url'] = $url;
        $data['og:type'] = 'article';
        $data['og:title'] = $post->post_title;
        $data['og:description'] = get_the_excerpt( $post );
        $image = wpcoupon_coupon()->get_thumb('full',true, true );
        if ( $image ) {
            $data['og:image'] = $image;
        }
        wp_reset_postdata();
    }

    foreach ( $data as $k => $v ) {
        echo '<meta property="'.esc_attr( $k ).'" content="'.esc_attr( $v ).'" />'."\n";
    }

    $twitter_keys = array(
        'twitter:title' => 'og:title',
        'twitter:url' => 'og:url',
        'twitter:description' => 'og:description',
        'twitter:image' => 'og:image',
    );
    if ( ! empty ( $data ) ) {
        if ( $data['og:image'] ) {
            echo "\n<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
        }
        foreach ( $twitter_keys as $k => $id ) {
            if ( isset ( $data[ $id ] ) ) {
                echo "<meta name=\"{$k}\" content=\"".esc_attr( $data[ $id ] )."\" />\n";
            }
        }
    }

    /*
    if (
        strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit/") !== false ||
        strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false
    ) {
        // it is probably Facebook's bot
        die();
    }
    else {
        // that is not Facebook
    }
    */

}

add_action( 'wp_head', 'wp_coupon_open_graph', 3 );
