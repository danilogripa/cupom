<?php
/**
 * Add Slider widget.
 */
class WPCoupon_Slider_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_slider', // Base ID
            esc_html__( 'WPCoupon Slider', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display a Slider', 'wp-coupon' ),
                'classname' => 'st-slider-widget'
            ), // Args
            array(
                'width' => 530
            )
        );
    }

    public function item( $name, $value = array(), $closed = true ){

        $value = wp_parse_args( $value,
            array(
                'image_url'     => '',
                'image_id'      => '',
                'title'         => '',
                'description'   => '',
                'url'           => '',
            )
        );

        $class = $closed ? 'closed': '';

        $image_url = '';
        if ( $value['image_id'] > 0 ) {
            $image_attributes = wp_get_attachment_image_src( $value['image_id'] ); // returns an array
            if ( $image_attributes ) {
                $image_url =  $image_attributes[0];
            }
        }

        ?>
        <div data-name="<?php echo esc_attr( $name ); ?>" class="item <?php echo esc_attr( $class );?>">
            <div class="handle">
                <span class="item-index"></span>
                <span class="ellipsis live-item-title"></span>
                <a href="#" class="toggle"></a>
            </div>
            <div class="item-settings">

                <div class="image-upload-wrapper element-thumbnail">
                    <div class="image-upload thumb-preview">
                        <?php if ( $image_url != '' ){ ?>
                        <img src="<?php echo esc_attr( $image_url ); ?>" alt="">
                        <?php } ?>
                        <input type="hidden" name="<?php echo $this->get_field_name( 'fake' ); ?>" value="<?php echo esc_attr( $image_url != '' ? $image_url : $value['image_url'] ); ?>" class="image_url" data-name="image_url">
                        <input type="hidden" name="<?php echo $this->get_field_name( 'fake' ); ?>" value="<?php echo esc_attr( $value['image_id'] ); ?>" class="image_id" data-name="image_id" >
                    </div>
                    <a class="remove-thumbnail" href="#"><?php esc_attr_e( 'Remove', 'wp-coupon' ); ?></a>
                </div>

                <div class="element-settings">

                    <div class="element">
                        <label><?php esc_attr_e( 'Title', 'wp-coupon' ); ?></label>
                        <input type="text" name="<?php echo $this->get_field_name( 'fake' ); ?>" value="<?php echo esc_attr( $value['title'] ); ?>" data-name="title" class="live-title widefat">
                    </div>

                    <div class="element">
                        <label><?php esc_attr_e( 'Description', 'wp-coupon' ); ?></label>
                        <textarea name="<?php echo $this->get_field_name( 'fake' ); ?>" data-name="description" class="widefat"><?php echo esc_textarea( $value['description'] ); ?></textarea>
                    </div>

                    <div class="element">
                        <label><?php esc_attr_e( 'URL', 'wp-coupon' ); ?></label>
                        <input type="text" name="<?php echo $this->get_field_name( 'fake' ); ?>" value="<?php echo esc_attr( $value['url'] ); ?>" data-name="url" class="widefat">
                    </div>

                </div>

                <div class="actions">
                    <a href="#" class="remove"><?php esc_attr_e( 'Remove', 'wp-coupon' ); ?></a>|<a href="#" class="close"><?php esc_attr_e( 'Close', 'wp-coupon' ); ?></a>
                </div>

            </div>
        </div>
        <?php
    }

    public function form( $instance ) {

        $instance =  wp_parse_args( $instance, array(
            'title'     => '',
            'items'     => array(),
            'controls'  =>  array()
        ) );

        $id =  uniqid('widget-ui-');
        $name = $this->get_field_name( 'items' );

        $controls = wp_parse_args( $instance['controls'], array(
            'slideSpeed' => 300,
            'paginationSpeed' => 300,
            'autoPlay' => "true",
            'stopOnHover' => "true",
        ) );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <div class="st-widget-ui-items" id="<?php echo esc_attr( $id ); ?>">
            <div class="ui-items">
                <?php
                foreach( $instance['items'] as $item ) {
                    $this->item( $name, $item );
                }
                ?>
            </div><!-- /.ui-items -->
            <a href="#" class="button-secondary new-item"><?php esc_html_e( 'Add item', 'wp-coupon' ); ?></a>

            <script type="text/template" class="widget-ui-template" id="<?php echo esc_attr( $id ); ?>_template">
                <?php
                $this->item( $name, array(), false );
                ?>
            </script>
        </div><!-- /.st-widget-ui-items -->

        <script type="text/javascript">
            jQuery( document).ready( function( $ ){
                // $( "#sortable" ).sortable();
                new ST_Widgets( "<?php echo esc_js( $id ) ?>" );
            } );
        </script>

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
                    'false'  => esc_html__( 'no', 'wp-coupon' ),
                );
                foreach (  $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $controls['stopOnHover'], $k, false ).' >'.$v.'</option>';
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
            'title' => '',
            'items' => array(),
            'controls' => array(),
        ) );

        $instance['controls']  =  wp_parse_args(
            $instance['controls']
            , array(
                'navigation'        => true ,
                'navigationText'    => array(
                    '<img src="'.get_template_directory_uri().'/assets/images/arrow-left.png">',
                    '<img src="'.get_template_directory_uri().'/assets/images/arrow-right.png">'
                ),
                'slideSpeed'        => 300,
                'paginationSpeed'   => 300,
                'autoPlay'          => true,
                'stopOnHover'       => true,
                'singleItem'        => true,
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
        wp_enqueue_script( 'st-slider', $url.'assets/js/slider.js', array(), false, true );

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }

        ?>
        <div class="home-slider-wrapper shadow-box">
            <div class="st-slider slideshow home-slider" data-settings="<?php echo esc_attr( json_encode( $instance['controls'] ) ); ?>" >
                <?php
                foreach( $instance['items'] as $i => $item ) {

                    $item = wp_parse_args( $item,
                        array(
                            'image_url'     => '',
                            'image_id'      => '',
                            'title'         => '',
                            'description'   => '',
                            'url'           => '',
                        )
                    );

                    $image_url = '';
	                $image_alt = '';
                    if ( $item['image_id'] > 0 ) {
                        $image_attributes = wp_get_attachment_image_src( $item['image_id'], 'full' ); // returns an array
	                    $image_alt = get_post_meta( $item['image_id'], '_wp_attachment_image_alt', true);

	                    if ( $image_attributes ) {
                            $image_url =  $image_attributes[0];
                        }
                    }
                    if ( ! $image_url ) {
                        $image_url =  $item['image_url'];
                    }

                    if ( ! $image_url ) {
                        continue;
                    }

                    ?>
                    <div class="slideshow_item">
                        <?php
                        if( !empty( $item['url'] ) ) echo '<a title="'.esc_attr( $item['title'] ).'" href="' . esc_url( $item['url'] ) . '">';
                        ?>
                        <img src="<?php echo esc_url( $image_url ) ?>" alt="<?php echo esc_attr( $image_alt ) ?>">
                        <?php
                        if( ! empty( $item['url'] ) ) echo '</a>';
                        ?>
                    </div>

                    <?php
                }
                ?>
            </div><!-- END .slideshow -->
        </div><!-- END .home-slideshow-wrapper -->
        <?php
        echo $args['after_widget'];
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
function wpcoupon_register_slider_widget() {
    register_widget( 'WPCoupon_Slider_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_slider_widget' );