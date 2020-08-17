<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_Coupons_Import {
    public $store = false;
    public $category = false;
    public $coupon = false;

    public $msg = array();

    function setup_fields( $setup = array() ){
        if ( $setup ) {
            $setup = wp_parse_args($setup, array(
                'coupon' => array(),
                'store' => array(),
                'category' => array(),
            ));

            $default_setup = WP_Coupon_IE::get_data_fields();
            $_SESSION['wp_coupons_import_settings'] = array();
            foreach ( $default_setup as $group_k => $group ) {
                $_SESSION['wp_coupons_import_settings'][ $group_k ] = array();
                foreach ( $group as $id => $field ) {
                    if ( isset( $setup[ $group_k ][ $id ] ) && $setup[ $group_k ][ $id ] ) {
                        $_SESSION['wp_coupons_import_settings'][$group_k][ $id ] = $setup[ $group_k ][ $id ];
                    } else {
                        $_SESSION['wp_coupons_import_settings'][$group_k][ $id ] = $field[ 'field' ];
                    }
                }
            }
        }
    }

    function get_setup( $group ){
        return $this->get_val( $_SESSION['wp_coupons_import_settings'], $group );
    }

    function setup_import_data( $row_data ){
        foreach ( $_SESSION['wp_coupons_import_settings'] as $key => $group ) {
            $data = array();
            $have_data = false;
            foreach ( $group as $k => $field ) {
                if ( isset ( $row_data[ $field ] ) ){
                    $data[ $k ] = $row_data[ $field ];
                    $have_data = true;
                } else {
                    $data[ $k ] = '';
                }
            }
            if ( $have_data ) {
                $this->{ $key } = $data;
            } else {
                $this->{ $key } = false;
            }
        }

    }

    /**
     * Download image form url
     *
     * @return bool
     */
     function download( $url, $parent_id = 0 ){
        if ( empty ( $url ) ) {
            return false;
        }
        // These files need to be included as dependencies when on the front end.
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Set variables for storage, fix file filename for query strings.
        preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches );
        $file_array = array();
        $file_array['name'] = basename( $matches[0] );

        // Download file to temp location.
        $file_array['tmp_name'] = download_url( $url );

        // If error storing temporarily, return the error.
        if ( is_wp_error( $file_array['tmp_name'] ) ) {
            //return $file_array['tmp_name'];
            return false;
        }

        // Do the validation and storage stuff.
        $id = media_handle_sideload( $file_array,$parent_id );

        // If error storing permanently, unlink.
        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
            return false;
        }

        return array(
            'id' => $id,
            'url' => wp_get_attachment_url( $id )
        );
    }

    function get_val( $data, $key, $default = false ){
        if ( is_array( $data ) ) {
            if (isset($data[$key])) {
                return $data[$key];
            } else {
                return $default;
            }
        } else if ( is_object( $data ) ) {
            if ( property_exists( $data, $key ) ) {
                return $data->{$key};
            } else {
                return $default;
            }
        }

        return $default;
    }

    function get_term( $search, $tax ){
        $t = false;
        if ( term_exists( $search, $tax) ) {
            if ( is_int( $search ) ) {
                $t = get_term_by('id', $search, $tax );
            }
            if ( ! $t ) {
                $t = get_term_by('slug', sanitize_title( $search ), $tax);
            }
            if ( ! $t ) {
                $t = get_term_by( 'name', $search, $tax );
            }
            return $t;
        }

        return false;
    }

    function save_coupon(){
        if ( $this->coupon ) {

            $author = $this->get_val( $this->coupon, 'author' );
            if ( $author ) {
                $author = get_user_by( 'email', $author );
                if ( $author ) {
                    $author = $author->ID;
                } else {
                    $author = false;
                }
            }

            $post_date = $this->get_val( $this->coupon, 'post_date' ) ? $this->get_val( $this->coupon, 'post_date' )  : date_i18n('Y-m-d H:i:s');
            if ( $post_date ) {
                $post_date = strtotime( $post_date );
                $post_date = date_i18n( 'Y-m-d H:i:s', $post_date );
            }

            $post_data = array(
                'post_title'    => $this->get_val( $this->coupon, 'post_title' ),
                'post_content'  => $this->get_val( $this->coupon, 'post_content' ),
                'post_excerpt'  => $this->get_val( $this->coupon, 'post_excerpt' ),
                'post_date'     => $post_date,
                'post_author'   => $author,
                'post_type'     => 'coupon',
                'post_status'   => $this->get_val( $this->coupon, 'post_status' )  ? $this->get_val( $this->coupon, 'post_status' ) : 'publish',
            );

            if ( ! $author ) {
                unset( $post_data['post_author'] );
            }

            $coupon_type = $this->get_val( $this->coupon, '_wpc_coupon_type' );
            $coupon_code = $this->get_val( $this->coupon, '_wpc_coupon_type_code' );
            if ( ! $coupon_code ) {
                $coupon_type = 'sale';
            }

            $is_update = false;

            $old_post = get_page_by_title( $post_data['post_title'], OBJECT, 'coupon' );
            if ( $old_post ) {
                if ( $old_post ) {
                    if ( get_post_meta( $old_post->ID, '_wpc_coupon_type', true ) == $coupon_type ) {
                        $post_data['ID'] = $old_post->ID;
                        $is_update = true;
                    } else {
                        $old_post = false;
                    }
                }
            }

            $post_data = array_map( 'wp_coupon_ie_remove_none_printable', $post_data );

            $post_id = wp_insert_post( $post_data );
            // if import coupon fail
            if ( ! $post_id || is_wp_error( $post_id ) ) {
                return false;
            }

            if ( $old_post ) {
                $this->msg['p-' . $post_id] = sprintf(esc_html__('Coupon: #%1$s - %2$s Updated.', 'wp_coupon_ie'), $post_id, $post_data['post_title']);
            } else {
                $this->msg['p-' . $post_id] = sprintf(esc_html__('Coupon: #%1$s - %2$s saved.', 'wp_coupon_ie'), $post_id, $post_data['post_title']);
            }
            $expires = $this->get_val( $this->coupon, '_wpc_expires' );
            if ( $expires ) {
                if ( ! is_int( $expires ) ) { // Check if is unix timestamp
                    $expires = strtotime( $expires );
                }
            }

            $coupon_meta = array(
                '_wpc_coupon_type'        => $coupon_type,
                '_wpc_expires'            => $expires,
                '_wpc_coupon_type_code'   => $coupon_code,
                '_wpc_destination_url'    => $this->get_val( $this->coupon, '_wpc_destination_url' ),
                '_wpc_exclusive'          => $this->get_val( $this->coupon, '_wpc_exclusive' ),
                '_wpc_coupon_save'        => $this->get_val( $this->coupon, '_wpc_coupon_save' ),
            );

            if ( $this->get_val( $this->coupon, 'printable_url' ) ) {
                // download image
                $image_data = $this->download( $this->get_val( $this->coupon, 'printable_url' ) );
                if ( $image_data ) {
                    $coupon_meta['_wpc_coupon_type_printable_id'] = $image_data['id'];
                    $coupon_meta['_wpc_coupon_type_printable'] = $image_data['url'];
                }
            }
            if ( $is_update ){
                $coupon_meta = array_filter( $coupon_meta );
            } else {
                $tracking_keys = array(
                    // tracking coupon
                    '_wpc_used'                      => 0,
                    '_wpc_percent_success'           => 100,
                    '_wpc_views'                     => 0,
                    '_wpc_today'                     => '' ,
                    '_wpc_vote_up'                   => 0,
                    '_wpc_vote_down'                 => 0,
                );
                foreach ( $tracking_keys as $k  => $v ) {
                    $_v = $this->get_val( $this->coupon, $k );
                    if ( ! $_v ) {
                        $_v = $v;
                    }
                    $coupon_meta[ $k ] = $_v;
                }
            }

            $coupon_meta = apply_filters( 'wp_import_coupon_meta_data', $coupon_meta, $this, $is_update );
            foreach ( (array) $coupon_meta as $k => $v ){
                update_post_meta( $post_id, $k, $v );
            }

            $coupon_taxs = array(
                'coupon_store' => $this->get_val( $this->coupon, 'store' ),
                'coupon_category' => $this->get_val( $this->coupon, 'category' ),
            );

           foreach ( $coupon_taxs as $tax_name => $tax ) {
               if ( $tax ) {
                   $_ts = explode(',', $tax);
                   $_ts = array_map('trim', $_ts);
                   $_ts = array_unique( $_ts );

                   if ( ! empty( $_ts ) ) {
                       $term_ids = array();
                       foreach ($_ts as $s) {
                           if ( term_exists( $s, $tax_name ) ) {
                               $t = $this->get_term( $s, $tax_name );
                               if ( $t ) {
                                   $term_ids[$t->term_id] = $t->term_id;
                               }
                           } else {
                               $t = wp_insert_term( $s, $tax_name, array(
                                  'description' => '',
                                  'slug' => sanitize_title( $s ),
                                  'parent'=> ''
                               ) );

                               if ( $t && ! is_wp_error( $t ) ){
                                   $t = ( object ) $t;
                                   $term_ids[ $t->term_id ] = $t->term_id;

                               }
                           }
                       }

                       if ( ! empty ( $term_ids ) ) {
                           $r = wp_set_post_terms( $post_id, $term_ids, $tax_name );
                           //var_dump( $r );
                       }

                   }
               }
           }

            if ( function_exists( 'wpcoupon_check_and_move_expires_coupon' ) ) {
                wpcoupon_check_and_move_expires_coupon( $post_id );
            }

            return $post_id;
        }
        return false;
    }

    function save_store(){
        $tax = 'coupon_store';
        if ( $this->store ) {
            $name = $this->get_val( $this->store, 'name' );
            if ( ! $name ) {
                $name = $this->get_val( $this->store, 'slug' );
            }
            // check the term may insert when save coupon
            $t = $this->get_term( $name, $tax );

            $parent = $this->get_val($this->store, 'parent');
            $parent_id = 0;
            $term_id = 0;
            if ( $parent ) {
                $p = $this->get_term($parent, $tax);
                if ( $p ) {
                    $parent_id = $t->term_id;
                }
            }

            $t_des = wp_kses_post( $this->get_val($this->store, 'description') );

            $t_data = array(
                'name' => $name,
                'slug' => sanitize_title($this->get_val($this->store, 'slug')),
                'parent' => $parent_id
            );
            if ( $t_des ) {
                // Remove non print able
                $t_data['description'] = $t_des;
            }

            $t_data = array_map( 'wp_coupon_ie_remove_none_printable', $t_data );

            $is_update = false;

            if ( $t ) {
                $term_id =  $t->term_id;
                wp_update_term( $term_id , $tax, $t_data );
                $is_update = true;
            } else {

                $t = wp_insert_term($name, $tax, $t_data );
                if ( $t && ! is_wp_error( $t ) ) {
                    $t = ( object ) $t;
                    $term_id =  $t->term_id;
                }
            }

            if ( ! $term_id || is_wp_error( $term_id ) ) {
                return false;
            }

            if ( $is_update ) {
                $this->msg['t-'. $term_id ] = sprintf( esc_html__( 'Store: #%1$s - %2$s updated.', 'wp_coupon_ie' ), $term_id, $name );
            } else {
                $this->msg['t-'. $term_id ] = sprintf( esc_html__( 'Store: #%1$s - %2$s saved.', 'wp_coupon_ie' ), $term_id, $name );
            }

            $meta_data = array(
                '_wpc_store_url'       => $this->get_val( $this->store, 'store_url' ),
                '_wpc_store_aff_url'   => $this->get_val( $this->store, 'store_aff_url' ),
                '_wpc_store_name'      => '',
                '_wpc_store_heading'   => $this->get_val( $this->store, 'store_heading' ),
                '_wpc_is_featured'     => $this->get_val( $this->store, 'store_heading' ) ? 'on' : '',
                '_wpc_count_posts'     => 0,
                '_wpc_extra_info'      => $this->get_val( $this->store, 'extra_info' ),
                '_wpc_store_image_id'  => '',
                '_wpc_store_image'     => '',

            );

            $meta_data = array_map( 'wp_coupon_ie_remove_none_printable', $meta_data );

            $image_url = $this->get_val( $this->store, 'store_image' );
            if ( $image_url ) {
                $image_data = $this->download( $image_url );
                if ( $image_data ) {
                    $meta_data['_wpc_store_image_id'] = $image_data['id'];
                    $meta_data['_wpc_store_image'] = $image_data['url'];
                }
            }

            if ( function_exists( 'update_term_meta' ) ) {
                if ( $is_update ) {
                    $meta_data = array_filter( $meta_data );
                }
                // Hook for 3rd party
                $meta_data = apply_filters( 'wpcoupon_csv_import_store_metadata', $meta_data, $this->store, $is_update, $this );
                foreach ( $meta_data as $k => $v ) {
                    update_term_meta( $term_id, $k, $v );
                }

            }

            return $term_id;
        }
        return false;
    }


    function save_category(){
        $tax = 'coupon_category';
        if ( $this->category ) {
            $name = $this->get_val( $this->category, 'name' );
            if ( ! $name ) {
                $name = $this->get_val( $this->category, 'slug' );
            }

            // check the term may insert when save coupon
            $t = $this->get_term( $name, $tax );

            $parent = $this->get_val( $this->category, 'parent');
            $parent_id = 0;
            $term_id = 0;
            if ( $parent ) {
                $p = $this->get_term($parent, $tax);
                if ( $p ) {
                    $parent_id = $p->term_id;
                }
            }

            $t_des = $this->get_val($this->category, 'description');
            $t_data = array(
                'name' => $name,
                'slug' => sanitize_title($this->get_val($this->category, 'slug')),
                'parent' => $parent_id
            );
            if ( $t_des ) {
                $t_data['description'] = $t_des;
            }

            $t_data = array_map( 'wp_coupon_ie_remove_none_printable', $t_data );

            $is_update = false;

            if ( $t ) {
                $term_id =  $t->term_id;
                wp_update_term( $term_id , $tax, $t_data );
                $is_update = true;
            } else {

                $t = wp_insert_term( $name, $tax, $t_data );
                if ( $t && ! is_wp_error( $t ) ) {
                    $t = ( object ) $t;
                    $term_id =  $t->term_id;
                }
            }

            if ( ! $term_id || is_wp_error( $term_id ) ) {
                return false;
            }
            if ( $is_update ) {
                $this->msg['t-'. $term_id ] = sprintf( esc_html__( 'Category: #%1$s - %2$s updated.', 'wp_coupon_ie' ), $term_id, $name );
            } else {
                $this->msg['t-'. $term_id ] = sprintf( esc_html__( 'Category: #%1$s - %2$s saved.', 'wp_coupon_ie' ), $term_id, $name );
            }

            $meta_data = array(
                '_wpc_icon'             => $this->get_val( $this->category, 'icon' ),
                '_wpc_cat_image_id'     => '',
                '_wpc_cat_image'        => '',
            );

            $image_url = $this->get_val( $this->category, 'cat_image' );
            if ( $image_url ) {
                $image_data = $this->download( $image_url );
                if ( $image_data ) {
                    $meta_data['_wpc_cat_image_id'] = $image_data['id'];
                    $meta_data['_wpc_cat_image'] = $image_data['url'];
                }
            }

            $meta_data = array_map( 'wp_coupon_ie_remove_none_printable', $meta_data );

            if ( function_exists( 'update_term_meta' ) ) {
                if ( $is_update ) {
                    $meta_data = array_filter( $meta_data );
                }
                // Hook for 3rd party
                $meta_data = apply_filters( 'wpcoupon_csv_import_category_metadata', $meta_data, $this->category, $is_update, $this );
                foreach ( $meta_data as $k => $v ) {
                    update_term_meta( $term_id, $k, $v );
                }
            }

            return $term_id;
        }
        return false;
    }

    function save(){
        $return = array(
            'save'          => false,
            'save_coupon'   => false,
            'save_category' => false,
            'msg'           => array(),
        );
        if ( $this->save_coupon() ){
            $return['save'] = true;
            $return['save_coupon'] = true;
        }

        if ( $this->save_store() ){
            $return['save'] = true;
            $return['save_store'] = true;
        }

        if ( $this->save_category() ){
            $return['save'] = true;
            $return['save_category'] = true;
        }

        return $return;
    }

}