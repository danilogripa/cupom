<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>
<div class="wrap">
    <h1><?php esc_html_e( 'Coupons CSV Import', 'wp_coupon_ie' ); ?></h1>
    <div class="wp-coupon-import-wrapper">
        <p class="upload-help"><?php printf( esc_html__( 'If you have a csv file of coupons, you may import here, %1$s', 'wp_coupon_ie' ), '<a href="'.esc_url( WP_COUPON_IE_URL.'dummy-data/coupon-example.xlsx' ).'">'.esc_html__( 'Download example in excel file', 'wp_coupon_ie' ).'</a>' ); ?> </p>
        <form class="wp-coupon-ie-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'tools.php?page=wp_coupon_import' ) ); ?>">
            <label><input  id="wp_coupon_import_file"  name="wp_coupon_import_file" type="file"></label>
            <input id="wp_coupon_import_submit" type="submit" value="<?php esc_attr_e( 'Upload Now', 'wp_coupon_ie' ) ?>" class="button" name="import-csv-submit">
            <?php wp_nonce_field('wp_coupon_ie', 'wp_coupon_ie' ); ?>
        </form>
    </div>

    <div class="wp-cie-progress-wrapper">
        <div class="wp-cie-progress">
            <div class="wp-cie-label">0%</div>
            <div class="wp-cie-percent"></div>
        </div>
        <div class="wp-cie-progress-log"></div>
    </div>
    <?php

    if (
        isset( $_SESSION['wp_coupon_import_data'] )
        && ! empty( $_SESSION['wp_coupon_import_data'] )
        && isset( $_SESSION['wp_coupon_imported_data'] )
        && ! $_SESSION['wp_coupon_imported_data']
    ) {
        $row =  current( $_SESSION['wp_coupon_import_data'] );
        $fields =  $this->get_data_fields();
        ?>
        <form class="wp-cie-settings" method="post" action="<?php echo esc_url( admin_url( 'tools.php?page=wp_coupon_import' ) ); ?>">
            <p class="upload-help"><?php esc_html_e( 'Some fields do not matches of data structure. Please re-setup before import.', 'wp_coupon_ie' ); ?></p>
            <div class="clear"></div>
            <div class="col">
                <table>
                   <thead>
                    <tr>
                        <td colspan="2"><?php esc_html_e( 'Coupons Settings', 'wp_coupon_ie' ); ?></td>
                    </tr>
                   </thead>
                   <?php foreach ( $fields['coupon'] as $k => $v ) { ?>
                   <tr>
                       <td>
                           <?php echo $v['label']; ?>
                       </td>
                       <td>
                           <select name="coupon[<?php echo esc_attr( $k ); ?>]">
                               <option value=""><?php esc_attr_e( '---Select field---', 'wp_coupon_ie' ); ?></option>
                               <?php foreach ( $row as $field_name => $_v ) { ?>
                               <option <?php echo ( $v['field'] == $field_name ) ? ' selected="selected" ' : ''; ?> value="<?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_name ); ?></option>
                               <?php } ?>
                           </select>
                       </td>
                   </tr>
                    <?php } ?>
                </table>
            </div>


            <div class="col">
                <table>
                    <thead>
                    <tr>
                        <td colspan="2"><?php esc_html_e( 'Store Settings', 'wp_coupon_ie' ); ?></td>
                    </tr>
                    </thead>
                    <?php foreach ( $fields['store'] as $k => $v ) { ?>
                        <tr>
                            <td>
                                <?php echo $v['label']; ?>
                            </td>
                            <td>
                                <select name="store[<?php echo esc_attr( $k ); ?>]">
                                    <option value=""><?php esc_attr_e( '---Select field---', 'wp_coupon_ie' ); ?></option>
                                    <?php foreach ( $row as $field_name => $_v ) { ?>
                                        <option <?php echo ( $v['field'] == $field_name ) ? ' selected="selected" ' : ''; ?> value="<?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_name ); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <div class="col">
                <table>
                    <thead>
                    <tr>
                        <td colspan="2"><?php esc_html_e( 'Category Settings', 'wp_coupon_ie' ); ?></td>
                    </tr>
                    </thead>
                    <?php foreach ( $fields['category'] as $k => $v ) { ?>
                        <tr>
                            <td>
                                <?php echo $v['label']; ?>
                            </td>
                            <td>
                                <select name="category[<?php echo esc_attr( $k ); ?>]">
                                    <option value=""><?php esc_attr_e( '---Select field---', 'wp_coupon_ie' ); ?></option>
                                    <?php foreach ( $row as $field_name => $_v ) { ?>
                                        <option <?php echo ( $v['field'] == $field_name ) ? ' selected="selected" ' : ''; ?> value="<?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_name ); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div style="clear: both"></div>
        </form>

        <p class="wp-cie-actions">
            <input type="submit" class="wp-cie-start-import button-primary" value="<?php esc_attr_e( 'Start Import', 'wp_coupon_ie' ); ?>">

            <a href="<?php print wp_nonce_url(admin_url('tools.php?page=wp_coupon_import&wp_cie_action=cancel-import'), 'wp_coupon_ie', 'nonce');?>" class="wp-cie-cancel-import button-secondary"><?php esc_html_e( 'Cancel', 'wp_coupon_ie' ); ?></a>
        </p>

        <script type="text/javascript">
            var wp_ci_number = <?php echo intval( sizeof( $_SESSION['wp_coupon_import_data'] ) ); ?>;
        </script>
        <?php

        $_SESSION['wp_coupon_imported_data'] = true;
    }

    ?>
</div>