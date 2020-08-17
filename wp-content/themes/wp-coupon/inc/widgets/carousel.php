<?php
/**
 * Add Taxonomy widget.
 */
class WPCoupon_Carousel_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_carousel', // Base ID
            esc_html__( 'WPCoupon Store Carousel', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display store width Carousel', 'wp-coupon' ),
                'classname' => 'widget_carousel widget_wpc_carousel'
            ), // Args
            array(
                'width' => 530
            )
        );
    }
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {


        $instance =  wp_parse_args( $instance, array(
            'title'               => '',
            'query'               =>  array(),
            'controls'            =>  array(),
            'featured_only'       => '',
        ) );

        $query =  wp_parse_args( $instance['query'], array(
            'include'        => '',
            'exclude'        => '',
            'order'          => 'ASC',
            'orderby'        => '',
            'number'         => 6,

        ) );

        if ( $query['include'] != '' ){
            $query['include'] = array_map( 'intval', explode(',', $query['include'] ) );
        } else {
            unset( $query['include'] );
        }

         if ( $query['exclude'] != '' ){
            $query['exclude'] = array_map( 'intval', explode(',', $query['exclude'] ) );
        } else {
            unset( $query['exclude'] );
        }

        if ( $instance['featured_only'] == 1 ) {
            $query['meta_key'] = '_wpc_is_featured';
            $query['meta_value'] = 'on';

            $query['meta_query'] = array(
                array(
                    'key'     => '_wpc_is_featured',
                    'value'   => 'on',
                    'compare' => '=',
                ),
            );

        }

        $instance['controls']  =  wp_parse_args(
            $instance['controls']
            , array(
                'navigation' => true ,
                'navigationText' => array(
                    '<img src="'.get_template_directory_uri().'/assets/images/arrow-left.png">',
                    '<img src="'.get_template_directory_uri().'/assets/images/arrow-right.png">'
                ),
                'slideSpeed' => 300,
                'paginationSpeed' => 300,
                'autoPlay' => true,
                'stopOnHover' => true,
                'items' => 5,
                'itemsDesktopSmall' => 5,
                'itemsMobile' => 2,
            )
        );

        foreach(  $instance['controls'] as $k => $v ){
            if ( is_numeric( $v ) ) {
                 $instance['controls'][ $k ] =  intval( $v );
            } else if ( $v == 'true' ){
                $instance['controls'][ $k ] = true;
            } else if ( $v == 'false' ){
                $instance['controls'][ $k ] = false;
            }
        }

        $url = get_template_directory_uri().'/inc/widgets/';
        wp_enqueue_script( 'owl.carousel', $url.'assets/js/owl.carousel.js', array(), false, true );
        wp_enqueue_script( 'st-carousel', $url.'assets/js/carousel.js', array(), false, true );

        $terms = get_terms( 'coupon_store', $query );

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }

        ?>
        <div class="shadow-box popular-stores stores-thumbs">
            <div class="st-carousel store-carousel" data-settings="<?php echo esc_attr( json_encode( $instance['controls'] ) ); ?>">
                <?php if ( $terms  ) { ?>
                    <?php
                    foreach ( $terms as $term ) {
                        wpcoupon_setup_store( $term );
                        ?>
                        <div class="column">
                            <div class="store-thumb">
                                <a href="<?php echo  wpcoupon_store()->get_url(); ?>">
                                    <?php
                                        echo wpcoupon_store()->get_thumbnail( 'wpcoupon_small_thumb' );
                                    ?>
                                </a>
                            </div>
                            <div class="store-name">
                                <?php
                                $store_url =  wpcoupon_store()->get_home_url();
                                $term_url = wpcoupon_store()->get_url();
                                if ( $store_url && $term_url != $store_url ) {

                                    $data = parse_url( $store_url );
                                    if (is_array($data)) {
                                        $url = isset($data['host']) ? $data['host'] : '';
                                        if (!$url) {
                                            $url = isset($data['path']) ? $data['path'] : '';
                                        }

                                        if ($url != '') {
                                            $url = str_replace('www.', '', strtolower($url));
                                        }

                                    } else {
                                        $url = wpcoupon_store()->get_display_name();
                                    }
                                } else {
                                    $url =  $url = wpcoupon_store()->get_display_name();
                                }

                                ?>
                                <a href="<?php echo esc_url( $term_url ) ; ?>"><?php echo esc_html( $url ); ?></a>

                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>

        <?php

        wp_reset_postdata();

        echo $args['after_widget'];
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $instance =  wp_parse_args( $instance, array(
            'title'               => '',
            'query'               =>  array(),
            'controls'            =>  array(),
            'featured_only'       => '',

        ) );

        $query =  wp_parse_args( $instance['query'], array(
            'include'        => '',
            'exclude'        => '',
            'order'          => 'ASC',
            'orderby'        => 'DESC',
            'number'         => 6,
        ) );

        $controls = wp_parse_args( $instance['controls'], array(
                'slideSpeed' => 300,
                'paginationSpeed' => 300,
                'autoPlay' => "true",
                'stopOnHover' => "true",
                'items' => 5,
                'itemsDesktopSmall' => 5,
                'itemsMobile' => 2,
        ) );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <p>
            <input class="widefat" id="<?php echo $this->get_field_id( 'featured_only' ); ?>" name="<?php echo $this->get_field_name( 'featured_only' ); ?>" <?php checked( $instance['featured_only'], 1 ); ?> type="checkbox" value="1">

            <label for="<?php echo $this->get_field_id( 'featured_only' ); ?>"><?php esc_html_e( 'Featured stores only.', 'wp-coupon' ); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php esc_html_e( 'Include:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'query' ).'[include]'; ?>" type="text" value="<?php echo esc_attr( $query['include'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php esc_html_e( 'Exclude:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'query' ).'[exclude]'; ?>" type="text" value="<?php echo esc_attr( $query['exclude'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'How many stores to display ?', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'query' ).'[number]'; ?>" type="text" value="<?php echo esc_attr( $query['number'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'query' ).'[orderby]'; ?>">
                <?php
                $a = array(
                    'none'  => esc_html__( 'Default', 'wp-coupon' ),
                    'count'  => esc_html__( 'Number coupons', 'wp-coupon' ),
                    'name'  => esc_html__( 'Title', 'wp-coupon' ),
                    'id'  => esc_html__( 'Preserve Store ID order given in the inlcude IDs', 'wp-coupon' ),
                ) ;
                foreach ( $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $query['orderby'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'query' ).'[order]'; ?>">
                <?php
                $a = array(
                    'desc' => esc_html__( 'Desc', 'wp-coupon' ),
                    'asc'  => esc_html__( 'Asc', 'wp-coupon' ),
                );
                foreach (  $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $query['order'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>

        <hr>


        <p>
            <label for="<?php echo $this->get_field_id( 'slideSpeed' ); ?>"><?php esc_html_e( 'Slide speed (millisecond)', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'slideSpeed' ); ?>" name="<?php echo $this->get_field_name( 'controls' ).'[slideSpeed]'; ?>" type="text" value="<?php echo esc_attr( $controls['slideSpeed'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'paginationSpeed' ); ?>"><?php esc_html_e( 'Pagination speed (millisecond)', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'paginationSpeed' ); ?>" name="<?php echo $this->get_field_name( 'controls' ).'[paginationSpeed]'; ?>" type="text" value="<?php echo esc_attr( $controls['paginationSpeed'] ); ?>">
        </p>

        <p>
            <label><?php esc_html_e( 'Number items visible:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'controls' ).'[items]'; ?>">
                <?php
                for( $i = 1 ; $i <= 12; $i++ ) {
                    echo '<option value="'.$i.'" '.selected( $controls['items'], $i, false ).' >'.$i.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <label><?php esc_html_e( 'Number items visible on tablet:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'controls' ).'[itemsDesktopSmall]'; ?>">
                <?php
                for( $i = 1 ; $i <= 12; $i++ ) {
                    echo '<option value="'.$i.'" '.selected( $controls['itemsDesktopSmall'], $i, false ).' >'.$i.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <label><?php esc_html_e( 'Number items visible on mobile:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'controls' ).'[itemsMobile]'; ?>">
                <?php
                for( $i = 1 ; $i <= 12; $i++ ) {
                    echo '<option value="'.$i.'" '.selected( $controls['itemsMobile'], $i, false ).' >'.$i.'</option>';
                } ?>
            </select>
        </p>


         <p>
            <label ><?php esc_html_e( 'Auto play:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'controls' ).'[autoPlay]'; ?>">
                <?php
                $a = array(
                    'true' => esc_html__( 'Yes', 'wp-coupon' ),
                    'false'  => esc_html__( 'no', 'wp-coupon' ),
                );
                foreach (  $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $controls['autoPlay'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <label ><?php esc_html_e( 'Stop on hover:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'controls' ).'[stopOnHover]'; ?>">
                <?php
                $a = array(
                    'true' => esc_html__( 'Yes', 'wp-coupon' ),
                    'false'  => esc_html__( 'No', 'wp-coupon' ),
                );
                foreach (  $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $controls['stopOnHover'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>


        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        //$instance = array();
        $new_instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $new_instance;
        // return $instance;
    }

} // class Popular_Store

// register Foo_Widget widget
function wpcoupon_register_carousel_widget() {
    register_widget( 'WPCoupon_Carousel_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_carousel_widget' );
