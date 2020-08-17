<?php
/**
 * Add Newsletter widget.
 * @see WP_Widget_Archives
 */
class WPCoupon_Newsletter extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_newsletter', // Base ID
            esc_html__( 'WPCoupon Newsletter', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display newsletter box', 'wp-coupon' ),
                'classname' => 'widget_newsletter widget_wpc_newsletter'
            ), // Args
            array(
                'width' => 430
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
        echo $args['before_widget'];
        $instance =  wp_parse_args( $instance, array(
            'title'         => '',
            'mailchimp'     => '',
            'before_form'   => '',
            'after_form'    => '',
            'facebook'      => '',
            'twitter'       => '',
            'linkedin'      => '',
            'google'        => '',
            'flickr'        => '',
            'youtube'       => '',
            'instagram'       => '',
            'pinterest'       => '',
            'link_target'    => '',
        ) );

        if ( ! $instance['link_target'] ) {
            $instance['link_target'] = '_blank';
        }

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }
        $form_id = uniqid( 'newsletter-box' );
        ?>
        <div class="newsletter-box-wrapper shadow-box">
            <form id="<?php echo $form_id; ?>" method="post" action="<?php echo esc_attr( $instance['mailchimp'] ) ?>" target="_blank">

                <?php if ( $instance['before_form'] != '' ) { ?>
                    <div class="newsletter-text before-form"><?php echo balanceTags( $instance['before_form'] ); ?></div>
                <?php } ?>

                <div class="ui action left icon input">
                    <input class="submit-input" type="email" required="required" name="EMAIL" placeholder="<?php esc_attr_e( 'Your email', 'wp-coupon' ); ?>">
                    <i class="mail outline icon"></i>
                    <div class="submit-btn ui button" onclick="document.getElementById('<?php echo $form_id; ?>').submit();"><?php esc_html_e( 'Subscribe', 'wp-coupon' ); ?></div>
                </div>
                <div class="clear"></div>

                <?php if ( $instance['after_form'] != '' ) { ?>
                <div class="newsletter-text after-form"><?php echo balanceTags( $instance['after_form'] ); ?></div>
                <?php } ?>
                <?php
                $socials = $this->get_socials( $instance );
                if ( $socials != '' ){
                ?>
                <div class="sidebar-social">
                    <?php
                        print( $socials );
                    ?>
                </div>
                <?php } ?>
            </form>
        </div>
        <?php

        echo $args['after_widget'];
    }

    function get_socials( $instance ){
        ob_start();
        $old_content = ob_get_clean();
        ob_start();
        ?>
        <?php if ( $instance['facebook'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['facebook'] ); ?>" class="ui circular icon button"><i class="facebook icon"></i></a>
        <?php } ?>

        <?php if ( $instance['twitter'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['twitter'] ); ?>" class="ui circular icon button"><i class="twitter icon"></i></a>
        <?php } ?>

        <?php if ( $instance['linkedin'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['linkedin'] ); ?>" class="ui circular icon button"><i class="linkedin icon"></i></a>
        <?php } ?>

        <?php if ( $instance['google'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['google'] ); ?>" class="ui circular icon button"><i class="google plus icon"></i></a>
        <?php } ?>

        <?php if ( $instance['flickr'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['flickr'] ); ?>" class="ui circular icon button"><i class="flickr icon"></i></a>
        <?php } ?>

        <?php if ( $instance['youtube'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['youtube'] ); ?>" class="ui circular icon button"><i class="youtube icon"></i></a>
        <?php } ?>

        <?php if ( $instance['instagram'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['instagram'] ); ?>" class="ui circular icon button"><i class="instagram icon"></i></a>
        <?php } ?>
        <?php if ( $instance['pinterest'] ) { ?>
            <a target="<?php echo esc_attr( $instance['link_target'] ); ?>" href="<?php echo esc_attr( $instance['pinterest'] ); ?>" class="ui circular icon button"><i class="pinterest icon"></i></a>
        <?php } ?>
        <?php
        $content = ob_get_clean();
        echo wp_kses_post( $old_content );
        return trim( $content );
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
            'mailchimp'     => '',
            'before_form'   => '',
            'after_form'    => '',
            'facebook'      => '',
            'twitter'       => '',
            'linkedin'      => '',
            'google'        => '',
            'flickr'        => '',
            'youtube'       => '',
            'instagram'     => '',
            'pinterest'     => '',
            'link_target'   => '',
        ) );

        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <div>
            <label for="<?php echo $this->get_field_id( 'mailchimp' ); ?>"><?php esc_html_e( 'Mailchimp action:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'mailchimp' ); ?>" name="<?php echo $this->get_field_name( 'mailchimp' ); ?>" type="text" value="<?php echo esc_attr( $instance['mailchimp'] ); ?>">
            <p class="description">
                E.g: http://yourdomainid.us7.list-manage.com/subscribe/post?u=dc130fe66084d082c54779086&id=736887358d <br/>
            </p>
        </div>

        <p>
            <label for="<?php echo $this->get_field_id( 'before_form' ); ?>"><?php esc_html_e( 'Before form', 'wp-coupon' ); ?></label>
            <textarea style="width: 100%; height: 50px;" id="<?php echo $this->get_field_id( 'before_form' ); ?>" name="<?php echo $this->get_field_name( 'before_form' ); ?>" ><?php echo esc_textarea( $instance['before_form'] ); ?></textarea>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'after_form' ); ?>"><?php esc_html_e( 'After form', 'wp-coupon' ); ?></label>
            <textarea style="width: 100%; height: 50px;" id="<?php echo $this->get_field_id( 'after_form' ); ?>" name="<?php echo $this->get_field_name( 'after_form' ); ?>" ><?php echo esc_textarea( $instance['after_form'] ); ?></textarea>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php esc_html_e( 'facebook URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo esc_attr( $instance['facebook'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php esc_html_e( 'twitter URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo esc_attr( $instance['twitter'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php esc_html_e( 'linkedin URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text" value="<?php echo esc_attr( $instance['linkedin'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'google' ); ?>"><?php esc_html_e( 'google URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'google' ); ?>" name="<?php echo $this->get_field_name( 'google' ); ?>" type="text" value="<?php echo esc_attr( $instance['google'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'flickr' ); ?>"><?php esc_html_e( 'flickr URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'flickr' ); ?>" name="<?php echo $this->get_field_name( 'flickr' ); ?>" type="text" value="<?php echo esc_attr( $instance['flickr'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php esc_html_e( 'youtube URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" type="text" value="<?php echo esc_attr( $instance['youtube'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php esc_html_e( 'Instagram URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text" value="<?php echo esc_attr( $instance['instagram'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php esc_html_e( 'Pinterest URL:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'pinterest' ); ?>" name="<?php echo $this->get_field_name( 'pinterest' ); ?>" type="text" value="<?php echo esc_attr( $instance['pinterest'] ); ?>">
        </p>


        <p class="link-target">
            <label for="<?php echo $this->get_field_id( 'link_target' ); ?>"><?php esc_html_e( 'Social link target', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'link_target' ); ?>">
                <option <?php selected( $instance['link_target'], '_blank' ) ?> value="_blank"><?php esc_html_e( '_blank', 'wp-coupon' ); ?></option>
                <option <?php selected( $instance['link_target'], '_self' ) ?> value="_self"><?php esc_html_e( '_self', 'wp-coupon' ); ?></option>
                <option <?php selected( $instance['link_target'], '_parent' ) ?> value="_parent"><?php esc_html_e( '_parent', 'wp-coupon' ); ?></option>
                <option <?php selected( $instance['link_target'], '_top' ) ?> value="_top"><?php esc_html_e( '_top', 'wp-coupon' ); ?></option>
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
function wpcoupon_register_newsletter_widget() {
    register_widget( 'WPCoupon_Newsletter' );
}
add_action( 'widgets_init', 'wpcoupon_register_newsletter_widget' );
