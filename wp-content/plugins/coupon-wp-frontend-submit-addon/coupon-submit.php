<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPCoupon_Front_Coupon_Edit {

    /**
     * List error codes
     * @var array
     */
    private $errors = array();

    /**
     * @see wp_insert_post()
     *
     * An array representing the elements that make up a post.
     * There is a one-to-one relationship between these elements and the names of columns in the wp_posts table in the database.
     *
     * @var array
     */
    private $post_data = array();

    /**
     * Array post meta
     *
     * @var array
     */
    private $post_meta = array();

    /**
     * Array post terms
     *
     * @var array
     */
    private $store = array();

    /**
     * Array post terms
     *
     * @var array
     */
    private $post_terms = array();

    /**
     * Image type
     *
     * Can be upload if user select upload file, url if user enter a url
     *
     * @var string
     */
    private $image_type;

    /**
     * The url of image th
     * @var string
     */
    private $download_image_url;
    /**
     * image url after uploaded/downloaded image
     * @var string
     */
    private $image_url;
    /**
     * Attachment ID when uploaded/downloaded
     * @var int
     */
    private $image_id;

    /**
     * Coupon ID
     *
     * The ID return affer save coupon
     *
     * @var
     */
    private $coupon_id;

    /**
     * Flag is edit or not
     *
     * @var bool
     */
    private $is_edit = false;

    /**
     * Object of prev coupon before save
     * @see wpcoupon_coupon()
     * @var bool|object
     */
    private  $prev_coupon = false;

    function __construct() {

    }

    public static function errors(  ){
         $error_codes = array(
             'title'        => esc_html__( 'Please enter coupon title.', 'wp-coupon-submit' ),
             'content'      => esc_html__( 'Please enter coupon description.', 'wp-coupon-submit' ),
             'store'        => esc_html__( 'Please select a store.', 'wp-coupon-submit' ),
             'cat'          => esc_html__( 'Please select a category.', 'wp-coupon-submit' ),
             'no_store'     => esc_html__( 'Store does not exists please select other.', 'wp-coupon-submit' ),
             'type'         => esc_html__( 'Please select coupon type.', 'wp-coupon-submit' ),
             'image'        => esc_html__( 'Please enter image url.', 'wp-coupon-submit' ),
             'upload'       => esc_html__( 'Please select a image to upload', 'wp-coupon-submit' ),
             'agree'        => esc_html__( 'Please agree to the Terms and Conditions before submit.', 'wp-coupon-submit' ),
             'expires'      => esc_html__( 'Please enter coupon expires. If you don\'t know expires let check Don\'t know the expiration date.', 'wp-coupon-submit' ),
             'code'         => esc_html__( 'Please enter coupon code.', 'wp-coupon-submit' ),
             'permission'   => esc_html__( 'You have not permission to edit this coupon', 'wp-coupon-submit' ),
             'unknown'      => esc_html__( 'An error occurred please try again later', 'wp-coupon-submit' ),
         );
        return $error_codes;
    }

    /**
     * Get errors by code
     *
     * @param $code
     * @return bool
     */
    public static function get_error( $code ) {
        $errors = self::errors();
        if ( isset( $errors[ $code ] ) ) {
            return $errors[ $code ];
        }  else {
            return false;
        }
    }

    /**
     * Save coupon
     */
    public function save(){
        $this->get_data();
        $is_save = false ;

        $return_url = isset( $_POST['_wp_original_http_referer'] ) ? $_POST['_wp_original_http_referer'] : home_url('/');


        // do not count coupon type in this hook becau term do not exist.
        remove_action( "wp_insert_post",  array( 'WPCoupon_Coupon_Admin', 'update_store_data' ), 96, 3 );
        remove_action( "wp_insert_post",  array( 'WPCoupon_Coupon_Admin', 'update_store_count' ), 97, 2 );

        if ( empty( $this->errors ) ) {
            // insert new coupon
            if ( ! isset( $this->post_data['ID'] ) ||  $this->post_data['ID'] == '' ) {
                $post_id =  wp_insert_post( $this->post_data );
                // if insert success
                if ( ! is_wp_error( $post_id ) ) {
                    $is_save =  true;
                    $this->coupon_id = $post_id;
                    $this->save_media();
                    $this->maybe_new_store_logo();
                    $this->save_terms();

                    foreach ( $this->post_meta as $k => $v ) {
                        update_post_meta( $post_id, $k , $v );
                    }

                    do_action( 'after_frontend_coupon_submitted', $post_id );
                }

            } else { // is update post
                $post_id =  wp_update_post( $this->post_data , true ) ;
                if ( ! is_wp_error( $post_id ) ) {
                    $is_save =  true;
                    $this->coupon_id = $post_id;
                    $this->save_media();
                    $this->save_terms();

                    if ( $this->post_meta['_wpc_coupon_type_printable_id'] == '' ){
                        unset( $this->post_meta['_wpc_coupon_type_printable_id'] );
                    }
                    if ( $this->post_meta['_wpc_coupon_type_printable'] == '' ){
                        unset( $this->post_meta['_wpc_coupon_type_printable'] );
                    }

                    foreach ( $this->post_meta as $k => $v ) {
                        update_post_meta( $post_id, $k , $v );
                    }

                    do_action( 'after_frontend_coupon_submitted', $post_id );

                }
            }
        }

        // Delete current output buffer
        ob_start();
        ob_get_clean();
        ob_start();

        // if is ajax
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            if ( $is_save ) {
                $_REQUEST['coupon_save'] = 1;
                $_REQUEST['c_status'] = $this->post_data['post_status'];
            } else {
                if ( empty ( $this->errors ) ) {
                    $this->make_it_error('unknown');
                }
                $_REQUEST['errors'] = $this->errors;
            }

            WPCoupon_Coupon_Submit_ShortCode::message();
            $content = ob_get_clean();

            $response = array(
                'success'   => true,
                'data'      => $content,
                'meta'      => $this->post_meta,
                'image_url' => $this->image_url
            );
            if (  $is_save ) {
                $response['coupon_id'] = $this->coupon_id;
            } else {
                $response['success']   = false;
                $response['coupon_id'] = 0;
            }
            wp_send_json( $response );
            die();
        }

        $return_url = remove_query_arg( array( 'coupon_save', 'c_status', 'errors' ) , $return_url );
        if ( $is_save ) {
            $return_url = add_query_arg(
                array( 'coupon_save' => 1, 'c_status' => $this->post_data['post_status'] ) ,
                $return_url
            );
        } else {
            if ( empty ( $this->errors ) ) {
               $this->make_it_error('unknown');
            }
            $return_url = add_query_arg(
                array( 'errors' =>  join(',', $this->errors ) ) ,
                $return_url
            );
        }

        wp_redirect( $return_url );
        die();

    }

    function save_terms(){
        //$term_taxonomy_ids = wp_set_object_terms( 42, $cat_ids, 'category' );
        if ( ! empty ( $this->post_terms ) ) {
            foreach ( $this->post_terms as $tax => $term_ids ) {
                wp_set_object_terms( $this->coupon_id , $term_ids, $tax );
            }
        }
    }


    public function is_error( $error_code ){
        return isset( $this->errors[ $error_code ] );
    }
    /**
     * Set error
     *
     * @param $error_code
     */
    public function make_it_error( $error_code ){
        $this->errors[ $error_code ] = $error_code;
    }

    /**
     * Remove error
     *
     * @param $error_code
     */
    public function make_it_ok( $error_code ){
        if ( isset ( $this->errors[ $error_code ] ) ) {
            unset( $this->errors[ $error_code ] );
        }
    }

    /**
     * Get data when users submit
     */
    public function get_data(){

        $post_data = array(
            'post_title' => '',
            'post_content' => '',
            'post_date' => date_i18n('Y-m-d H:i:s'),
            'post_author' => '',
            'post_type' => 'coupon',
            'post_status' => 'pending',
        );

        // Check coupon title
        if ( isset ( $_POST['coupon_title'] )  && $_POST['coupon_title'] != '' ) {
            $post_data['post_title'] = $_POST['coupon_title'];
        } else {
            $this->make_it_error( 'title' );
        }
        // Check coupon content
        if ( isset ( $_POST['coupon_description'] ) && $_POST['coupon_description'] != '' ) {
            $post_data['post_content'] = esc_html( $_POST['coupon_description'] );
        } else {
            $this->make_it_error( 'content' );
        }
        // check who submit
        $option_status = wpcoupon_get_option( 'cs_coupon_status', 'default' );
        if ( $option_status == 'default' ) {
            $is_admin = false;
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                $allowed_roles = apply_filters('allowed_roles_edit_coupon', array('editor', 'administrator', 'author'));
                if (array_intersect($allowed_roles, $user->roles)) {
                    //auto publish coupon if user is admin
                    $post_data['post_status'] = 'publish';
                    $is_admin = true;
                }
                $post_data['post_author'] = $user->ID;
            }
        } else { // custom status
            $post_data['post_status'] = $option_status;
            if ( is_user_logged_in() ) {
                $user = wp_get_current_user();
                $post_data['post_author'] = $user->ID;
            }
        }

        // check coupons ID
        if ( isset( $_POST['coupon_id'] )  && intval( $_POST['coupon_id'] ) > 0 ) {
            $coupon_id = intval( $_POST['coupon_id'] );
            $coupon =  get_post( $coupon_id );
            if ( $coupon && $coupon->post_type == 'coupon' ) {
                // Keep coupon status
                $post_meta['post_status'] = $coupon->post_status;
                $this->prev_coupon  = wpcoupon_coupon( $coupon );
                /**
                 * Check who can edit coupon
                 * Only admin and person who submit this coupon can edit
                 */
                if ( is_user_logged_in() ) {
                    if ( $is_admin ||  $coupon->post_author ==  $user->ID ) {
                        $post_data['ID'] = $coupon_id;
                        $this->is_edit = true;
                        $post_data['post_status'] = 'publish';
                    } else {
                        $this->make_it_error( 'permission' );
                    }
                } else {
                    $this->make_it_error( 'permission' );
                }
            }
        }
        // default meta keys
        $post_meta = array(
            '_wpc_store'                     => '',
            '_wpc_coupon_type'               => '',
            //'start_datetime'                => '',
            '_wpc_expires'                   => '',
            '_wpc_coupon_type_code'          => '',
            //'_wpc_coupon_type_sale'          => '',
            '_wpc_coupon_type_printable_id'  => '',
            '_wpc_coupon_type_printable'     => '',
            '_wpc_destination_url'           => '',
        );


        // Check store
        if ( isset( $_POST['coupon_store'] ) && intval( $_POST['coupon_store'] ) > 0 ) {
            $store_id =  intval( $_POST['coupon_store'] );
            $_term = get_term( $store_id, 'coupon_store' );
            // Check if store exists
            if ( $_term  && ! is_wp_error( $_term )) {
                $post_meta['_wpc_store'] = $store_id;
                $this->post_terms['coupon_store'] = $store_id;
            } else {
                $this->make_it_error( 'no_store' );
            }
        } else {
            $this->make_it_error( 'store' );
        }

        // Check new store
        if ( wpcoupon_get_option( 'cs_enable_new_store', true ) ) {
            if ($this->is_error('store') || $this->is_error('no_store')) {
                $new_store_name = isset($_POST['new_store_name']) ? $_POST['new_store_name'] : false;
                $new_store_name = trim($new_store_name);
                if (!$new_store_name) {
                    $this->make_it_error('no_store');
                } else {
                    $this->make_it_ok('no_store');
                    $this->make_it_ok('store');
                }
                $new_store_url = isset($_POST['new_store_url']) ? $_POST['new_store_url'] : false;
                $new_store_url = trim($new_store_url);
                if (!$new_store_url) {
                    $this->make_it_error('no_store');
                } else {
                    $this->make_it_ok('no_store');
                    $this->make_it_ok('store');
                }

                $new_store_url = esc_url($new_store_url);
                $post_meta['_wpc_new_store_info'] = array(
                    'name' => $new_store_name,
                    'url' => $new_store_url,
                    'logo_id' => 0,
                );
            }
        }

        // check category
        $cat_id = isset( $_POST['coupon_cat'] ) ?  absint( $_POST['coupon_cat'] ): 0;
        $_term = get_term( $cat_id, 'coupon_category' );
        if ( $cat_id <= 0 || ! $_term || is_wp_error( $_term ) ) {
            $this->make_it_error( 'cat' );
        } else {
            $this->post_terms['coupon_category'] = $cat_id;
        }


        // check coupon type
        if ( isset ( $_POST['coupon_type'] ) && $_POST['coupon_type'] != '' ) {
            $post_meta['_wpc_coupon_type'] =  sanitize_title( $_POST['coupon_type'] );
        } else {
            $post_meta['_wpc_coupon_type'] = 'code';
            $this->make_it_error('type');
        }

        // Check Coupon code
        if ( $post_meta['_wpc_coupon_type'] == 'code' ) {
            if ( isset(  $_POST['coupon_code'] ) ) {
                $post_meta['_wpc_coupon_type_code'] = esc_html( $_POST['coupon_code'] );
            } else {
                $this->make_it_error( 'code' );
            }
        }

        // Check image type
        $this->image_type =  isset( $_POST['coupon_image_type'] ) ? (string) $_POST['coupon_image_type']  : 'url';
        $this->image_type =  strtolower( $this->image_type );

        if ( $this->image_type == 'upload' ) {
            // check file selected
            if ( isset( $_FILES['coupon_image_file'] ) && $_FILES['coupon_image_file']['name'] == '' ) {
                $this->make_it_error( 'upload' );
            }
        } else if ( $this->image_type == 'url' ) {
            // check if enter image url
            if ( isset ( $_POST['coupon_image_url'] ) && $_POST['coupon_image_url'] != '' ) {
                $this->download_image_url = $_POST['coupon_image_url'];
            } else {
                $this->make_it_error( 'image' );
            }
        }
        // if is edit coupon and use not upload the image or enter new url
        if ( $this->is_edit ) {
            $this->make_it_ok('image');
            $this->make_it_ok('upload');
        }

        // request media if is print coupon
        if ( $post_meta['_wpc_coupon_type'] != 'print' ) {
            $this->make_it_ok('image');
            $this->make_it_ok('upload');
        }

        // Check coupon expires
        $date =  isset( $_POST['coupon_expires'] ) ?  $_POST['coupon_expires'] : '';
        $is_unknown_date = isset( $_POST['coupon_expires_unknown'] ) && $_POST['coupon_expires_unknown'] == 1  ? true : false;
        if ( ! $is_unknown_date ) {
            if ( preg_match( "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date ) )
            {
                $post_meta['_wpc_expires'] = strtotime( $date );
            }else{
                $this->make_it_error( 'expires' );
            }
        } else {
            $post_meta['_wpc_expires'] = '';
        }

        if ( isset( $_POST['coupon_aff_url'] ) ){
            $post_meta['_wpc_destination_url'] = esc_attr( $_POST['coupon_aff_url'] );
        }

        $this->post_data =  apply_filters( 'wpcoupon_submit_coupon_data', $post_data );
        $this->post_meta =  apply_filters( 'wpcoupon_submit_coupon_meta', $post_meta );

        $_filter_data = array(
            'post_data' => $this->post_data,
            'post_meta' => $this->post_meta,
            'errors' => $this->errors,
        );

        $_filter_data = apply_filters( 'wpcoupon_submit_coupon_meta_data', $_filter_data , $this );
        $this->post_data = $_filter_data['post_data'];
        $this->post_meta = $_filter_data['post_meta'];
        $this->errors    = $_filter_data['errors'];
    }

    /**
     * Handle media
     *
     * @return bool
     */
    private function save_media(){
        if ( $this->image_type == 'upload' ) {
            $r = $this->upload();
        } else {
            $r = $this->download();
        }

        if ( $r ) {
            /**
             * If use upload, download new image then remove old image attachment
             *
             */
            if ( $this->is_edit ) {
                if ( $this->prev_coupon->get_type() == 'print' ) {
                    $attachment_id = get_post_meta( $this->prev_coupon->ID, '_wpc_coupon_type_printable_id' , true );
                } else {
                    $attachment_id = get_post_meta( $this->prev_coupon->ID, '_thumbnail_id' , true );
                }

                if ( $attachment_id > 0 ) {
                    $attachment =  get_post( $attachment_id );
                    // Only delete attachment if image attached to this coupon
                    if ( $attachment && $attachment->post_parent == $this->prev_coupon->ID ) {
                        wp_delete_attachment( $attachment_id , true );
                    }
                }
            }

            /**
             * Set new print image or set thumbnail
             */
            if ( $this->post_meta['_wpc_coupon_type'] == 'print' ) {
                $this->post_meta['_wpc_coupon_type_printable_id'] = $this->image_id;
                $this->post_meta['_wpc_coupon_type_printable'] = $this->image_url;
            } else {
                set_post_thumbnail( $this->coupon_id, $this->image_id );
            }

        } else {
            if ( $this->is_edit ) {
                // if no image upload ,download and user switch coupon type from print to other types
                if ( $this->prev_coupon->get_type() == 'print' && $this->post_data['_wpc_coupon_type'] != 'print' ) {
                    // switch printable image to thumbnail
                    if ( $this->prev_coupon->_wpc_coupon_type_printable_id > 0 ) {
                        set_post_thumbnail( $this->coupon_id, $this->prev_coupon->_wpc_coupon_type_printable_id );
                    }
                } else if ( $this->prev_coupon->get_type() != 'print' && $this->post_data['_wpc_coupon_type'] == 'print' ) {
                    // if  user switch coupon type from other types to print
                    if ( $this->prev_coupon->_thumbnail_id > 0 ) {
                        $this->post_meta['_wpc_coupon_type_printable_id'] = $this->prev_coupon->_thumbnail_id;
                        $this->post_meta['_wpc_coupon_type_printable'] = wp_get_attachment_url( $this->prev_coupon->_thumbnail_id );
                    }
                }
            }
        }
        return $r;
    }

    /**
     * Download image form url
     *
     * @return bool
     */
    private function download(){
        if ( empty ( $this->download_image_url ) ) {
            return false;
        }
        // These files need to be included as dependencies when on the front end.
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Set variables for storage, fix file filename for query strings.
        preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $this->download_image_url, $matches );
        $file_array = array();
        $file_array['name'] = basename( $matches[0] );

        // Download file to temp location.
        $file_array['tmp_name'] = download_url( $this->download_image_url );

        // If error storing temporarily, return the error.
        if ( is_wp_error( $file_array['tmp_name'] ) ) {
            //return $file_array['tmp_name'];
            return false;
        }

        // Do the validation and storage stuff.
        $id = media_handle_sideload( $file_array, $this->coupon_id );

        // If error storing permanently, unlink.
        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
            return false;
        }

        $this->image_id = $id;
        $this->image_url = wp_get_attachment_url( $id );

        return true;
    }

    /**
     * Upload image form local
     *
     * @return bool
     */
    private function upload( $input_file_name = 'coupon_image_file' ){

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $uploadedfile = $_FILES[ $input_file_name ];

        $upload_overrides = array( 'test_form' => false );

        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && !isset( $movefile['error'] ) ) {
            //echo "File is valid, and was successfully uploaded.\n";
            $this->image_url =  $movefile['url'];
            $this->image_id = wp_insert_attachment(
                array(
                    'post_mime_type' => $movefile['type'],
                    'guid'           => $movefile['url'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['url'] ) ),
                    'post_parent'    => $this->coupon_id,
                ),
                $movefile['file'],
                $this->coupon_id
            );

            wp_update_attachment_metadata( $this->image_id, wp_generate_attachment_metadata( $this->image_id, $movefile['file'] ) );

            return true;
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            return false;
        }
    }


    /**
     * Upload image form local
     *
     * @return bool
     */
    private function maybe_new_store_logo(){
        if ( ! wpcoupon_get_option( 'cs_enable_store_logo', true ) ) {
            return;
        }

        if ( ! isset( $_FILES[ 'coupon_store_file' ] ) ) {
            return;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $uploadedfile = $_FILES[ 'coupon_store_file' ];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && !isset( $movefile['error'] ) ) {
            //echo "File is valid, and was successfully uploaded.\n";
            $image_id = wp_insert_attachment(
                array(
                    'post_mime_type' => $movefile['type'],
                    'guid'           => $movefile['url'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['url'] ) ),
                    'post_parent'    => $this->coupon_id,
                ),
                $movefile['file'],
                $this->coupon_id
            );

            wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $movefile['file'] ) );

            if ( isset( $this->post_meta['_wpc_new_store_info'] ) ){
                $this->post_meta['_wpc_new_store_info']['logo_id'] = $image_id;
            }

        }
    }

}

function wpcoupon_front_coupon_edit(){
    if ( isset( $_POST['_submit_coupon'] ) && $_POST['_submit_coupon'] == 1 ) {
        $c = new WPCoupon_Front_Coupon_Edit();
        $c->save();
    }
}
add_action( 'init', 'WPCoupon_Front_Coupon_Edit' );


/**
 * Update coupon store when user submitted
 *
 * @param $post_id
 */
function wpcoupon_submit_update_store( $post_id ){
    $post = get_post( $post_id );
    if ( ! $post ) {
        return;
    }

    if ( 'publish' == $post->post_status ) {

        $new_store_info = get_post_meta( $post->ID, '_wpc_new_store_info', true );
        if ( ! empty( $new_store_info ) ) {

            $new_store_info = wp_parse_args( $new_store_info, array(
                'name'      => '',
                'url'       => '',
                'logo_id'   => '',
            ) );
            // coupon_store
            if ( $new_store_info['name'] && $new_store_info['url'] ) {
                $term_id = false;
                // Ensure not duplicate store
                $test_term = get_term_by( 'name', $new_store_info['name'], 'coupon_store' );
                if ( ! $test_term ) {
                    $r = wp_insert_term(
                        $new_store_info['name'], // the term
                        'coupon_store', // the taxonomy
                        array()
                    );
                    if ( ! is_wp_error( $r ) ) {
                        $term_id = $r['term_id'];
                        update_term_meta( $term_id, '_wpc_store_url', $new_store_info['url'] );
                        update_term_meta( $term_id , '_wpc_store_image_id', $new_store_info['logo_id'] );
                    }
                } else {
                    $term_id = $test_term->term_id;
                }

                if ( $term_id ) {
                    wp_set_object_terms( $post->ID, $term_id, 'coupon_store' );
                }
            }
            delete_post_meta( $post->ID, '_wpc_new_store_info' );
        }

    }

}
add_action( 'after_frontend_coupon_submitted', 'wpcoupon_submit_update_store' );
add_action( 'save_post_coupon', 'wpcoupon_submit_update_store' );

/**
 * Ajax for submit coupon
 */
add_action( 'wp_ajax_submit_coupon', 'wpcoupon_front_coupon_edit' );
add_action( 'wp_ajax_nopriv_submit_coupon', 'wpcoupon_front_coupon_edit' );



class WPCoupon_Coupon_Submit_ShortCode {

    public static function message(){
        // Success message
        if ( isset( $_REQUEST['coupon_save'] ) ) {
            ?>
            <div class="st-response-msg ui success message">
                <i class="close icon"></i>
                <div class="header">
                    <?php esc_html_e( 'Your coupon has been saved.', 'wp-coupon-submit' ); ?>
                </div>
                <?php if ( isset ( $_REQUEST['c_status'] ) ) { ?>
                    <p>
                        <?php
                        if ( $_REQUEST['c_status'] == 'publish' ) {
                            esc_html_e( 'Your coupon published', 'wp-coupon-submit' );
                        } else {
                            esc_html_e( 'Your coupon is pending review ', 'wp-coupon-submit' );
                        } ?>
                    </p>
                <?php } ?>
            </div>
        <?php
        }
        // error message
        if ( isset ( $_REQUEST['errors'] ) ) {

            if ( ! is_array( $_REQUEST['errors'] ) ){
                $errors =  ( string ) $_REQUEST['errors'];
                $errors = explode( ',', $errors );
            } else {
                $errors = $_REQUEST['errors'];
            }
            ?>
            <div class="st-response-msg ui error message">
                <i class="close icon"></i>
                <div class="header">
                    <?php esc_html_e( 'There was some errors with your submission', 'wp-coupon-submit' ); ?>
                </div>
                <ul class="list">
                    <?php
                    foreach ( $errors as $k ) {
                        $error =  WPCoupon_Front_Coupon_Edit::get_error( $k ) ;
                        if ( $error ) {
                            echo '<li>'.$error.'</li>';
                        }
                    } ?>
                </ul>
            </div>
        <?php
        }
    }


    /**
     *
     * Front end submit
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    public static function submit_coupon_form( $atts = array(), $content = ""  ){
        // the url of current add-ond

        $url = WP_COUPON_SUBMIT_URL;
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-datetimepicker', $url.'js/jquery-ui-timepicker-addon.min.js', array( 'jquery' ) , '1.0.0',  true );
        wp_enqueue_script( 'jquery-ui-datetimepicker' );
        wp_enqueue_script( 'st-submit-coupon', $url.'js/coupon-submit.js', array( 'wpcoupon_global' ) );
        wp_enqueue_style( 'coupon-submit', $url.'css/coupon-submit.css' );
        wp_localize_script( 'st-submit-coupon', 'coupon_submit', array(
            'still_not_found'   =>  esc_html__( 'Still not found your store ?', 'wp-coupon-submit' ),
            'not_found'         =>  esc_html__( 'Oops! That store can&rsquo;t be found.', 'wp-coupon-submit' ),
            'new_store_logo'    => wpcoupon_get_option( 'cs_enable_store_logo', true ),
            'new_store'         => wpcoupon_get_option( 'cs_enable_new_store', true ),
            'who_can_submit'    => wpcoupon_get_option( 'cs_who_can_submit', 'anyone' ),
            'login_notice'      => esc_html__( 'Your must login to submit coupon', 'wp-coupon-submit' ),
            'is_logged_in'      => is_user_logged_in(),
        ) );
        // check if
        $single_store = false;
        $term = false;
        if ( is_tax() ){
            $term = get_queried_object();
            if ( $term ) {
                $single_store = $term->term_id;
            }
        }

        if ( ! $ref = wp_get_original_referer() ) {
            $ref =  wp_get_referer() ;
            if ( ! $ref ) {
                $ref = wp_unslash( $_SERVER['REQUEST_URI'] );
            }
        }

        if ( $ref ) {
            $ref = remove_query_arg( 'errors', $ref );
        }

        $coupon_id = isset( $_REQUEST['c_id'] ) ?  intval( $_REQUEST['c_id'] ) : 0;


        if ( $coupon_id > 0 ){
            global $post;
            $post = get_post( $coupon_id );

        } else {
            $post = false;
        }

        // check how can edit this coupon
        if ( $post && is_user_logged_in() ) {
            $user = wp_get_current_user();
            $allowed_roles = apply_filters('allowed_roles_edit_coupon', array('editor', 'administrator', 'author'));
            if ( $post->post_author ==  $user->ID ||  array_intersect( $allowed_roles, $user->roles ) ) {

            } else {

            }

        } else {
           // wp_reset_postdata();
           // $post = get_post( 0 );
        }

        if ( $post ) {
            wpcoupon_setup_coupon( $post );
        } else {
            wpcoupon_setup_coupon( -1 );
        }

        ob_start();

        self::message();
        ?>
        <form action="<?php echo home_url('/'); ?>" method="post" class="st-coupon-form ui small form" enctype="multipart/form-data">
            <div class="field" >
                <?php if ( ! $single_store ) { ?>
                    <div class="ui action input select-store-input">
                        <input type="text" class="select-coupon-store" autocomplete="off" name="new_store_name" readonly="readonly" placeholder="<?php esc_html_e( 'Select a store', 'wp-coupon-submit' ) ;?>">
                        <button type="button" class="ui right labeled icon button">
                            <i class="shop icon"></i>
                            <?php esc_html_e( 'Select store', 'wp-coupon-submit' ); ?>
                        </button>
                    </div>
                    <input type="hidden" class="select-coupon-store-id" autocomplete="off" name="coupon_store" value="">
                    <input type="hidden" class="new-store-url" autocomplete="off" name="new_store_url" value="">
                    <input type="file" autocomplete="off" class="hidden hide" name="coupon_store_file">

                    <div id="wp-submit-coupon-stores" class="ui modal">
                        <div class="header">
                            <?php esc_html_e( 'Select a store', 'wp-coupon-submit' ); ?>
                        </div>
                        <div class="content">

                            <div class="search-stores ui icon input">
                                <i class="search icon"></i>
                                <input type="text" autocomplete="off" class="search-store-input" placeholder="<?php esc_html_e( 'Search store...', 'wp-coupon-submit' ); ?>">
                            </div>
                            <p class="message"><?php esc_html_e( 'Type store name and choice your store.', 'wp-coupon-submit' ); ?></p>

                            <div class="cs-ajax-results"></div>

                            <?php if (  wpcoupon_get_option( 'cs_enable_new_store', true ) ) { ?>

                            <div class="store-add-new">
                                <div class="ui warning message">
                                    <div class="header store-search-header">
                                        <?php esc_html_e( 'Still not found your store ?', 'wp-coupon-submit' ); ?>
                                    </div>
                                    <?php printf( esc_html__( 'Click %1$s to add new store.', 'wp-coupon-submit' ), '<a class="show-new-store-form" href="#">'.esc_html__( 'here', 'wp-coupon-submit' ).'</a>' ); ?>
                                </div>

                                <div class="new-store-form ui form">
                                    <div class="field">
                                        <label><?php esc_html_e( 'Store name', 'wp-coupon-submit' ); ?></label>
                                        <input autocomplete="off" class="new-store-name" type="text">
                                    </div>

                                    <div class="field">
                                        <label><?php esc_html_e( 'Store URL', 'wp-coupon-submit' ); ?></label>
                                        <input class="new-store-home-url" autocomplete="off" type="text">
                                    </div>

                                    <?php if ( wpcoupon_get_option( 'cs_enable_store_logo', true ) ) { ?>
                                    <div class="field ">
                                        <label><?php esc_html_e( 'Store logo', 'wp-coupon-submit' ); ?></label>
                                        <div class="ui action input new-store-logo">
                                            <input class="new-store-logo-placeholder" autocomplete="off" readonly="readonly" value="" type="text">
                                            <button class="ui teal right labeled icon button">
                                                <i class="upload icon"></i>
                                                <?php esc_html_e( 'Upload logo', 'wp-coupon-submit' ); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="save-new-store-submit ui positive right labeled icon button">
                                        <?php esc_html_e( 'Add new store', 'wp-coupon-submit' ); ?><i class="shop icon"></i>
                                    </div>

                                </div>
                            </div>
                            <?php } ?>

                        </div>
                        <div class="actions">
                            <div class="ui black deny button">
                                <?php esc_html_e( 'Close', 'wp-coupon-submit' ); ?>
                            </div>
                        </div>
                    </div>

                <?php } else { ?>
                    <input type="text" disabled="disabled" placeholder="<?php echo esc_attr( $term->name ); ?>">
                    <input type="hidden" name="coupon_store" value="<?php echo esc_attr( $single_store ); ?>">
                <?php } ?>
            </div>

            <div class="field field-cat">
                <?php
                $selected =  wpcoupon_coupon()->get_categories('ids');

                if ( $selected ) {
                    $selected =  current( $selected );
                }

                wp_dropdown_categories( array(
                    'show_option_none' => esc_html__( 'Select Category *' , 'wp-coupon-submit' ),
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'show_count' => 0,
                    'hide_empty' => 0,
                    'child_of' => 0,
                    'echo' => 1,
                    'selected' => $selected,
                    'hierarchical' => 1,
                    'name' => 'coupon_cat',
                    'class' => 'ui dropdown',
                    'depth' => 0,
                    'tab_index' => 0,
                    'taxonomy' => 'coupon_category',
                    'hide_if_empty' => false,
                ) ); ?>
            </div>

            <div class="field field-coupon-type ">
                <select name="coupon_type" class=" ui dropdown">
                    <option value=""><?php esc_attr_e( 'Offer Type *', 'wp-coupon-submit' ); ?></option>
                    <option <?php selected( wpcoupon_coupon()->get_type(), 'code' ); ?> value="code"><?php esc_attr_e( 'Coupon Code', 'wp-coupon-submit' ); ?></option>
                    <option <?php selected( wpcoupon_coupon()->get_type(), 'sale' ); ?> value="sale"><?php esc_attr_e( 'Sale' , 'wp-coupon-submit' ); ?></option>
                    <option <?php selected( wpcoupon_coupon()->get_type(), 'print' ); ?> value="print"><?php esc_attr_e( 'Printable', 'wp-coupon-submit'); ?></option>
                </select>
            </div>

            <div class="field-code field">
                <label><?php esc_html_e( 'Add code or change offer type', 'wp-coupon-submit' ); ?></label>
                <input type="text" name="coupon_code" value="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>" placeholder="<?php esc_attr_e( 'Code' , 'wp-coupon-submit' ); ?>">
            </div>

            <div class="field field-image">
                <label>
                    <span class=c-tile-others>
                    <?php esc_html_e( 'Coupon image', 'wp-coupon-submit' ); ?>
                    </span>
                    <span class=c-tile-print>
                    <?php esc_html_e( 'Print coupon image', 'wp-coupon-submit' ); ?>
                    </span>
                    <i class="info circle icon icon-popup" data-variation="inverted" data-content="<?php esc_attr_e( 'Click icon to switch input method', 'wp-coupon-submit' ); ?>"></i>
                </label>
                <p style="margin-bottom: 0px;" class="c-input-switcher ui icon input">
                    <input name="coupon_image_url" type="text" class="text-input" placeholder="<?php esc_attr_e( 'Image URL', 'wp-coupon-submit' ); ?>">
                    <input name="coupon_image_file" type="file" class="file-input" style="display: none;">
                    <i class="for-upload upload link icon icon-popup" data-variation="inverted" data-content="<?php esc_attr_e( 'Upload a image', 'wp-coupon-submit' ); ?>"></i>
                    <i style="display: none;"  class="for-input link world icon icon-popup" data-variation="inverted" data-content="<?php esc_attr_e( 'Input image URL', 'wp-coupon-submit' ); ?>"></i>
                    <input type="hidden" name="coupon_image_type" value="url">
                </p>
                <?php
                $img = '';
                if ( ! $single_store ) {
                    if ( wpcoupon_coupon()->ID > 0 ) {
                        if (wpcoupon_coupon()->get_type() == 'print') {
                            $src = wpcoupon_coupon()->get_print_image();
                            if ($src != '') {
                                $img = '<img src="' . esc_attr(wpcoupon_coupon()->get_print_image()) . '" alt=""/>';
                            }
                        } else {
                            $img = wpcoupon_coupon()->get_thumb('full', false);
                        }
                    }
                    if ($img) {
                        ?>
                        <p class="image-thumb" style="margin-bottom: 0px; margin-top: 1em;"><?php echo wp_kses_post( $img ); ?></p>
                    <?php
                    }
                }
                ?>
            </div>

            <div class="field">
                <input name="coupon_aff_url" type="text" placeholder="<?php esc_attr_e( 'Coupon Aff URL' , 'wp-coupon-submit' ); ?>">
            </div>

            <div class="field">
                <p style="margin-bottom: 0px;" class="ui icon input">
                    <?php $date_id = uniqid('date-'); ?>
                    <input type="text" class="st-datepicker" data-alt="#<?php echo esc_attr( $date_id ); ?>" value="<?php echo ( wpcoupon_coupon()->_wpc_expires  ) ? date_i18n('d/m/Y', wpcoupon_coupon()->_wpc_expires  ) : ''; ?>" placeholder="<?php esc_attr_e( 'Exp Date : dd/mm/yyyy', 'wp-coupon-submit' ); ?>">
                    <input name="coupon_expires" type="hidden" value="<?php echo ( wpcoupon_coupon()->_wpc_expires  ) ? date_i18n('Y-m-d', wpcoupon_coupon()->_wpc_expires  ) : ''; ?>" id="<?php echo esc_attr( $date_id ); ?>" name="coupon-expires">
                    <i class="calendar outline icon"></i>
                </p>
            </div>

            <div class="inline field">
                <div class="ui checkbox">
                    <input name="coupon_expires_unknown" <?php echo ( wpcoupon_coupon()->_wpc_expires == '' && wpcoupon_coupon()->ID > 0 )  ? 'checked="checked"' : ''; ?> value="1" type="checkbox">
                    <label><?php esc_html_e( 'Don\'t know the expiration date.', 'wp-coupon-submit' ); ?></label>
                </div>
            </div>
            <div class="field">
                <input name="coupon_title" value="<?php echo esc_attr( wpcoupon_coupon()->post_title ); ?>" type="text" placeholder="<?php esc_attr_e( 'Offer Title', 'wp-coupon-submit' ); ?>">
            </div>

            <div class="field">
                <textarea name="coupon_description" placeholder="<?php esc_attr_e( 'Offer Description', 'wp-coupon-submit' ); ?>"><?php echo esc_textarea( wpcoupon_coupon()->post_content ); ?></textarea>
            </div>

            <?php do_action( 'st_submit_coupon_more_fields' );

            if ( 'anyone' != wpcoupon_get_option( 'cs_who_can_submit', 'anyone' ) && ! is_user_logged_in() ) {
                ?>
                <div class="ui warning message not-login-notice">
                    <p>
                        <?php esc_html_e( 'Your must login to submit coupon', 'wp-coupon-submit' ); ?>
                    </p>
                </div>
                <?php
            }

            ?>

            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce(); ?>">
            <input type="hidden" name="_submit_coupon" value="1">
            <input type="hidden" name="coupon_id" value="<?php echo wpcoupon_coupon()->ID; ?>">
            <input type="hidden" name="action" value="submit_coupon">
            <input type="hidden" name="_wp_original_http_referer" value="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" />
            <button class="fluid ui button btn btn_primary btn_large"><?php esc_html_e( 'Submit', 'wp-coupon-submit' ); ?></button>
        </form>
        <?php

        wp_reset_postdata();
        $html = ob_get_clean();
        return $html;
    }

    static function content_shortcode( $atts, $content = '' ){
        return self::submit_coupon_form( );
    }
}

add_shortcode( 'wpcoupon_submit', array( 'WPCoupon_Coupon_Submit_ShortCode', 'content_shortcode' ) );



