<?php

/**
 * Plugin Name: Visual Term Description Editor
 * Plugin URI:  https://github.com/sheabunge/visual-term-description-editor
 * Description: Replaces the plain-text term (category, tag) description editor with a WYSIWYG visual editor
 * Author:      Shea Bunge
 * Author URI:  http://bungeshea.com
 * License:     MIT
 * License URI: http://opensource.com/licences/MIT
 * Version:     1.4.1
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Implement a visual editor for term descriptions
 *
 * @since 1.0
 */
class Visual_Term_Description_Editor {

    /**
     * The taxonomies which should use the visual editor
     *
     * @var array
     * @since 1.0
     */
    public $taxonomies;

    /**
     * The constructor function for the class
     *
     * @since 1.0
     *
     * @param array $taxonomies The taxonomies which should use a visual editor
     */
    public function __construct( array $taxonomies ) {

        /* Setup the class variables */
        $this->taxonomies = $taxonomies;
    }

    /**
     * Register actions and filters
     *
     * @since 1.4.0
     */
    public function run() {

        /* Only users with the "publish_posts" capability can use this feature */
        if ( current_user_can( 'publish_posts' ) ) {

            /* Remove the filters which disallow HTML in term descriptions */
            remove_filter( 'pre_term_description', 'wp_filter_kses' );
            remove_filter( 'term_description', 'wp_kses_data' );

            /* Add filters to disallow unsafe HTML tags */
            if ( ! current_user_can( 'unfiltered_html' ) ) {
                add_filter( 'pre_term_description', 'wp_kses_post' );
                add_filter( 'term_description', 'wp_kses_post' );
            }
        }

        /* Apply `the_content` filters to term description */
        if ( isset( $GLOBALS['wp_embed'] ) ) {
            add_filter( 'term_description', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
            add_filter( 'term_description', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
        }

        add_filter( 'term_description', 'wptexturize' );
        add_filter( 'term_description', 'convert_smilies' );
        add_filter( 'term_description', 'convert_chars' );
        add_filter( 'term_description', 'wpautop' );
        add_filter( 'term_description', 'shortcode_unautop' );
        add_filter( 'term_description', 'do_shortcode', 11);

        /* Loop through the taxonomies, adding actions */
        foreach ( $this->taxonomies as $taxonomy ) {
            add_action( $taxonomy . '_edit_form_fields', array( $this, 'render_field_edit' ), 1, 2 );
            add_action( $taxonomy . '_add_form_fields', array( $this, 'render_field_add' ), 1, 1 );
        }
    }

    /**
     * Add the visual editor to the edit tag screen
     *
     * @since 1.0
     *
     * @param object $tag      The tag currently being edited
     * @param string $taxonomy The taxonomy that the tag belongs to
     */
    public function render_field_edit( $tag, $taxonomy ) {

        $settings = array(
            'textarea_name' => 'description',
            'textarea_rows' => 10,
        );

        ?>
        <tr class="form-field term-description-wrap">
            <th scope="row" valign="top"><label for="description"><?php esc_html_e( 'Description', 'wp-coupon' ); ?></label></th>
            <td><?php wp_editor( htmlspecialchars_decode( $tag->description ), 'html-description', $settings ); ?>
                <p class="description"><?php esc_html_e( 'The description is not prominent by default, however some themes may show it.', 'wp-coupon' ); ?></p></td>
            <script type="text/javascript">
                // Remove the non-html field
                jQuery( 'textarea#description' ).closest( '.form-field' ).remove();
            </script>
        </tr>
    <?php
    }

    /**
     * Add the visual editor to the add new tag screen
     *
     * @since 1.0
     *
     * @param string $taxonomy The taxonomy that a new tag is being added to
     */
    public function render_field_add( $taxonomy ) {

        $settings = array(
            'textarea_name' => 'description',
            'textarea_rows' => 7,
        );

        ?>
        <div class="form-field term-description-wrap">
            <label for="tag-description"><?php esc_html_e( 'Description', 'wp-coupon' ); ?></label>
            <?php wp_editor( '', 'html-tag-description', $settings ); ?>
            <p><?php esc_html_e( 'The description is not prominent by default, however some themes may show it.', 'wp-coupon' ); ?></p>

            <script type="text/javascript">
                // Remove the non-html field
                jQuery( 'textarea#tag-description' ).closest( '.form-field' ).remove();

                jQuery(function() {
                    // Trigger save
                    jQuery( '#addtag' ).on( 'mousedown', '#submit', function() {
                        tinyMCE.triggerSave();
                    });
                });

            </script>
        </div>
    <?php
    }

}

/**
 * Instantiates the class to work on all of the registered taxonomies
 *
 * @since 1.0
 */
function visual_term_description_editor() {

    /* Retrieve an array of registered taxonomies */
    $taxonomies = get_taxonomies( '', 'names' );
    $taxonomies = apply_filters( 'visual_term_description_taxonomies', $taxonomies );

    /* Initialize the class */
    $plugin = new Visual_Term_Description_Editor( $taxonomies );
    $plugin->run();

    /* Make class accessible to other plugins */
    add_filter( 'visual_term_description_editor', $plugin );
}

add_action( 'wp_loaded', 'visual_term_description_editor', 999 );

/**
 * Fix the formatting buttons on the HTML section of
 * the visual editor from being full-width
 *
 * @since 1.1
 */
function fix_visual_term_description_editor_style() {
    echo '<style>.quicktags-toolbar input { width: auto; }</style>';
}

add_action( 'admin_head-edit-tags.php', 'fix_visual_term_description_editor_style' );
