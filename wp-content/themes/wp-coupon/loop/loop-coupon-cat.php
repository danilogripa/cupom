<?php
$has_thumb = wpcoupon_maybe_show_coupon_thumb();
$has_expired = wpcoupon_coupon()->has_expired();
?>
<div data-id="<?php echo wpcoupon_coupon()->ID; ?>"
	 class="coupon-item store-listing-item <?php echo $has_thumb ? 'has-thumb' : 'no-thumb'; ?> c-cat c-type-<?php echo esc_attr( wpcoupon_coupon()->get_type() ); ?> shadow-box <?php echo ( $has_expired ) ? 'coupon-expired' : 'coupon-live'; ?>">
	<?php if ( $has_thumb ) { ?>
	<div class="store-thumb-link">
		<?php wpcoupon_thumb( $has_thumb === 'save_value' ? true : false ); ?>
		<div class="store-name"><a href="<?php echo wpcoupon_coupon()->get_store_url(); ?>"><?php printf( esc_html__( '%s Coupons', 'wp-coupon' ), wpcoupon_coupon()->store->name ); ?><i class="angle right icon"></i></a></div>
	</div>
	<?php } ?>

	<div class="latest-coupon">
		<h3 class="coupon-title" >
			<?php edit_post_link( '<i class="edit icon"></i>', '', '', wpcoupon_coupon()->ID ); ?>
			<a class="coupon-link"
				<?php if ( ! wpcoupon_is_single_enable() ) { ?>
			   rel="nofollow"
				<?php } ?>
			   title="<?php echo esc_attr( get_the_title( wpcoupon_coupon()->ID ) ); ?>"
			   data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
			   data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
			   data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>"
			   data-code="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>"
			   href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php echo get_the_title( wpcoupon_coupon()->ID ); ?></a>
		</h3>
		<div class="coupon-des">
			<?php if ( wpcoupon_get_option( 'coupon_more_desc', true ) ) { ?>
				<div class="coupon-des-ellip"><?php
					echo wpcoupon_coupon()->get_excerpt(
						false,
						'<span class="c-actions-span">...<a class="more" href="#">' . esc_html__( 'More', 'wp-coupon' ) . '</a></span>',
						$has_thumb
                    );
												?></div>
				<?php
				if ( wpcoupon_coupon()->has_more_content ) {
					?>
					<div class="coupon-des-full"><?php
						echo str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', wpcoupon_coupon()->post_content . ' <a class="more less" href="#">' . esc_html__( 'Less', 'wp-coupon' ) . '</a>' ) );
					?></div>
				<?php } ?>
			<?php } else { ?>
				<?php echo apply_filters( 'the_content', wpcoupon_coupon()->post_content ); ?>
			<?php } ?>
		</div>
	</div>

	<div class="coupon-detail coupon-button-type">
		<?php
		switch ( wpcoupon_coupon()->get_type() ) {

			case 'sale':
				?>
				<a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>" class="coupon-deal coupon-button" href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php esc_html_e( 'Get Deal', 'wp-coupon' ); ?> <i class="shop icon"></i></a>
				<?php
				break;
			case 'print':
				?>
				<a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>" class="coupon-print coupon-button" href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"><?php esc_html_e( 'Print Coupon', 'wp-coupon' ); ?> <i class="print icon"></i></a>
				<?php
				break;
			default:
				?>
				<a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
				   data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
				   href="<?php echo esc_attr( wpcoupon_coupon()->get_href() ); ?>"
				   class="coupon-button coupon-code"
				   data-tooltip="<?php echo esc_attr_e( 'Click to copy & open site', 'wp-coupon' ); ?>"
				   data-position="top center"
				   data-inverted=""
				   data-code="<?php echo esc_attr( wpcoupon_coupon()->get_code() ); ?>"
				   data-aff-url="<?php echo esc_attr( wpcoupon_coupon()->get_go_out_url() ); ?>">
					<span class="code-text" rel="nofollow"><?php echo esc_html( wpcoupon_coupon()->get_code( 8, true ) ); ?></span>
					<span class="get-code"><?php esc_html_e( 'Get Code', 'wp-coupon' ); ?></span>
				</a>
				<?php
		}
		?>
		<div class="clear"></div>

		<div class="exp-text">
			<?php echo wpcoupon_coupon()->get_expires(); ?>
			<a data-position="top center" data-inverted=""  data-tooltip="<?php esc_attr_e( 'Save this coupon', 'wp-coupon' ); ?>" href="#" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" class="add-coupon-favorite coupon-save icon-popup"><i class="outline star icon"></i></a>
		</div>

	</div>
	<div class="clear"></div>
	<?php
	get_template_part( 'loop/coupon-modal' );
	?>
</div>
