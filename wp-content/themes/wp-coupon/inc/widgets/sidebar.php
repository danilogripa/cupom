<?php
/**
 * Add Sidebar widget.
 */
class WPCoupon_Sidebar_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'wpcoupon_sidebar', // Base ID
            esc_html__( 'WPCoupon Sidebar', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display a Sidebar', 'wp-coupon' ),
                'classname' => 'st-sidebar'
            ), // Args
            array(
                'width' => 530
            )
        );
    }

    public function form( $instance ) {

        $instance =  wp_parse_args( $instance, array(
            'sidebar' => 'sidebar',
        ) );
        global $wp_registered_sidebars;

        $options = array();

        foreach( $wp_registered_sidebars as $k => $sidebar ){
            $options[ $k ] = $sidebar['name'];
        }

        ?>

        <p class="st-widget-sidebar-settings">
            <label for="<?php echo $this->get_field_id( 'sidebar' ); ?>"><?php esc_html_e( 'Sidebar', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'sidebar' ); ?>">
                <?php
                foreach ( $options as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $instance['sidebar'], $k, false ).' >'.$v.'</option>';
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
            'sidebar' => 'sidebar',
        ) );

        ?>
        <div class="widget-area" role="complementary">
            <?php
            dynamic_sidebar( $instance['sidebar']  );
            ?>
        </div>
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
function wpcoupon_register_sidebar_widget() {
    register_widget( 'WPCoupon_Sidebar_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_sidebar_widget' );