<?php
/**
 * Add Taxonomy widget.
 */
class WPCoupon_Categories extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'st_categories', // Base ID
            esc_html__( 'WPCoupon Categories', 'wp-coupon' ), // Name
            array(
                'description' => esc_html__( 'Display any taxonomies as categories list', 'wp-coupon' ),
                'classname' => 'widget_wpc_categories'
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
            'number'        => 6,
            'taxonomy'      => 'category',
            'orderby'       => 'count',
            'order'         => 'desc',
            'depth'         => '1',
            'show_count'    => 0,
            'item_per_row'  => 2,
        ) );

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }


        $_args = array(
            'show_option_all'    => '',
            'orderby'            => 'name',
            'order'              => 'ASC',
            'style'              => 'list',
            'show_count'         => 1,
            'hide_empty'         => 0,
            'use_desc_for_title' => 1,
            'child_of'           => 0,
            'feed'               => '',
            'feed_type'          => '',
            'feed_image'         => '',
            'exclude'            => '',
            'exclude_tree'       => '',
            'include'            => '',
            'hierarchical'       => 1,
            'title_li'           => false,
            'show_option_none'   => false,
            'number'             => null,
            'echo'               => 1,
            'depth'              => 1,
            'current_category'   => 0,
            'pad_counts'         => 0,
            'taxonomy'           => 'category',
            'walker'             =>  new WPCoupon_Walker_Category()
        );

        $_args =  wp_parse_args( $instance , $_args );

        echo '<div class="'.apply_filters( 'st_categories_class_name' , 'list-categories shadow-box' ).'">';
            echo '<div class="ui grid stackable">';
                echo '<ul class="'.wpcoupon_number_to_html_class( $instance['item_per_row'] ).' column row">';
                    wp_list_categories( $_args );
                echo '</ul>';
            echo '</div>';
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

        $instance =  wp_parse_args( $instance, array(
            'title'         => '',
            'number'        => 6,
            'taxonomy'      => 'category',
            'orderby'       => 'count',
            'order'         => 'desc',
            'depth'         => '1',
            'show_count'    => 0,
            'item_per_row'  => 2,
        ) );

        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';

        $taxonomies = get_taxonomies( array(
            'public'   => true,
        ), 'objects' );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php esc_html_e( 'Taxonomy:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
                <?php foreach (  $taxonomies as $k => $tax ) {
                    echo '<option value="'.$k.'" '.selected( $instance['taxonomy'], $k, false ).' >'.$tax->labels->name.' ('.$k.')'.'</option>';
                } ?>
            </select>
        </p>

        <div>
            <label for="<?php echo $this->get_field_id( 'depth' ); ?>"><?php esc_html_e( 'Depth:', 'wp-coupon' ); ?></label>
            <?php

            $depth = array(
                '0' => '<code>0</code> - ' . esc_html__( 'All Categories and child Categories', 'wp-coupon' ),
                '-1' => '<code>-1</code> -' . esc_html__('All Categories displayed in flat (no indent) form (overrides hierarchical)', 'wp-coupon' ),
                '1' => '<code>1</code> - ' . esc_html__( 'Show only top level Categories', 'wp-coupon' ),
                'n' => '<code>n</code> - ' . esc_html__( 'Value of n (a number) specifies the depth (or level) to descend in displaying Categories', 'wp-coupon' ),
            );

            ?>

            <input class="widefat" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" type="text" value="<?php echo esc_attr( $instance['depth'] ); ?>">
            <br/>

            <p class="description"><?php echo join( "<br/>", $depth ); ?></p>
        </div>


        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                <?php
                $a = array(
                    'count' => esc_html__( 'Count', 'wp-coupon' ),
                    'name'  => esc_html__( 'Name', 'wp-coupon' ),
                ) ;
                foreach ( $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $instance['orderby'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'order' ); ?>">
                <?php
                $a = array(
                    'desc' => esc_html__( 'Desc', 'wp-coupon' ),
                    'asc'  => esc_html__( 'Asc', 'wp-coupon' ),
                );
                foreach (  $a as $k => $v ) {
                    echo '<option value="'.$k.'" '.selected( $instance['order'], $k, false ).' >'.$v.'</option>';
                } ?>
            </select>
        </p>


        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number store to show:', 'wp-coupon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php esc_html_e( 'Show count ?', 'wp-coupon' ); ?></label>
            <input class="widefat" <?php checked( $instance['show_count'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" value="1">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'item_per_row' ); ?>"><?php esc_html_e( 'Number item per row:', 'wp-coupon' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'item_per_row' ); ?>">
                <?php for (  $i = 1; $i <=16 ; $i++ ) {
                    echo '<option value="'.$i.'" '.selected( $instance['item_per_row'], $i, false ).' >'.$i.'</option>';
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


function wpcoupon_register_categories_widget() {
    register_widget( 'WPCoupon_Categories' );
}
add_action( 'widgets_init', 'wpcoupon_register_categories_widget' );



class WPCoupon_Walker_Category extends Walker_Category{

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.1.0
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category in reference to parents. Default 0.
     * @param array  $args     An array of arguments. @see wp_list_categories()
     * @param int    $id       ID of the current category.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        /** This filter is documented in wp-includes/category-template.php */
        $cat_name = apply_filters(
            'list_cats',
            esc_attr( $category->name ),
            $category
        );

        // Don't generate an element if the category name is empty.
        if ( ! $cat_name ) {
            return;
        }

        $link = '<a href="' . esc_url( get_term_link( $category ) ) . '" ';
        if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
            /**
             * Filter the category description for display.
             *
             * @since 1.2.0
             *
             * @param string $description Category description.
             * @param object $category    Category object.
             */
            $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
        }

        $link .= '>';

        if ( ! empty( $args['show_count'] ) ) {
            $link .= ' <span class="coupon-count">' . number_format_i18n( $category->count ) . '</span> ';
        }

        $link .= $cat_name . '</a>';

        if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
            $link .= ' ';

            if ( empty( $args['feed_image'] ) ) {
                $link .= '(';
            }

            $link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

            if ( empty( $args['feed'] ) ) {
                $alt = ' alt="' . sprintf(esc_html__( 'Feed for all posts filed under %s', 'wp-coupon' ), $cat_name ) . '"';
            } else {
                $alt = ' alt="' . $args['feed'] . '"';
                $name = $args['feed'];
                $link .= empty( $args['title'] ) ? '' : $args['title'];
            }

            $link .= '>';

            if ( empty( $args['feed_image'] ) ) {
                $link .= $name;
            } else {
                $link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
            }
            $link .= '</a>';

            if ( empty( $args['feed_image'] ) ) {
                $link .= ')';
            }
        }



        if ( 'list' == $args['style'] ) {
            $output .= "\t<li";
            $css_classes = array(
                'column',
                'cat-item',
                'cat-item-' . $category->term_id,
            );

            if ( ! empty( $args['current_category'] ) ) {
                $_current_category = get_term( $args['current_category'], $category->taxonomy );
                if ( $category->term_id == $args['current_category'] ) {
                    $css_classes[] = 'current-cat';
                } elseif ( $category->term_id == $_current_category->parent ) {
                    $css_classes[] = 'current-cat-parent';
                }
            }

            /**
             * Filter the list of CSS classes to include with each category in the list.
             *
             * @since 4.2.0
             *
             * @see wp_list_categories()
             *
             * @param array  $css_classes An array of CSS classes to be applied to each list item.
             * @param object $category    Category data object.
             * @param int    $depth       Depth of page, used for padding.
             * @param array  $args        An array of wp_list_categories() arguments.
             */
            $css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

            $output .=  ' class="' . $css_classes . '"';
            $output .= ">$link\n";
        } else {
            $output .= "\t$link<br />\n";
        }
    }

}
