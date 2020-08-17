<?php

/**
 * Plugin Name:       Coupon WP Mega Menu Addon
 * Plugin URI:        http://famethemes.com/plugins/coupon-wp-frontend-submit-addon/
 * Description:       Add Mega Menu for Coupon WP theme.
 * Version:           1.0.3
 * Author:            famethemes
 * Author URI:        http://famethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-coupon-mm
 * Domain Path:       /languages
 */


class WPCoupon_MegaMenu {
    function __construct(){

        define( 'WPCOUPON_MM_URL', trailingslashit( plugins_url('', __FILE__) ) );
        define( 'WPCOUPON_MM_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'front_end_scripts' ), 85 );

        if ( is_admin() ){
            include dirname(__FILE__).'/admin.php';
        } else {
            /**
             * @see Walker_Nav_Menu
             */
            add_filter( 'walker_nav_menu_start_el',array( $this, 'menu_mega' ), 65, 4 );
            add_filter( 'nav_menu_css_class', array( $this, 'menu_classes' ), 65, 4 );
            //add_filter( 'nav_menu_link_attributes', array( $this, 'menu_atts' ), 65, 4 );
        }

    }


    function front_end_scripts(){
        wp_enqueue_style( 'wpcoupon-mega', WPCOUPON_MM_URL.'css/mega.css' );
    }

    function get_menu_settings( $menu_id ){
        $default =  array(
            'enable' => '',
            'style' => '',
            'columns' => 4,
            'cwidth' => '',
            'content' => '',
            'mega_items' => '',
        ) ;
        $settings = get_post_meta( $menu_id, '_mm_settings', true );
        $settings = wp_parse_args( $settings, $default );
        if ( is_string( $settings['mega_items'] ) && $settings['mega_items'] != '' ){
            $settings['mega_items'] = json_decode( $settings['mega_items'] , true );
        } else {
            $settings['mega_items'] = array();
        }

        return $settings;
    }

    function render_mega_content( $item, $settings ){
        $w = '';
        if ( $settings['cwidth'] != '' ) {
            $w = 'mm-col-'.$settings['cwidth'];
        }
        if ( ! empty( $settings['mega_items'] ) ) {
            ob_start();
            echo '<div class="mm-inner container list-categories ">';
                echo '<ul class="mm-lists '.esc_attr( $w ).'" data-col="'.esc_attr(  $settings['columns'] ).'">';
                foreach( $settings['mega_items'] as $index => $term ) {
                    $term = get_term( $term['term_id'], $term['taxonomy'] );
                    if ( $term ){
                        $c ='';
                        if ( $settings['style'] == 'number' ) {
                            $c = '<span class="coupon-count">'.$term->count.'</span>';
                        } else if ( $settings['style'] == 'thumb' ) {
                            $thumb = false;
                            if ( $term->taxonomy == 'coupon_store' ) {

                                $image_id = get_term_meta($term->term_id, '_wpc_store_image_id', true);

                                if ($image_id > 0) {
                                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                                    if ($image) {
                                        $thumb = '<span class="img-thumb"><img class="ui avatar image" src="' . esc_attr($image[0]) . '" alt=" "></span>';
                                    }
                                }

                            } else {
                                $image_id = get_term_meta($term->term_id, '_wpc_cat_image_id', true);
                                if ($image_id > 0) {
                                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                                    if ($image) {
                                        $thumb = '<span class="img-thumb"><img class="ui avatar image" src="' . esc_attr($image[0]) . '" alt=" "></span>';
                                    }
                                }

                                if ( !$thumb ) {
                                    $icon = get_term_meta($term->term_id, '_wpc_icon', true);
                                    if (trim($icon) !== '') {
                                        $thumb = '<span class="img-icon"><i class="circular ' . esc_attr($icon) . '"></i></span>';
                                    }
                                }
                            }

                            if ( $thumb ) {
                                $c .= $thumb;
                            }
                        }



                        echo '<li><a href="'.get_term_link( $term ).'">'.$c.esc_html( $term->name ).'</a> </li>';
                    }
                }
                echo '</ul>';

            echo '</div>';

            $content = ob_get_clean();
        } else {
            $content = '';
        }

        if ( trim( $settings['content'] ) ) {
            $content .= '<div class="'.esc_attr( $w ).' mm-content">';
                $content .= '<div class="mm-inner container">';
                $content .= force_balance_tags( $settings['content'] );
                $content .= '</div>';
            $content .= '</div>';
        }

        if ( trim( $content ) ) {
            $content = '<div class="mm-item-content" id="mm-item-id-'.esc_attr( $item->ID ) .'">'.$content.'</div>';
        }
        return apply_filters( 'mm_render_mega_item_content', $content );

    }

    function menu_mega( $item_output, $item, $depth, $args  ){
        if ( $depth == 0 ) {
            $settings =  $this->get_menu_settings( $item->ID );
            if ( $settings['enable'] == 1 ){
                $item_output .=  $this->render_mega_content( $item, $settings );
            }
        }
        return $item_output;
    }

    function menu_classes( $classes, $item, $args, $depth  ){
        if ( $depth  == 0 ) {
            $settings = $this->get_menu_settings($item->ID);
            if ($settings['enable'] == 1) {
                $classes[] = 'mm-enable';
                $classes[] = 'menu-item-has-children';
            }
        }
        return $classes;
    }

}

add_action( 'plugins_loaded', 'wpcoupon_mm_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wpcoupon_mm_load_textdomain() {
    load_plugin_textdomain( 'wp-coupon-mm', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

new WPCoupon_MegaMenu();


