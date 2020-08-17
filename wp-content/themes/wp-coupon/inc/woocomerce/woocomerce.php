<?php
/**
 * Remove default WC breadcrumb
 *
 * @see woocommerce_content
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_filter( 'woocommerce_show_page_title', '__return_false' );



/**
 * Change number products to show
 *
 * @return int
 */
function wpcoupon_wc_change_number_products(){
    $shop_id = wc_get_page_id( 'shop' );
    $number = absint( get_post_meta( $shop_id,  '_wpc_shop_number_products', true ) );
    if ( ! $number ) {
        $number = 12;
    }
    return $number;
}
add_filter( 'loop_shop_per_page', 'wpcoupon_wc_change_number_products', 20 );


if (! function_exists('loop_columns')) {
    /**
     * Change shop layout column
     *
     * @return int
     */
    function loop_columns() {
        return 3; // 3 products per row
    }
}
add_filter('loop_shop_columns', 'loop_columns');




/**
 * Output the WooCommerce Breadcrumb to matches theme style
 *
 * @see woocommerce_breadcrumb
 *
 * @param array $args
 */
function wpcoupon_get_wc_breadcrumb( $args = array() ) {
    $args = wp_parse_args( $args, apply_filters( 'woocommerce_breadcrumb_defaults', array(
        'delimiter'   => ' &gt; ',
        'wrap_before' => '<div class="ui breadcrumb breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">',
        'wrap_after'  => '</div>',
        'before'      => '<span>',
        'after'       => '</span>',
        'home'        => _x( 'Home', 'breadcrumb', 'wp-coupon' )
    ) ) );

    $breadcrumbs = new WC_Breadcrumb();

    if ( $args['home'] ) {
        $breadcrumbs->add_crumb( $args['home'], apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) );
        $breadcrumbs->add_crumb( _x( 'Shop', 'breadcrumb', 'wp-coupon' ), get_permalink( wc_get_page_id( 'shop' ) ) );
    }

    $args['breadcrumb'] = $breadcrumbs->generate();

    extract( $args );
    $html = '';

    if ( ! empty( $breadcrumb ) ) {

        $html .= $wrap_before;

        foreach ( $breadcrumb as $key => $crumb ) {

            $html .= $before;

            if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
                $html .= '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
            } else {
                $html .= esc_html( $crumb[0] );
            }

            $html .= $after;

            if ( sizeof( $breadcrumb ) !== $key + 1 ) {
                $html .= $delimiter;
            }

        }

        $html .= $wrap_after;

    }

    return $html;

}

if ( ! function_exists( 'woocommerce_pagination' ) ) {
    /**
     * Change WC Pagination to matches theme style
     */
    function woocommerce_pagination()
    {
        global $wp_query;

        if ($wp_query->max_num_pages <= 1) {
            return;
        }
        $args = apply_filters('woocommerce_pagination_args', array(
            'base' => esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))),
            'format' => '',
            'add_args' => false,
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,

            'end_size' => 3,
            'mid_size' => 3,
            
            'show_all' => false,
            'prev_next' => false,
            'type' => 'array',
            'add_fragment' => '',
            'before_page_number' => '',
            'after_page_number' => ''

        ));

        $links = wpcoupon_paginate_links($args);

        ?>
        <div class="ui pagination menu woocommerce-pagination">
            <?php foreach ($links as $link) { ?>
                <?php echo wp_kses_post($link); ?>
            <?php } ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'woocommerce_product_loop_start' ) ) {

    /**
     * Output the start of a product loop. By default this is a UL.
     *
     * @param bool $echo
     * @return string
     */
    function woocommerce_product_loop_start( $echo = true ) {

        $shop_id = wc_get_page_id( 'shop' );
        $columns = absint( get_post_meta( $shop_id,  '_wpc_shop_number_products_per_row', true ) );
        if ( ! $columns ) {
            $columns = 3;
        }

        if( is_cart() || is_checkout() ) {
            $columns = 2;
        }

        $columns = apply_filters( 'shop_number_products_per_row', $columns );

        $classes = 'ui '.wpcoupon_number_to_column_class( $columns ).' column grid products stackable';

        if( is_shop()  || is_product_taxonomy() || is_product_category() || is_product_category() ) {
            if ( get_post_meta( $shop_id, '_wpc_shadow_box' , true ) == 'yes' ) {
                $classes .=' shadow-box content-box ';
            }
        }

        if ( $echo )
            echo  '<div class="'.esc_attr( $classes ).'">';
        else
            return '<div class="'.esc_attr( $classes ).'">';
    }
}


if ( ! function_exists( 'woocommerce_product_loop_end' ) ) {

    /**
     * Output the end of a product loop. By default this is a UL.
     *
     * @param bool $echo
     * @return string
     */
    function woocommerce_product_loop_end( $echo = true ) {
        ob_start();

        if ( $echo )
            echo '</div>';
        else
            return '</div>';
    }
}