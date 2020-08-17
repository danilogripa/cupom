<?php
class  WPCoupon_Search{

    function __construct() {
    	if ( ! is_admin()  ) {
		    add_action( 'pre_get_posts', array( $this, 'init' ) );
	    }
    }

    function init(){
	    global $wp_query;
        if ( is_search() && ! is_admin() ) {
            if (  wpcoupon_get_option( 'search_only_coupons', 1 ) ) {
                set_query_var( 'post_type', 'coupon' );
            } else {
	            $post_in = get_query_var( 'post_type' );
	            $enable_single = wpcoupon_get_option( 'enable_single_coupon', false );
	            if ( ! $enable_single && $post_in == 'any' ) {
		            $args = array(
			            'public'   => true,
		            );
		            $output = 'names'; // names or objects, note names is the default
		            $operator = 'and'; // 'and' or 'or'
		            $post_types = get_post_types( $args, $output, $operator );
		            $post_types['coupon'] = 'coupon';
		            set_query_var( 'post_type', $post_types );
	            }
            }
        }

        if ( is_search() || ( isset( $_REQUEST['c_s_store'] ) && $_REQUEST['c_s_store'] != '' ) ) {

            add_filter('posts_where', array($this, 'where'));
            add_filter('posts_join', array($this, 'join'));
            add_filter('posts_groupby', array($this, 'groupby'));
            if ( ! is_admin() ) {
                add_filter('posts_orderby', array($this, 'orderby'));
                add_filter('posts_distinct',  array( $this, 'distinct' ) );
            }

        }
    }

    function where($where){
        global $wpdb;
        $s = '';
        $op = 'OR';
        if ( is_admin() ) {
            if ( isset( $_REQUEST['c_s_store'] ) && $_REQUEST['c_s_store'] != '') {
                $s = esc_sql( $_REQUEST['c_s_store'] );
                $op = 'AND';
            }
        } else {
            $s = esc_sql( get_search_query() );
        }
        $s = trim( $s );
        if ( $s ) {
            $where .= "{$op} (t.name LIKE '%".$s. "%' AND tt.taxonomy IN ( 'coupon_store', 'coupon_category' ) AND {$wpdb->posts}.post_status = 'publish' )";
        }

        return $where;
    }

    function join($join){
        global $wpdb;

        $join = "LEFT JOIN {$wpdb->term_relationships} AS tr ON {$wpdb->posts}.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id=tr.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} AS t ON t.term_id = tt.term_id";

        return $join;
    }

    function groupby($groupby){
        global $wpdb;

        $groupby = "{$wpdb->posts}.ID";

        return $groupby;
    }

    function orderby( $orderby ){
        global $wpdb;
        $orderby = " t.name ASC, {$wpdb->posts}.post_title ";
        return $orderby;
    }

    function distinct( $distinct ){
        return $distinct;
    }


}

new WPCoupon_Search();




