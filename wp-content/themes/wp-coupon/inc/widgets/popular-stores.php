<?php

/**
 * Filter to allow terms by rand
 *   $orderby = apply_filters( 'get_terms_orderby', $orderby, $this->query_vars, $this->query_vars['taxonomy'] );
 * @param $orderby
 * @param $args
 * @return string
 */
function wpcoupon_order_terms_by_rand( $orderby, $args ){
    if ( isset( $args['orderby'] ) ) {
        if ( strtolower( $args['orderby'] ) == 'rand' ) {
            return " RAND() ";
        }
    }
    return $orderby;
}
add_filter( 'get_terms_orderby', 'wpcoupon_order_terms_by_rand' , 35, 2);

/**
* Add Popular Store widget.
*/
class WPCoupon_Popular_Store extends WP_Widget {
    /**
    * Register widget with WordPress.
    */
    function __construct() {
        parent::__construct(
            'popular_stores', // Base ID
            esc_html__( 'WPCoupon Stores', 'wp-coupon' ), // Name
            array( 'description' => esc_html__( 'Display Stores', 'wp-coupon' ), ) // Args
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

        $args = wp_parse_args( $args, array(
            'before_widget' => '',
            'after_widget' => '',
            'after_title' => '',
        ) );

        echo $args['before_widget'];

        $instance =  wp_parse_args( $instance, array(
            'title'         => '',
            'number'        => 6,
            'item_per_row'  => 2,
            'include' => '',
            'exclude' => '',
            'orderby' => 'count',
            'order' => 'desc',
        ) );

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }

        $instance['include'] = explode( ',', $instance['include'] );
        $instance['exclude'] = explode( ',', $instance['exclude'] );

        $instance['include'] = array_map( 'absint', $instance['include'] );
        $instance['include'] = array_filter( $instance['include'] );

        $instance['exclude'] = array_map( 'absint', $instance['exclude'] );
        $instance['exclude'] = array_filter( $instance['exclude'] );
        $tax_args = array(
            'orderby'                => $instance['orderby'],
            'order'                  => $instance['order'],
            'hide_empty'             => false,
            'include'                => $instance['include'],
            'exclude'                => $instance['exclude'],
            'exclude_tree'           => array(),
            'number'                 => $instance['number'],
            'hierarchical'           => false,
            'pad_counts'             => false,
            'child_of'               => 0,
            'childless'              => false,
            'cache_domain'           => 'core',
            'taxonomy'               => 'coupon_store',
            'update_term_meta_cache' => true,
        );

        $stores = get_terms( $tax_args );
        global $post;
        ?>
        <div class="widget-content shadow-box">
            <div class="ui <?php echo wpcoupon_number_to_html_class( $instance['item_per_row'] ); ?> column grid">
                <?php
                foreach ( $stores as $store ) {
                    wpcoupon_setup_store( $store );
                    ?>
                <div class="column">
                    <div class="store-thumb">
                        <a class="ui image middle aligned" href="<?php echo wpcoupon_store()->get_url(); ?>">
                            <?php echo wpcoupon_store()->get_thumbnail() ?>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
       // wp_reset_postdata();
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
            'title'         => '',
            'number'        => 6,
            'item_per_row' => 2,
            'include' => '',
            'exclude' => '',
            'orderby' => 'count',
            'order' => 'desc',
        ) );
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Stores', 'wp-coupon' );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ,'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number store to show:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php esc_html_e( 'Comma-separated of term ids to include:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" type="text" value="<?php echo esc_attr( $instance['include'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php esc_html_e( 'Comma-separated of term ids to exclude:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo esc_attr( $instance['exclude'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'item_per_row' ); ?>"><?php esc_html_e( 'Number item per row:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'item_per_row' ); ?>">
            <?php for (  $i = 1; $i <=16 ; $i++ ) {
                echo '<option value="'.$i.'" '.selected( $instance['item_per_row'], $i, false ).' >'.$i.'</option>';
            } ?>
            </select>

        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                <?php foreach ( array(
                                    'name' => esc_html__( "Name", 'wp-coupon' ),
                                    'count' => esc_html__( "Number Coupons", 'wp-coupon' ),
                                    'rand' => esc_html__( "Random", 'wp-coupon' ),
                                ) as $k => $t ) {
                    echo '<option value="'.esc_attr( $k ).'" '.selected( $instance['orderby'], $k, false ).' >'.esc_html( $t ).'</option>';
                } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'order' ); ?>">
                <?php foreach ( array(
                                    'desc' => esc_html__( "Desc", 'wp-coupon' ),
                                    'asc'=> esc_html__( "Asc", 'wp-coupon' ),
                                ) as $k => $t ) {
                    echo '<option value="'.esc_attr( $k ).'" '.selected( $instance['order'], $k, false ).' >'.esc_html( $t ).'</option>';
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
        $new_instance['include'] = ( isset( $new_instance['include'] ) ) ? sanitize_text_field( $new_instance['include'] ) : '';
        $new_instance['exclude'] = ( isset( $new_instance['exclude'] ) ) ? sanitize_text_field( $new_instance['exclude'] ) : '';
        $new_instance['orderby'] = ( isset( $new_instance['orderby'] ) ) ? sanitize_text_field( $new_instance['orderby'] ) : '';
        $new_instance['order']   = ( isset( $new_instance['order'] ) ) ? sanitize_text_field( $new_instance['order'] ) : '';
        return $new_instance;
       // return $instance;
    }

} // class Popular_Store

// register Foo_Widget widget
function wpcoupon_register_popular_store_widget() {
    register_widget( 'WPCoupon_Popular_Store' );
}
add_action( 'widgets_init', 'wpcoupon_register_popular_store_widget' );