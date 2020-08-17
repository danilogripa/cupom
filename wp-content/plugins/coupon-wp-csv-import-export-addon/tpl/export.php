<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $wpdb;
/**
 * Create the date options fields for exporting a given post type.
 *
 * @global wpdb      $wpdb      WordPress database abstraction object.
 * @global WP_Locale $wp_locale Date and Time Locale object.
 *
 * @since 3.1.0
 *
 * @param string $post_type The post type. Default 'post'.
 */
function wp_cie_export_date_options( $post_type = 'post' ) {
    global $wpdb, $wp_locale;

    $months = $wpdb->get_results( $wpdb->prepare( "
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
		FROM $wpdb->posts
		WHERE post_type = %s AND post_status != 'auto-draft'
		ORDER BY post_date DESC
	", 'coupon' ) );

    $month_count = count( $months );
    if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
        return;

    foreach ( $months as $date ) {
        if ( 0 == $date->year )
            continue;

        $month = zeroise( $date->month, 2 );
        echo '<option value="' . $date->year . '-' . $month . '">' . $wp_locale->get_month( $month ) . ' ' . $date->year . '</option>';
    }
}



?>
<div class="wrap">
    <h1><?php esc_html_e( 'Coupons CSV Export', 'wp_coupon_ie' ); ?></h1>
    <p><?php esc_html_e( 'When you click the button below WordPress will create an CSV file for you to save to your computer.', 'wp_coupon_ie' ) ?></p>
    <p><?php esc_html_e( "Once you’ve saved the download file, you can use the Import function to import the content from this site.", 'wp_coupon_ie' ); ?></p>
    <h2><?php echo esc_html_e( 'Choose what to export', 'wp_coupon_ie' ); ?></h2>
    <form method="post" action="<?php echo esc_url( admin_url( 'tools.php?page=wp_coupon_export' ) ); ?>">
        <p>
            <label><span class="label-responsive"><?php esc_html_e( 'Store:', 'wp_coupon_ie' ); ?></span>
                <?php wp_dropdown_categories( array(
                    'show_option_all' => esc_html__( 'All', 'wp_coupon_ie' ),
                    'taxonomy' => 'coupon_store',
                    'name'  => 'coupon_store'
                ) ); ?>
            </label>
        </p>
        <p>
            <label><span class="label-responsive"><?php _e( 'Authors:' ); ?></span>
                <?php
                $authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = 'coupon'" );
                wp_dropdown_users( array(
                    'include' => $authors,
                    'name' => 'author',
                    'multi' => true,
                    'show_option_all' => esc_html__( 'All', 'wp_coupon_ie' ),
                    'show' => 'display_name_with_login',
                ) ); ?>
            </label>
        </p>
        <p>
            <?php esc_html_e( 'Start date:', 'wp_coupon_ie' ); ?>
            <select name="start_date">
                <option value=""><?php esc_html_e( 'All date', 'wp_coupon_ie' ) ?></option>
                <?php wp_cie_export_date_options(); ?>
            </select>
            <?php esc_html_e( 'End date:', 'wp_coupon_ie' ); ?>
            <select name="end_date">
                <option value=""><?php esc_html_e( 'All date', 'wp_coupon_ie' ) ?></option>
                <?php wp_cie_export_date_options(); ?>
            </select>
        </p>
        <input name="wp_cie_export" type="submit" value="<?php esc_attr_e( 'Download Export File', 'wp_coupon_ie' ) ?>" class="button-primary">
        <?php wp_nonce_field('wp_coupon_ie', 'wp_coupon_ie' ); ?>
    </form>
</div>