<?php
/**
 * Add Coupons widget.
 */
class WPCoupon_Coupons_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_coupons', // Base ID
            esc_html__( 'WPCoupon Coupons', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display Coupons', 'wp-coupon' ),
                'classname' => 'st-coupons'
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
        echo $args['before_widget'];

        $instance =  wp_parse_args( $instance, array(
            'title'               => '',
            'posts_per_page'      => '',
            'num_words'           => 10,
            'num_words_no_thumb'  => 16,
            'layout'              => 'less',
            'show_paging'         => '',
            'hide_expired'         => '',
            'hide_latest'        => '',
            'show_popular'        => '',
            'show_ending_soon'    => '',
        ) );

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }

        global $wp_query,  $post, $paged, $wp_rewrite;
        $paged =  wpcoupon_get_paged();
        $get_args = array();
        $number =  apply_filters( 'st_coupons_number' , get_option( 'posts_per_page' ) );
        if( $instance['posts_per_page'] ) {
            $number = $instance['posts_per_page'];
        } else {

        }

        $get_args[ 'posts_per_page' ] = $number;
        $get_args['hide_expired'] = $instance['hide_expired'];
        $posts =  wpcoupon_get_coupons( $get_args, $paged , $_max_page );
        $current_link = get_permalink();

        $tpl_name = null;
        if ( ! $instance['layout'] || $instance['layout'] == 'less' ) {
            $tpl_name = 'cat';
        }

        if ( isset( $instance['num_words'] ) ) {
            $GLOBALS['coupon_num_words'] =  $instance['num_words'] ;
            $GLOBALS['coupon_num_words_no_thumb'] =  $instance['num_words_no_thumb'] ;
        }

        $id = uniqid('coupons-');

        $num_tab = 0;
        if ( $instance['show_popular'] ) {
            $num_tab ++;
        }

        if ( $instance['show_ending_soon'] ) {
            $num_tab ++;
        }

        if ( ! $instance['hide_latest'] ) {
            $num_tab ++;
        }

        if ( $num_tab > 1 ) {
        ?>
        <section class="coupon-filter">
            <div data-target="#<?php echo esc_attr( $id ); ?>" class="filter-coupons-by-tab ui pointing fluid three item menu">
                <?php if ( ! $instance['hide_latest'] ) { ?>
                <a data-filter=".latest-tab" class="item filter-nav active"><?php esc_html_e( 'Latest Coupons', 'wp-coupon' ); ?></a>
                <?php } ?>
                <?php if ( $instance['show_popular'] ) { ?>
                <a data-filter=".popular-tab" class="item filter-nav"><?php esc_html_e( 'Popular Coupons', 'wp-coupon' ); ?></a>
                <?php } ?>
                <?php if ( $instance['show_ending_soon'] ){ ?>
                <a data-filter=".ending-soon-tab" class="item filter-nav"><?php esc_html_e( 'Ending Soon', 'wp-coupon' ); ?></a>
                <?php } ?>
            </div>
        </section>
        <?php } ?>
        <div class="coupons-tab-contents" id="<?php echo esc_attr($id); ?>">
            <?php if ( ! $instance['hide_latest'] ) { ?>
                <div class="ajax-coupons coupon-tab-content latest-tab">
                    <?php if ( $posts ) { ?>
                    <div class="store-listings st-list-coupons">
                        <?php
                        foreach ($posts as $post) {
                            wpcoupon_setup_coupon($post, $current_link);
                            get_template_part('loop/loop-coupon', $tpl_name);
                        }
                        ?>
                    </div>
                    <?php } else { ?>
                        <div class="ui warning message">
                            <i class="close icon"></i>

                            <div class="header">
                                <?php esc_html_e('Oops! No coupons found', 'wp-coupon'); ?>
                            </div>
                            <p><?php esc_html_e('There is no coupons! Please comeback later.', 'wp-coupon'); ?></p>
                        </div>
                    <?php
                    }
                    if ($instance['show_paging']) {
                        if ($_max_page > 1) { ?>
                            <div class="load-more wpb_content_element">
                                <a href="<?php echo next_posts($_max_page, false); ?>" class="ui button btn btn_primary btn_large" data-args="<?php echo esc_attr(json_encode($instance)); ?>"
                                   data-next-page="<?php echo($paged + 1); ?>" data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i
                                        class="arrow circle down icon"></i></a>
                            </div>
                        <?php }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            <?php } ?>
            <?php
            if ( $instance['show_popular'] ) {
                // Popular coupons
                $coupons = wpcoupon_get_popular_coupons($number);
                $_max_page = $wp_query->max_num_pages;
                ?>
                <div class="ajax-coupons coupon-tab-content popular-tab <?php echo ( $num_tab > 1 ) ? 'hide' : '' ?>">
                    <div class="store-listings st-list-coupons">
                        <?php
                        if ($coupons) {
                            foreach ($coupons as $post) {
                                wpcoupon_setup_coupon($post, $current_link);
                                get_template_part('loop/loop-coupon', $tpl_name);
                            }
                        } else {
                            ?>
                            <div class="ui warning message">
                                <i class="close icon"></i>

                                <div class="header">
                                    <?php esc_html_e('Oops! No coupons found', 'wp-coupon'); ?>
                                </div>
                                <p><?php esc_html_e('There is no coupons! Please comeback later.', 'wp-coupon'); ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php

                    if ($instance['show_paging']) {
                        if ($_max_page > 1) { ?>
                            <div class="load-more wpb_content_element">
                                <a href="<?php echo next_posts($_max_page, false); ?>" class="ui button btn btn_primary btn_large"
                                   data-doing="load_popular_coupons"
                                   data-args="<?php echo esc_attr(json_encode($instance)); ?>"
                                   data-next-page="<?php echo($paged + 1); ?>" data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?>
                                    <i class="arrow circle down icon"></i></a>
                            </div>
                        <?php }
                    }

                    wp_reset_postdata();
                    ?>
                </div>
                <?php
            }

            if ( $instance['show_ending_soon'] ){
                // end ding soon
                $coupons  = wpcoupon_get_ending_soon_coupons( apply_filters( 'wpcoupon_ending_soon_coupons_day_left', 3 ), $number );
                $_max_page =  $wp_query->max_num_pages;
                ?>
                <div class="ajax-coupons coupon-tab-content ending-soon-tab <?php echo ( $num_tab > 1 ) ? 'hide' : '' ?>">
                    <div class="store-listings st-list-coupons">
                        <?php
                        if ( $coupons ) {
                            foreach ($coupons as $post) {
                                wpcoupon_setup_coupon($post, $current_link);
                                get_template_part('loop/loop-coupon', $tpl_name);
                            }
                        } else {
                            ?>
                            <div class="ui warning message">
                                <i class="close icon"></i>
                                <div class="header">
                                    <?php esc_html_e( 'Oops! No coupons found', 'wp-coupon' ); ?>
                                </div>
                                <p><?php esc_html_e( 'There is no coupons ! Please comeback later.', 'wp-coupon' ); ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php

                    if ( $instance['show_paging'] ) {
                        if ($_max_page > 1) { ?>
                            <div class="load-more wpb_content_element">
                                <a href="<?php echo next_posts($_max_page, false); ?>" class="ui button btn btn_primary btn_large"
                                   data-doing="load_ending_soon_coupons"
                                   data-args="<?php echo esc_attr(json_encode($instance)); ?>"
                                   data-next-page="<?php echo($paged + 1); ?>" data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?>
                                    <i class="arrow circle down icon"></i>
                                </a>
                            </div>
                        <?php }
                    }

                    wp_reset_postdata();
                    ?>
                </div>
            <?php
            }
            ?>
        </div>
        <?php
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
            'posts_per_page'      => '',
            'num_words'           => 10,
            'num_words_no_thumb'  => 16,
            'layout'              => 'less',
            'show_paging'         => '',
            'hide_expired'         => '',
            'popular_tab'         => '',

            'hide_latest'         => '',
            'show_popular'         => '',
            'show_ending_soon'     => '',
        ) );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php esc_html_e( 'Number coupons to show:', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $instance['posts_per_page'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'num_words' ); ?>"><?php esc_html_e( 'Excerpt length:', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'num_words' ); ?>" name="<?php echo $this->get_field_name( 'num_words' ); ?>" type="text" value="<?php echo esc_attr( $instance['num_words'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'num_words_no_thumb' ); ?>"><?php esc_html_e( 'Excerpt length if hide thumbnails:', 'wp-coupon' ); ?></label>
            <input class="" id="<?php echo $this->get_field_id( 'num_words_no_thumb' ); ?>" name="<?php echo $this->get_field_name( 'num_words_no_thumb' ); ?>" type="text" value="<?php echo esc_attr( $instance['num_words_no_thumb'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php esc_html_e( 'Box layout:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'layout' ); ?>">
                <?php
                $a = array(
                    'less' => esc_html__( 'Less', 'wp-coupon' ),
                    'full'  => esc_html__( 'Full', 'wp-coupon' ),
                ) ;
                foreach ( $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $instance['layout'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'show_paging' ); ?>"><?php esc_html_e( 'Show paging:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'show_paging' ); ?>">
                <?php
                $a = array(
                    '' => esc_html__( 'No', 'wp-coupon' ),
                    'yes'  => esc_html__( 'Yes', 'wp-coupon' ),
                ) ;
                foreach ( $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $instance['show_paging'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <input <?php checked( $instance['hide_expired'], 1 ); ?> class="" id="<?php echo $this->get_field_id( 'hide_expired' ); ?>" value="1" name="<?php echo $this->get_field_name( 'hide_expired' ); ?>" type="checkbox" ">
            <label for="<?php echo $this->get_field_id( 'hide_expired' ); ?>"><?php esc_html_e( 'Do not show expired coupons.', 'wp-coupon' ); ?></label>
        </p>
        <hr>

        <p>
            <input <?php checked( $instance['hide_latest'], 1 ); ?> class="" id="<?php echo $this->get_field_id( 'hide_latest' ); ?>" value="1" name="<?php echo $this->get_field_name( 'hide_latest' ); ?>" type="checkbox" ">
            <label for="<?php echo $this->get_field_id( 'hide_latest' ); ?>"><?php esc_html_e( 'Hide latest coupons tab', 'wp-coupon' ); ?></label>
        </p>
        <p>
            <input <?php checked( $instance['show_popular'], 1 ); ?> class="" id="<?php echo $this->get_field_id( 'show_popular' ); ?>" value="1" name="<?php echo $this->get_field_name( 'show_popular' ); ?>" type="checkbox" ">
            <label for="<?php echo $this->get_field_id( 'show_popular' ); ?>"><?php esc_html_e( 'Show popular tab', 'wp-coupon' ); ?></label>
        </p>
        <p>
            <input <?php checked( $instance['show_ending_soon'], 1 ); ?> class="" id="<?php echo $this->get_field_id( 'show_ending_soon' ); ?>" value="1" name="<?php echo $this->get_field_name( 'show_ending_soon' ); ?>" type="checkbox" ">
            <label for="<?php echo $this->get_field_id( 'show_ending_soon' ); ?>"><?php esc_html_e( 'Show ending tab', 'wp-coupon' ); ?></label>
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
function wpcoupon_register_coupons_widget() {
    register_widget( 'WPCoupon_Coupons_Widget' );
}
add_action( 'widgets_init', 'wpcoupon_register_coupons_widget' );