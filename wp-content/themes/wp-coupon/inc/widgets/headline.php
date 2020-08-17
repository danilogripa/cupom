<?php
/**
 * Add Headline widget.
 */
class WPCoupon_Headline_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_headline', // Base ID
            esc_html__( 'WPCoupon Headline', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display a headline', 'wp-coupon' ),
                'classname' => 'st-headline'
            ), // Args
            array(
                'width' => 530
            )
        );
    }

    public function form( $instance ) {

        $instance =  wp_parse_args( $instance, array(
            'title' => 'Heading',
            'tag' => 'h2',
        ) );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <p class="st-widget-sidebar-settings">
            <label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php esc_html_e( 'Heading level', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'tag' ); ?>">
                <?php
                for ( $i = 1;  $i <= 6; $i++ ) {
                    echo '<option value="h'.$i.'" '.selected( $instance['tag'], 'h'.$i, false ).' >'.sprintf( 'Heading %s', $i ).'</option>';
                } ?>
            </select>
        </p>
        <?php
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
        // sidebar

        $instance =  wp_parse_args( $instance, array(
            'title' => 'title',
            'tag' => 'h2',
        ) );

        $tag =  'h2';

        if ( isset( $instance['tag']) ) {
            $tag =  $instance['tag'];
        }
        ?>
        <<?php echo $tag; ?> class="section-heading"><?php echo $instance['title'] ?></<?php echo $tag; ?>>
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
        //$new_instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $new_instance;
        // return $instance;
    }

} // class Popular_Store

// register Foo_Widget widget
function wpcoupon_register_headline_widget() {
    register_widget( 'WPCoupon_Headline_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_headline_widget' );