<?php

$args = array(
    'base'               => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'post_type', get_pagenum_link( 999999999, false ) ) ) ),
    'format'             => '',
    'show_all'           => false,
    'prev_next'          => false,
    'type'               => 'array',
    'add_args'           => false,
    'add_fragment'       => '',
    'before_page_number' => '',
    'after_page_number'  => ''
);
$links = wpcoupon_paginate_links( $args );
if ( $links ) {
    ?>
    <div class="ui pagination menu">
        <?php foreach ($links as $link) { ?>
            <?php echo wp_kses_post( $link ); ?>
        <?php } ?>
    </div>
    <?php
}
