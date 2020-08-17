<?php
/**
 * User: truongsa
 * Date: 4/4/16
 * Time: 7:29 PM
 */

class WPCoupon_MegaMenuAdmin {
    function __construct(){
        add_action( 'admin_print_scripts-nav-menus.php', array( $this, 'add_scripts' ) );
        add_action( 'admin_footer-nav-menus.php', array( $this, 'item_settings_tpl' ) , 65 );
        add_action( 'admin_footer-nav-menus.php', array( $this, 'load_menu_items_settings' ) );

        add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 60, 3 );

        add_action( 'wp_ajax_mm_search_terms', array( $this, 'ajax_search_term' ) );

    }

    function ajax_search_term(){

        $s = $_GET['s'];

        $terms = get_terms( array(
            'name__like' => $s,
            'taxonomy'   => array( 'coupon_category', 'coupon_store' ),
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => 100,
            'hide_empty' => false,
        ) );

        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $text = $t->taxonomy == 'coupon_category' ? esc_html__( 'Category', 'wp-coupon-mm' ) : esc_html__( 'Store', 'wp-coupon-mm' ) ;
                echo '<div class="mm-child-item" data-id="'.esc_attr( $t->term_id ).'">'.esc_html( $t->name ).' ('. $text .') <span class="dashicons dashicons-plus"></span></div>';
            }
        }
        wp_die();
    }

    function wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $args ){
        if ( isset( $_REQUEST['menu-item-enable-mega'] ) && is_array( $_REQUEST['menu-item-enable-mega'] ) ) {
            if ( isset( $_REQUEST['menu-item-enable-mega'][ $menu_item_db_id ] ) ) {
                $settings = array(
                    'enable' => '',
                    'columns' => '',
                    'mega_items' => '',
                    'style' => '',
                    'cwidth' => '',
                    'content' => '',
                );
                $settings['enable']     = $_REQUEST['menu-item-enable-mega'][ $menu_item_db_id ] == 1 ? 1 : 0;
                $settings['mega_items'] =( $_REQUEST['menu-item-mega-items'][ $menu_item_db_id ] ) ? $_REQUEST['menu-item-mega-items'][ $menu_item_db_id ] : array();
                $settings['columns']    = isset( $_REQUEST['menu-item-columns'][ $menu_item_db_id ] ) ? absint( $_REQUEST['menu-item-columns'][ $menu_item_db_id ]  ) : 3;
                $settings['style']      = isset( $_REQUEST['menu-item-style'][ $menu_item_db_id ] ) ?  $_REQUEST['menu-item-style'][ $menu_item_db_id ]  : '';
                $settings['cwidth']     = isset( $_REQUEST['menu-item-cwidth'][ $menu_item_db_id ] ) ?  $_REQUEST['menu-item-cwidth'][ $menu_item_db_id ]  : '';
                $settings['content']     = isset( $_REQUEST['menu-item-mc'][ $menu_item_db_id ] ) ?  $_REQUEST['menu-item-mc'][ $menu_item_db_id ]  : '';
                update_post_meta( $menu_item_db_id,  '_mm_settings', $settings );

            }else {
                delete_post_meta( $menu_item_db_id,  '_mm_settings' );
            }

        }

    }

    function get_menu_items_settings( $menu_id ){
        $items = wp_get_nav_menu_items( $menu_id );

        $default =  array(
            'enable' => '',
            'columns' => 3,
            'style' => '',
            'cwidth' => '',
            'mega_items' => '',
        ) ;

        $menus_items = array();

        if ( $items ){
            foreach ( $items as $p ) {
                $settings = get_post_meta( $p->ID, '_mm_settings', true );
                $settings = wp_parse_args( $settings, $default );

                if ( is_string( $settings['mega_items'] ) && $settings['mega_items'] != '' ){
                    $settings['mega_items'] = json_decode( $settings['mega_items'] , true );
                } else {
                    $settings['mega_items'] = array();
                }

                $menus_items[ $p->ID ] = $settings;

            }
        }

        return $menus_items;
    }

    public static  function get_selected_menu_id()
    {

        $nav_menus = wp_get_nav_menus(array('orderby' => 'name'));

        $menu_count = count($nav_menus);

        $nav_menu_selected_id = isset($_REQUEST['menu']) ? (int)$_REQUEST['menu'] : 0;

        $add_new_screen = (isset($_GET['menu']) && 0 == $_GET['menu']) ? true : false;

        // If we have one theme location, and zero menus, we take them right into editing their first menu
        $page_count = wp_count_posts('page');
        $one_theme_location_no_menus = (1 == count(get_registered_nav_menus()) && !$add_new_screen && empty($nav_menus) && !empty($page_count->publish)) ? true : false;

        // Get recently edited nav menu
        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));
        if (empty($recently_edited) && is_nav_menu($nav_menu_selected_id))
            $recently_edited = $nav_menu_selected_id;

        // Use $recently_edited if none are selected
        if (empty($nav_menu_selected_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited))
            $nav_menu_selected_id = $recently_edited;

        // On deletion of menu, if another menu exists, show it
        if (!$add_new_screen && 0 < $menu_count && isset($_GET['action']) && 'delete' == $_GET['action'])
            $nav_menu_selected_id = $nav_menus[0]->term_id;

        // Set $nav_menu_selected_id to 0 if no menus
        if ($one_theme_location_no_menus) {
            $nav_menu_selected_id = 0;
        } elseif (empty($nav_menu_selected_id) && !empty($nav_menus) && !$add_new_screen) {
            // if we have no selection yet, and we have menus, set to the first one in the list
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        return $nav_menu_selected_id;

    }

    function get_taxonomies(){
        return get_taxonomies(  array( 'object_type' => array( 'post' ) ), 'objects' );
    }

    function load_menu_items_settings(){
        // Check user permisons
        //check_ajax_referer( 'customize-menus', 'customize-menus-nonce' );
        //if ( ! current_user_can( 'edit_theme_options' ) ) {
        //    wp_die( -1 );
        //}
    }

    /**
     * @see Walker_Nav_Menu_Edit
     * @param $hook
     */
    function item_settings_tpl(  ){
        $taxs =  $this->get_taxonomies();
        ?>
        <script type="text/html" id="mm-item-settings-tpl">
            <div class="mm-item-w" id="mm-item-id-{{ data.id }}">
                <input type="hidden" class="menu-item-mega-items" name="menu-item-mega-items[{{ data.id }}]" value="{{ data.mega.mega_items }}" id="menu-item-mega-items-{{ data.id }}">
                <p class="field-enable-mega description">
                    <label>
                        <input type="checkbox" <# if ( data.mega.enable === 1 ){ #> checked="checked" <#  } #> class="menu-item-enable-mega" name="menu-item-enable-mega[{{ data.id }}]" value="1" id="menu-item-enable-mega[{{ data.id }}]">
                        <?php esc_html_e( 'Enable mega menu', 'wp-coupon-mm' ); ?>
                    </label>
                </p>
                <div class="mm-fields">
                    <label> <?php esc_html_e( 'Mega Items', 'wp-coupon-mm' ); ?></label><br>
                    <div class="mm-child-items"></div>
                    <div class="search-tax-add">
                        <input type="text" placeholder="<?php esc_attr_e( 'Search coupon stores or categories', 'wp-coupon-mm' ); ?>" class="search-tax-add widefat code">
                        <div class="search-tax-result"></div>
                    </div>

                    <div class="field-query-posts">
                        <input type="hidden" class="menu-item-mega-args" name="menu-item-mega-args[{{ data.id }}]" value="{{ data.mega.args }}" id="menu-item-mega-items-{{ data.id }}">
                        <#
                            var args = {};
                            if ( data.mega.args ) {
                                try {
                                    args = JSON.parse( data.mega.args );
                                } catch( e ) {

                                }
                            }
                        #>
                    </div>

                    <p class="field-mc">
                        <label>
                            <?php esc_html_e( 'Custom HTML code', 'wp-coupon-mm' ); ?>
                            <textarea name="menu-item-mc[{{ data.id }}]">{{ data.mega.content }}</textarea>
                        </label>
                    </p>

                    <p class="field-style">
                        <label>
                            <?php esc_html_e( 'Display style', 'wp-coupon-mm' ); ?>
                            <select name="menu-item-style[{{ data.id }}]">
                                <option value=""><?php esc_html_e( 'Default', 'wp-coupon-mm' ); ?></option>
                                <option <# if ( data.mega.style == 'number' ) { #> selected="selected" <# }  #> value="number"><?php esc_html_e( 'Show coupon number', 'wp-coupon-mm' ); ?></option>
                                <option <# if ( data.mega.style == 'thumb' ) { #> selected="selected" <# }  #> value="thumb"><?php esc_html_e( 'Show thumbnail', 'wp-coupon-mm' ); ?></option>
                            </select>
                        </label>
                    </p>

                    <p class="field-numpost">
                        <label for="edit-menu-item-columns-{{ data.id }}">
                            <?php esc_html_e( 'Number items per row', 'wp-coupon-mm' ); ?>
                            <select name="menu-item-columns[{{ data.id }}]">
                                <option value=""><?php esc_html_e( 'Default', 'wp-coupon-mm' ); ?></option>
                                <?php for ( $i = 1; $i <= 16; $i++ ){ ?>
                                <option <# if ( data.mega.columns == <?php echo $i; ?>) { #> selected="selected" <# }  #> value="<?php echo($i); ?>"><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </p>

                    <p class="field-content-width">
                        <label for="edit-menu-item-cwidth-{{ data.id }}">
                            <?php esc_html_e( 'Content width', 'wp-coupon-mm' ); ?>
                            <select name="menu-item-cwidth[{{ data.id }}]">
                                <option value=""><?php esc_html_e( 'Default', 'wp-coupon-mm' ); ?></option>
                                <?php for ( $i = 16; $i >= 1; $i-- ){ ?>
                                <option <# if ( data.mega.cwidth == <?php echo $i; ?>) { #> selected="selected" <# }  #> value="<?php echo($i); ?>"><?php echo round( ( $i / 16 ) * 100,  2 ).'%'; ?></option>
                                    <?php } ?>
                            </select>
                        </label>
                    </p>

                </div>
            </div>
        </script>
        <?php
    }

    function add_scripts(){


        wp_enqueue_script( 'wpcoupon-mega-menu-admin', WPCOUPON_MM_URL.'js/mega-menu-admin.js', array( 'jquery', 'backbone', 'underscore', 'json2' ), false, true );
        wp_enqueue_style( 'wpcoupon-mega-menu-admin', WPCOUPON_MM_URL.'/css/mega-menu-admin.css' );

        $menu_id = $this->get_selected_menu_id();
        $data = $this->get_menu_items_settings( $menu_id );

        $taxs =  $this->get_taxonomies();
        $taxs_data = array();
        foreach( $taxs as $k => $tax ){
            $taxs_data[ $k ] = get_terms( $k );
        }


        wp_localize_script( 'jquery', 'mm_admin_object', array(
            'mm'=> esc_html__( 'Mega', 'wp-coupon-mm' ),
            'loading'=> esc_html__( 'Loading...', 'wp-coupon-mm' ),
            '_nonce'=> wp_create_nonce( 'mm_admin_object' ),
            'menu_url'=> admin_url( 'nav-menus.php' ),
            //'confirm_import' => __( 'Are you sure want to IMPORT demo menus ?', 'wp-coupon-mm' ),
            //'import_txt' => __( 'Import demo menus', 'wp-coupon-mm' ),
            'menu_data' => $data,
            'taxs_data' => $taxs_data,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }
}

new WPCoupon_MegaMenuAdmin();



