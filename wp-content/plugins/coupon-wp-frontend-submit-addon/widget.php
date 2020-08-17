<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds WPCoupon_Coupon_Submit_Widget widget.
 */
class WPCoupon_Coupon_Submit_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_coupon_submit_widget', // Base ID
            esc_html__( 'Coupon Submit', 'wp-coupon-submit' ), // Name
            array( 'description' => esc_html__( 'Display submit coupon form.', 'wp-coupon-submit' ), ) // Args
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
            'title' => '',
            'store_title' => '',
        ) );
        $title =  $instance['title'];
        if ( is_singular( 'store' ) ) {
            $title = ( $instance['store_title'] != '' ) ? $instance['store_title'] : $title;
            $title = str_replace( '{store_name}', get_the_title(), $title );
        }

        echo $args['before_widget'];

        if ( ! empty( $title) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $title , 'st_coupon_submit_widget' ). $args['after_title'];
        }
        echo '<div class="widget-content shadow-box">';
        echo  WPCoupon_Coupon_Submit_ShortCode::submit_coupon_form();
        echo '</div>';
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Submit new coupon', 'wp-coupon-submit' );
        $store_title = ! empty( $instance['store_title'] ) ? $instance['store_title'] : esc_html__( 'Submit {store_name}\'s Coupon', 'wp-coupon-submit' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon-submit' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'store_title' ); ?>"><?php esc_html_e( 'Single Store Title:', 'wp-coupon-submit' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'store_title' ); ?>" name="<?php echo $this->get_field_name( 'store_title' ); ?>" type="text" value="<?php echo esc_attr( $store_title ); ?>">
        </p>
        <p class="description"><?php esc_html_e( 'The special title that will display on store page instead of Title, available tag: {store_name}', 'wp-coupon-submit' ); ?></p>
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
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['store_title'] = ( ! empty( $new_instance['store_title'] ) ) ? strip_tags( $new_instance['store_title'] ) : '';
        return $instance;
    }

} // class WPCoupon_Coupon_Submit_Widget


// register Foo_Widget widget
function wpcoupon_register_coupon_submit_widget() {
    register_widget( 'WPCoupon_Coupon_Submit_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_coupon_submit_widget' );