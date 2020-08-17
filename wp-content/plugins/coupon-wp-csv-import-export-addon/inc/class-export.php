<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

require WP_COUPON_IE_PATH.'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


/**
 * Coupons Export Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

function wp_coupons_export( $args = array() ) {
    global $wpdb, $post;

    $where = $wpdb->prepare( "{$wpdb->posts}.post_type = %s", 'coupon' );
    $where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";

    $defaults = array(
        'coupon_store' => false,
        'start_date' => false,
        'end_date' => false,
        'status' => false,
        'author' => 0,
    );
    $args = wp_parse_args( $args, $defaults );

    $join = '';
    if ( $args['coupon_store']  ) {
        $term = get_term( $args['coupon_store'], 'coupon_store', 'ARRAY_A' );
        if ( $term ) {
            $join = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
            $where .= $wpdb->prepare( " AND {$wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id'] );
        }
    }

    if ( $args['author'] )
        $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_author = %d", $args['author'] );

    if ( $args['start_date'] )
        $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date >= %s", date( 'Y-m-d', strtotime($args['start_date']) ) );

    if ( $args['end_date'] )
        $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date < %s", date( 'Y-m-d', strtotime('+1 month', strtotime($args['end_date'])) ) );

    // Grab a snapshot of post IDs, just in case it changes during the export.
    $post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} $join WHERE $where" );


    $GLOBALS['fields'] = 'title,content,excerpt,status,type,expires,code,printable_url,destination_url,store,category,date,exclusive,author,store_name,store_slug,store_description,store_parent,store_url,store_aff_url,store_heading,store_is_featured,store_extra_info,store_image,category_name,category_slug,category_description,category_parent,category_icon,category_cat_image';
    $GLOBALS['fields'] = explode( ',', $GLOBALS['fields'] );



    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();

    // Set document properties
    $spreadsheet->getProperties()->setCreator( get_bloginfo('name') )
        ->setTitle('Export coupons file')
        ->setSubject('Office 2007 XLSX Document')
        ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        ->setKeywords('office 2007 openxml php');

    global $spreadsheet_data ;
    $spreadsheet_data = array();

    function wp_cie_get_store( $post ){
        global $spreadsheet_data;
        $tax = 'coupon_store';
        $post = get_post( $post );
        $names = array();
        $terms = wp_get_post_terms( $post->ID, $tax );
        foreach ( (array) $terms as $term ) {
            $names[ $term->name ] = $term->name;
            if ( ! isset( $spreadsheet_data[ 't'.+ $term->term_id ] ) ) {
                $parent = '';

                if( $term->parent ) {
                    if ( isset( $spreadsheet_data[ 't'.+ $term->term_id ] ) ) {
                        $parent = $spreadsheet_data[ 't'.+ $term->term_id ]['store_name'];
                    } else {
                        $t =  get_term( $term->parent, $tax );
                        if ( $t ) {
                            $parent = $t->name;
                        }
                    }
                }

                $image_id = get_term_meta( $term->term_id, '_wpc_store_image_id', true );
                if ( $image_id ) {
                    $image_id = wp_get_attachment_url( $image_id );
                }

                $item = wp_cie_setup_item( array() );
                $item = array_merge( $item, array(
                    'store_name'        => $term->name,
                    'store_slug'        => $term->slug,
                    'store_description' => $term->description,
                    'store_parent'      => $parent,
                    'store_url'         => get_term_meta( $term->term_id, '_wpc_store_url', true ),
                    'store_aff_url'     => get_term_meta( $term->term_id, '_wpc_store_aff_url', true ),
                    'store_heading'     => get_term_meta( $term->term_id, '_wpc_store_heading', true ),
                    'store_is_featured' => get_term_meta( $term->term_id, '_wpc_is_featured', true ),
                    'store_extra_info'  => get_term_meta( $term->term_id, '_wpc_extra_info', true ),
                    'store_image'       => $image_id,
                ) );

                $spreadsheet_data[ 't'.+ $term->term_id ] = $item;
            }
        }
        return join( ',', $names );
    }

    function wp_cie_get_coupon_cat( $post ){
        global $spreadsheet_data;
        $tax = 'coupon_category';
        $post = get_post( $post );
        $names = array();
        $terms = wp_get_post_terms( $post->ID, $tax );
        foreach ( (array) $terms as $term ) {
            $names[ $term->name ] = $term->name;
            if ( ! isset( $spreadsheet_data[ 't'.+ $term->term_id ] ) ) {
                $parent = '';
                if( $term->parent ) {
                    if ( isset( $spreadsheet_data[ 't'.+ $term->term_id ] ) ) {
                        $parent = $spreadsheet_data[ 't'.+ $term->term_id ]['category_name'];
                    } else {
                        $t =  get_term( $term->parent, $tax );
                        if ( $t ) {
                            $parent = $t->name;
                        }
                    }
                }

                $image_id = get_term_meta( $term->term_id, '_wpc_cat_image_id', true );
                if ( $image_id ) {
                    $image_id = wp_get_attachment_url( $image_id );
                }
                $item = wp_cie_setup_item( array() );
                $item = array_merge( $item, array(
                    'category_name'        => $term->name,
                    'category_slug'        => $term->slug,
                    'category_description' => $term->description,
                    'category_parent'      => $parent,
                    'category_icon'         => get_term_meta( $term->term_id, '_wpc_icon', true ),
                    'category_cat_image'     => $image_id,
                ) );

                $spreadsheet_data[ 't'.+ $term->term_id ] = $item;
                $spreadsheet_data[ 't'.+ $term->term_id ] = $item;

            }
        }
        return join( ',', $names );
    }

    function wp_cie_setup_item( $item ){
        if ( ! is_array( $item ) ) {
            $item = array();
        }
        foreach ( $GLOBALS['fields'] as $f ) {
            if ( ! isset( $item[ $f ] ) ) {
                $item[ $f ] = '';
            }
        }
        return $item;
    }

    if ( $post_ids ) {
        /**
         * @global WP_Query $wp_query
         */
        global $wp_query;

        // Fake being in the loop.
        $wp_query->in_the_loop = true;

        // Fetch 20 posts at a time rather than loading the entire table into memory.
        while ($next_posts = array_splice($post_ids, 0, 20)) {
            $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

            // Begin Loop.
            foreach ($posts as $post) {
                setup_postdata($post);

                $printable_id = get_post_meta( $post->ID, '_wpc_coupon_type_printable_id', true );
                $printable_url = '' ;
                if ( $printable_id ) {
                    $printable_url = wp_get_attachment_url( $printable_id ) ;
                }

                $user_email = '';
                $author = get_userdata( $post->post_author );
                if ( $author ) {
                    $user_email = $author->user_email;
                }

                $item = wp_cie_setup_item( array() );
                $item = array_merge( $item,
                    array(
                        'title'             => $post->post_title,
                        'content'           => $post->post_content,
                        'excerpt'           => $post->post_excerpt,
                        'status'            => $post->post_status,
                        'type'              => get_post_meta( $post->ID, '_wpc_coupon_type', true ),
                        'expires'           => get_post_meta( $post->ID, '_wpc_expires', true ),
                        'code'              => get_post_meta( $post->ID, '_wpc_coupon_type_code', true ),
                        'printable_url'     => $printable_url,
                        'destination_url'   => get_post_meta( $post->ID, '_wpc_destination_url', true ),
                        'date'              => $post->post_date,
                        'exclusive'         => get_post_meta( $post->ID, '_wpc_exclusive', true ),
                        'store'             => wp_cie_get_store( $post ),
                        'category'          => wp_cie_get_coupon_cat( $post ),
                        'author'            => $user_email,
                    )
                );

                $spreadsheet_data[ 'p'.+ $post->ID ] = $item;
                $spreadsheet_data[ 'p'.+ $post->ID ] = $item;

            } // end loop post
        } // end while

    }// end post_ids

    //$csv->output('export-coupons.csv', $spreadsheet_data, $GLOBALS['fields'], ',');
    //die();

    //var_dump( $spreadsheet_data );
    //die();

    $col_ids = range( 'A', 'Z' );
    $col_ids[] =  'AA';
    $col_ids[] =  'AB';
    $col_ids[] =  'AC';
    $col_ids[] =  'AD';

    $headers = array_combine( $col_ids , $GLOBALS[ 'fields' ] );

    $spreadsheet->setActiveSheetIndex(0);
    foreach (  $headers as $col_index => $name ) {
        $spreadsheet->getActiveSheet()->setCellValue( $col_index.'1', $name );
    }

    $new_data = array();
    foreach ( $spreadsheet_data as $k => $data ) {
        $new_data[] = array_values( $data );
    }


    $spreadsheet->getActiveSheet()->fromArray($new_data, null, 'A2');

    $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Xls)

    $file_name = sanitize_title( get_bloginfo('name').'-coupons-export-'. date_i18n( 'Y-m-d h:i:s',  current_time( 'timestamp' ) ) );
    /// Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

