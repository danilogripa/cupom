<?php
// remove content plugin
if ( function_exists( 'WP_Coupons_Plugin' ) ){
    remove_action( 'plugins_loaded', 'WP_Coupons_Plugin' );
    if ( ! function_exists( 'deactivate_plugins' ) ) {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        // No longer needed this plugin, disable in silent.
        deactivate_plugins( 'wp-coupon-content-type/wp-coupon-content-type.php', true );
    }

}
add_action( 'init', 'wpcoupon_theme_post_types_init' );

/**
 * Register post types for theme.
 *
 * @TODO  Register coupon, store post type, store taxonomies
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @since 1.0.0
 */
function wpcoupon_theme_post_types_init() {
    // Coupon post type
    $labels = array(
        'name'               => esc_html_x( 'Coupons', 'post type general name', 'wp-coupon' ),
        'singular_name'      => esc_html_x( 'Coupon', 'post type singular name', 'wp-coupon' ),
        'menu_name'          => esc_html_x( 'Coupons', 'admin menu', 'wp-coupon' ),
        'name_admin_bar'     => esc_html_x( 'Coupon', 'add new on admin bar', 'wp-coupon' ),
        'add_new'            => esc_html_x( 'Add New', 'coupon', 'wp-coupon' ),
        'add_new_item'       => esc_html__( 'Add New Coupon', 'wp-coupon' ),
        'new_item'           => esc_html__( 'New Coupon', 'wp-coupon' ),
        'edit_item'          => esc_html__( 'Edit Coupon', 'wp-coupon' ),
        'view_item'          => esc_html__( 'View Coupon', 'wp-coupon' ),
        'all_items'          => esc_html__( 'All Coupons', 'wp-coupon' ),
        'search_items'       => esc_html__( 'Search Coupons', 'wp-coupon' ),
        'parent_item_colon'  => esc_html__( 'Parent Coupons:', 'wp-coupon' ),
        'not_found'          => esc_html__( 'No coupons found.', 'wp-coupon' ),
        'not_found_in_trash' => esc_html__( 'No coupons found in Trash.', 'wp-coupon' ),
        'attributes'        => esc_html__( 'Coupon attributes', 'wp-coupon' )
    );

    $enable_single = wpcoupon_get_option( 'enable_single_coupon', false );
    if ( $enable_single ) {
        $enable_single = true;
    } else {
        $enable_single = false;
    }

    $args = array(
        'labels'             => $labels,
        'public'             => $enable_single,
        'publicly_queryable' => $enable_single,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'coupon' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'exclude_from_search' => false,
        'feeds'              => true,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-tickets-alt',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt', 'comments', 'page-attributes' )
    );

    register_post_type( 'coupon', $args );

    $cat_slug = trim( wpcoupon_get_option( 'rewrite_category_slug', '' ) );
    if ( ! $cat_slug ) {
        $cat_slug = 'coupon-category';
    }

    /**
     * Store category
     * Add new taxonomy, make it hierarchical (like categories)
     */
    $labels = array(
        'name'              => esc_html_x( 'Coupon Categories', 'taxonomy general name', 'wp-coupon' ),
        'singular_name'     => esc_html_x( 'Coupon Category', 'taxonomy singular name', 'wp-coupon' ),
        'search_items'      => esc_html__( 'Search Coupon Categories', 'wp-coupon' ),
        'all_items'         => esc_html__( 'All Coupon Categories', 'wp-coupon' ),
        'parent_item'       => esc_html__( 'Parent Coupon Category', 'wp-coupon' ),
        'parent_item_colon' => esc_html__( 'Parent Coupon Category:', 'wp-coupon' ),
        'edit_item'         => esc_html__( 'Edit Coupon Category', 'wp-coupon' ),
        'update_item'       => esc_html__( 'Update Category', 'wp-coupon' ),
        'add_new_item'      => esc_html__( 'Add New Coupon Category', 'wp-coupon' ),
        'new_item_name'     => esc_html__( 'New Coupon Category Name', 'wp-coupon' ),
        'menu_name'         => esc_html__( 'Categories', 'wp-coupon' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => false,
        'show_in_menu'      => true,
        'show_in_nav_menus' => true,
        'show_in_quick_edit' => true,
        'rewrite'           => array( 'slug' => $cat_slug ),
    );

    register_taxonomy( 'coupon_category', array( 'coupon' ), $args );

    $store_slug = trim( wpcoupon_get_option( 'rewrite_store_slug', '' ) );
    if ( ! $store_slug ) {
        $store_slug = 'store';
    }
    /**
     * Coupon Store
     *
     * Add new taxonomy, make it hierarchical (like categories)
     */
    $labels = array(
        'name'              => esc_html_x( 'Coupon Stores', 'taxonomy general name', 'wp-coupon' ),
        'singular_name'     => esc_html_x( 'Coupon Store', 'taxonomy singular name', 'wp-coupon' ),
        'search_items'      => esc_html__( 'Search Stores', 'wp-coupon' ),
        'all_items'         => esc_html__( 'All Stores', 'wp-coupon' ),
        'parent_item'       => esc_html__( 'Parent Store', 'wp-coupon' ),
        'parent_item_colon' => esc_html__( 'Parent Store:', 'wp-coupon' ),
        'update_item'       => esc_html__( 'Update Store', 'wp-coupon' ),
        'add_new_item'      => esc_html__( 'Add New Store', 'wp-coupon' ),
        'new_item_name'     => esc_html__( 'New Store', 'wp-coupon' ),
        'menu_name'         => esc_html__( 'Stores', 'wp-coupon' ),
        'view_item'         => esc_html__( 'View Store', 'wp-coupon' ),
        'edit_item'         => esc_html__( 'Edit Store', 'wp-coupon' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_menu'      => true,
        'show_in_nav_menus' => true,
        'show_in_quick_edit'=> true,
        'rewrite'           => array( 'slug' => $store_slug ),
    );

    register_taxonomy( 'coupon_store', array( 'coupon' ), $args );


    $tag_slug = trim( wpcoupon_get_option( 'rewrite_tag_slug', '' ) );
    if ( ! $tag_slug ) {
        $tag_slug = 'coupon-tag';
    }
    /**
     * Coupon Store
     *
     * Add new taxonomy, make it hierarchical (like categories)
     */
    $labels = array(
        'name'              => esc_html_x( 'Coupon Tags', 'taxonomy general name', 'wp-coupon' ),
        'singular_name'     => esc_html_x( 'Coupon Tag', 'taxonomy singular name', 'wp-coupon' ),
        'search_items'      => esc_html__( 'Search Tag', 'wp-coupon' ),
        'all_items'         => esc_html__( 'All Tags', 'wp-coupon' ),
        'parent_item'       => esc_html__( 'Parent Tags', 'wp-coupon' ),
        'parent_item_colon' => esc_html__( 'Parent Tag:', 'wp-coupon' ),
        'update_item'       => esc_html__( 'Update Tag', 'wp-coupon' ),
        'add_new_item'      => esc_html__( 'Add New Tag', 'wp-coupon' ),
        'new_item_name'     => esc_html__( 'New Tag', 'wp-coupon' ),
        'menu_name'         => esc_html__( 'Tags', 'wp-coupon' ),
        'view_item'         => esc_html__( 'View Tags', 'wp-coupon' ),
        'edit_item'         => esc_html__( 'Edit Tag', 'wp-coupon' ),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'show_in_menu'      => true,
        'show_in_nav_menus' => false,
        'show_in_quick_edit'=> true,
        'rewrite'           => array( 'slug' => $tag_slug ),
    );

    register_taxonomy( 'coupon_tag', array( 'coupon' ), $args );


}


if ( is_admin()  ) {

    class  WPCoupon_Theme_Edit_Taxs_Columns{

        function __construct() {

            add_filter('manage_edit-coupon_category_columns', array( $this, 'category_columns' ) );
            add_filter('manage_coupon_category_custom_column',  array( $this, 'category_fields' ), 10, 3);

            add_filter('manage_edit-coupon_store_columns', array( $this, 'store_columns' ) );
            add_filter('manage_coupon_store_custom_column',  array( $this, 'store_fields' ), 5, 3);

        }

        function category_columns( $columns ) {
            // add 'My Column'
            $columns['icon'] = __( 'Icon', 'wp-coupon' );
            return $columns;
        }

        function category_fields( $term , $column_name, $term_id ){
            switch ( $column_name ) {
                case 'icon':
                    $icon = get_term_meta( $term_id , '_wpc_icon', true );
                    if ( trim( $icon ) !== '' ){
                        echo '<span class="c-cat-icon"><i class="'.esc_attr( $icon ).'"></i></span>';
                    }
                    break;
            }
        }

        function store_columns( $columns ) {
            //$name = $columns['name'];
            //unset( $columns['name'] );
            //unset( $columns['description'] );
            //unset( $columns['slug'] );
            //$count = $columns['count'];
            // st_debug( $columns ); die();


            $new_columns = array();
            $new_columns['cb'] = $columns['cb'];
            $new_columns['thumb'] = __( 'Thumbnail', 'wp-coupon' );
            $new_columns['name']  =  $columns['name'];
            $new_columns['posts'] =  $columns['posts'];
            $new_columns['url']   = __( 'URL', 'wp-coupon' );
            $new_columns['out']   = __( 'Out', 'wp-coupon' );
            $new_columns['feature']  = '<span class="dashicons dashicons-star-filled"></span>';
            return $new_columns;
        }

        function store_fields( $unknown , $column_name, $term_id ){
            $s = new WPCoupon_Store( $term_id );
            switch ( $column_name ) {
                case 'thumb':
                    echo $s->get_thumbnail();
                    break;
                case 'feature':
                    if ( $s->is_featured() ){
                        echo '<span class="dashicons dashicons-star-filled"></span>';
                    } else {
                        echo '<span class="dashicons dashicons-star-empty"></span>';
                    }
                    break;
                case 'icon':
                    $icon = get_term_meta( $term_id , '_wpc_icon', true );
                    if ( trim( $icon ) !== '' ){
                        echo '<span class="c-cat-icon"><i class="'.esc_attr( $icon ).'"></i></span>';
                    }
                    break;
                case 'out':
                    $out = get_term_meta( $term_id , '_wpc_go_out', true );
                    echo intval( $out );
                    break;
                case 'url':
                    ?>
                    <div>
                        <span><?php esc_html_e( 'URL:', 'wp-coupon' ); ?></span>
                        <?php  echo ( $s->_wpc_store_url != '' ) ? '<a href="'.esc_url($s->_wpc_store_url).'" title="'.esc_attr( $s->_wpc_store_url ).'">'.esc_html($s->_wpc_store_url).'</a>' : __( '[empty]', 'wp-coupon' ); ?>
                    </div>
                    <div>
                        <span><?php esc_html_e( 'Aff:', 'wp-coupon' ); ?></span>
                        <?php  echo ( $s->_wpc_store_aff_url != '' ) ? '<a href="'.esc_url($s->_wpc_store_aff_url).'" title="'.esc_attr( $s->_wpc_store_aff_url ).'">'.esc_html($s->_wpc_store_aff_url).'</a>' : __( '[empty]', 'wp-coupon' ); ?>
                    </div>
                    <?php
                    break;
            }
        }

    }

    new WPCoupon_Theme_Edit_Taxs_Columns();


    function wpcoupon_filter_to_coupon_administration(){

        //execute only on the 'post' content type
        global $post_type;
        if($post_type == 'coupon'){

            $post_formats_args = array(
                //'show_option_all'   => 'All Categories',
                'show_option_none'  => 'All Categories',
                'option_none_value'  => '',
                'orderby'           => 'NAME',
                'order'             => 'ASC',
                'name'              => 'coupon_category',
                'taxonomy'          => 'coupon_category'
            );

            //if we have a post format already selected, ensure that its value is set to be selected
            if(isset($_GET['coupon_category'])){
                $post_formats_args['selected'] = sanitize_text_field($_GET['coupon_category']);
            }
            wp_dropdown_categories($post_formats_args);

            $types = wpcoupon_get_coupon_types();

            $type = '';
            if(isset($_GET['c_type'])){
                $type = sanitize_text_field($_GET['c_type']);
            }
            ?>
            <select class="postform" id="coupon_type" name="c_type">
                <option value=""><?php esc_html_e( 'All coupon types', 'wp-coupon' ); ?></option>
                <?php foreach ( $types as $k => $v ) { ?>
                <option <?php selected( $type, $k ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
                <?php } ?>
            </select>
            <?php
            $s = '';
            if ( isset ( $_GET['c_s_store'] ) ) {
                $s = $_GET['c_s_store'];
            }
            ?>
            <input type="text" id="search-coupon-store" placeholder="<?php esc_attr_e( 'Filter store','wp-coupon' ); ?>" value="<?php echo esc_attr( $s ); ?>" name="c_s_store">
            <?php

        }
    }
    add_action('restrict_manage_posts','wpcoupon_filter_to_coupon_administration');

    function wpcoupon_disable_months_dropdown( $r, $post_type ){
        if ( $post_type == 'coupon' ) {
            $r = true;
        }
        return $r;
    }

    add_filter( 'disable_months_dropdown', 'wpcoupon_disable_months_dropdown', 15, 2 );


    /**
     * restrict the posts by the chosen post format
     * @param $query WP_Query
     */
    function wpcoupon_add_filter_to_coupons( &$query){

        global $post_type, $pagenow;

        //if we are currently on the edit screen of the post type listings
        if($pagenow == 'edit.php' && $post_type == 'coupon'){
            if( isset( $_GET['coupon_category'] ) ){

                //get the desired post format
                $cat = sanitize_text_field($_GET['coupon_category']);
                //if the post format is not 0 (which means all)

                if( $cat != 0 ){
                    $query->query_vars['tax_query'] = array(
                        'relation' => 'AND',
                        array(
                            'taxonomy'  => 'coupon_category',
                            'field'     => 'term_id',
                            'terms'     => array( $cat )
                        )
                    );
                }
            }

            /**
             * Search coupon by coupon types
             * @see WPCoupon_Search::where()
             */
            if ( isset( $_GET['c_type'] ) ) {
                $t = trim( ( string ) $_GET['c_type'] );

                if ( $t ) {
                    //$query->is_search  = false;
                    //unset( $query->query_vars['s']  );
                    $query->set('meta_query', array(
                        'relation' => 'AND',
                        array(
                            'key'     => '_wpc_coupon_type',
                            'value'   => $t,
                            'compare' => 'LIKE',
                        )
                    ));
                }
            }

            // filter store
            if ( isset( $_GET['c_s_store'] ) ) {
                $s = trim( ( string ) $_GET['c_s_store'] );
                if ( $s ) {
                    if ( !isset( $_GET['coupon_category'] ) ) {
                        $query->query_vars['tax_query'] = array(
                            array(
                                'taxonomy'  => 'coupon_store',
                                'field'     => 'name',
                                'terms'     => array($s)
                            )
                        );
                    } else {
                        $query->query_vars['tax_query'][] = array(
                            'taxonomy'  => 'coupon_store',
                            'field'     => 'name',
                            'terms'     => array($s)
                        );
                    }

                }
            }

        }

    }
    add_action('pre_get_posts','wpcoupon_add_filter_to_coupons');


} // end is admin




class WPCoupon_Coupon_Admin {

    function __construct() {
        if ( is_admin() ) {
            /**
             * Add more custom column for coupon post type
             */
            add_filter('manage_coupon_posts_columns', array($this, 'custom_edit_coupon_columns'));
            add_action('manage_coupon_posts_custom_column', array($this, 'custom_coupon_column'), 10, 2);
            add_filter( 'manage_edit-coupon_sortable_columns', array($this, 'sortable_columns'));
            add_action( 'pre_get_posts', array($this, 'column_orderby'));

        }

        add_action( "wp_insert_post",  array( __CLASS__, 'update_store_data' ), 96, 3 );
        add_action( "wp_insert_post",  array( __CLASS__, 'update_store_count' ), 97, 2 );

        add_action( "after_frontend_coupon_submitted",  array( __CLASS__, 'update_store_data' ), 96, 3 );
        add_action( "after_frontend_coupon_submitted",  array( __CLASS__, 'update_store_count' ), 97, 2 );

        add_action( 'before_delete_post', array( $this, 'delete_coupon' ) );

    }

    function column_orderby( $query ) {
        if( ! is_admin() ) {
            return;
        }

        if ( $query->get( 'post_type' ) != 'coupon' ) {
            return ;
        }

        $orderby = $query->get( 'orderby');
        if( 'expires' == $orderby ) {
            $query->set('meta_key','_wpc_expires');
            $query->set('orderby','meta_value_num');
        }

        if( 'stats' == $orderby ) {
            $query->set('meta_key','_wpc_used');
            $query->set('orderby','meta_value_num');
        }


    }

    function sortable_columns( $columns ) {
        $columns['expires'] = 'expires';
        $columns['stats']   = 'stats';

        //To make a column 'un-sortable' remove it from the array
        //unset($columns['date']);

        return $columns;
    }


    /**
     * Add more coupon columns
     * @since 1.0.0
     * @param $columns
     * @return mixed
     */
    function custom_edit_coupon_columns($columns) {

        $columns['coupon_type'] = esc_html__( 'Coupon', 'wp-coupon' );
        $columns['expires']     = esc_html__( 'Expires', 'wp-coupon' );
        $columns['stats']       = esc_html__( 'Votes / Clicks', 'wp-coupon' );


        //unset( $columns['author'] );
        // Move default columns to right
        if ( isset( $columns['comments']  ) ) {
            $title  =  $columns['comments'];
            unset( $columns['comments'] );
            $columns['comments']  = $title;
        }

        if ( isset( $columns['author']  ) ) {
            $title  =  $columns['author'];
            unset( $columns['author'] );
            $columns['author']  = $title;
        }

        if ( isset( $columns['date']  ) ) {
            $title  =  $columns['date'];
            unset( $columns['date'] );
            $columns['date']  = $title;
        }

        return $columns;
    }


    /**
     * Display coupon column data
     *
     * @since 1.0.0
     * @param $column
     * @param $post_id
     */
    function custom_coupon_column( $column, $post_id ) {
        wpcoupon_setup_coupon( $post_id );
        switch ( $column ) {
            case 'coupon_type' :

                if ( wpcoupon_coupon()->get_type() == 'code' ) {
                    if (  $code = wpcoupon_coupon()->get_code()  ) {
                        echo '<br/><code>'.esc_html( $code ).'</code>';
                    } else {
                        echo '<br/>'; esc_html_e( '[No Code]', 'wp-coupon' );
                    }
                } else {
                    echo strtoupper( wpcoupon_coupon()->get_coupon_type_text() );
                }
                break;
            case 'expires' :
                if ( wpcoupon_coupon()->has_expired() ) {
                    esc_html_e( 'Expired', 'wp-coupon' );
                    echo ' - ';
                    echo wpcoupon_coupon()->get_expires( get_option( 'date_format' ).' '.get_option( 'time_format' ), true );
                } else {
                    echo wpcoupon_coupon()->get_expires( get_option( 'date_format' ).' '.get_option( 'time_format' ) );
                }

                break;
            case 'stats' :
                echo '<span title="'.esc_attr__( 'Vote Up' ,'wp-coupon' ).'" style="color: #458b1b"><span class="dashicons dashicons-arrow-up"></span>' .wpcoupon_coupon()->_wpc_vote_up.'</span> ';
                echo '<span title="'.esc_attr__( 'Vote Down' ,'wp-coupon' ).'"  style="color: #fc702e"><span class="dashicons dashicons-arrow-down"></span>' . wpcoupon_coupon()->_wpc_vote_down .'</span> / ';
                echo '<span title="'.esc_attr__( 'Total Used' ,'wp-coupon' ).'" ><span class="dashicons dashicons-migrate"></span>' . wpcoupon_coupon()->get_total_used() .'</span>';
                break;

        }

    }


    public static function update_store_count( $post_ID , $post = null ){
        // Update coupon type count
        if ( ! $post ) {
            $post = get_post( $post_ID );
        }

        $post_type = get_post_type( $post ) ;
        if ( $post_type != 'coupon' ) {
            return ;
        }

        $types = apply_filters( 'store_count_coupons_types', array(
            'code',
            'sale',
            'print',
        ) );

        $ids =  false;
        $stores =  get_the_terms( $post_ID, 'coupon_store' );

        if ( $stores ) {
            $ids = wp_list_pluck( $stores, 'term_id');

            foreach ( $ids as $id ) {

                foreach ( $types as $c_type ) {
                    $args = array(
                        'post_type' => 'coupon',
                        'meta_key' => '_wpc_coupon_type',
                        'meta_value' => $c_type,
                        'meta_compare' => '=',
                        'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'coupon_store',
                                'field' => 'term_id',
                                'terms' => array($id),
                            ),
                        ),
                    );
                    $query = new WP_Query($args);
                    update_term_meta($id, '_wpc_coupon_' . $c_type, $query->found_posts);
                }
            }

        }

        update_post_meta( $post_ID, '_wpc_store', $ids );
    }

    function delete_coupon( $post_id ){
        // Update coupon type count
        $post = get_post( $post_id );
        $post_type = get_post_type( $post ) ;
        if ( $post_type != 'coupon' ) {
            return ;
        }

        $c_type = get_post_meta( $post->ID, '_wpc_coupon_type',  true );
        if ( ! $c_type ) {
            $c_type = 'code';
        }

        $stores =  update_post_meta(  $post->ID, '_wpc_store', true );
        if ( is_array( $stores ) ) {
            foreach ( $stores as $id ) {
                $n = intval( get_term_meta( $id, '_wpc_coupon_' . $c_type, true ) );
                if ( $n > 0 ) {
                    $n-=1;
                    update_term_meta( $id, '_wpc_coupon_' . $c_type, $n );
                }
            }
        }
    }


    /**
     * Update coupon count for store
     *
     * @param $post_ID
     * @param $post
     * @param null $update
     */
    public static function update_store_data( $post_ID, $post = null, $update = null  ){

        // Get post if not exists
        if ( ! $post ) {
            $post = get_post( $post_ID );
        }

        if ( 'coupon' != get_post_type( $post ) ) {
            return ;
        }

        /**
         * Update tracking data
         */
        $percent = get_post_meta( $post_ID, '_wpc_percent_success', true );
        if ( empty( $percent ) ||  $percent == '' ){
            update_post_meta( $post_ID, '_wpc_percent_success', 100 );
        }

        $meta = get_post_custom( $post_ID );

        if ( ! isset ( $meta[ '_wpc_used' ] ) ) {
            update_post_meta( $post_ID, '_wpc_used', 0 );
        }

        if ( ! isset ( $meta[ '_wpc_today' ] ) ) {
            update_post_meta( $post_ID, '_wpc_today', '' );
        }

        if ( ! isset ( $meta[ '_wpc_vote_up' ] ) ) {
            update_post_meta( $post_ID, '_wpc_vote_up', 0 );
        }

        if ( ! isset ( $meta[ '_wpc_vote_down' ] ) ) {
            update_post_meta( $post_ID, '_wpc_vote_down', 0 );
        }

        if ( ! isset ( $meta[ '_wpc_expires' ] ) ) {
            update_post_meta( $post_ID, '_wpc_expires', '' );
        }

    }

}

new WPCoupon_Coupon_Admin();


/**
 *
 * Open comment for single coupon
 *
 * @param $open
 * @param $post_id
 * @return bool
 */
function wpcoupon_open_coupon_comments( $open, $post_id ) {
    if ( get_post_type( $post_id ) == 'coupon' ) {
        $open = true;
    }
    return $open;
}

add_filter( 'comments_open', 'wpcoupon_open_coupon_comments', 25, 2 );


/**
 * Change coupon permalink
 */
add_filter( 'post_type_link', 'wpcoupon_coupon_link', 85, 3 );
function wpcoupon_coupon_link( $permalink, $post, $leavename = null ){
    if ( 'coupon' != get_post_type( $post ) ) {
        return $permalink;
    }

    $c = new WPCoupon_Coupon( $post );
    global $wp_rewrite;
    if ( $leavename ) {
        if (  $wp_rewrite->using_permalinks() ){
            $store_url = $c->get_store_url();
            if ( $store_url  ) {
                return trailingslashit( $store_url ).'%coupon%/';
            }

        }
        return $permalink;
    }

    if ( ! $wp_rewrite->using_permalinks() ){
        return $permalink;
    }


    return $c->get_href();
}
