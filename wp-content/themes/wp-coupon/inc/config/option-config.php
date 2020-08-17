<?php

/**
 * Theme Options Config
 */

if ( ! class_exists( 'WPCoupon_Theme_Options_Config' ) ) {

	class WPCoupon_Theme_Options_Config {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( ! class_exists( 'ReduxFramework' ) ) {
				return;
			}
			$this->initSettings();
		}


		public function initSettings() {

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}

			$this->args = apply_filters( 'st_redux_theme_options_args', $this->args );

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
				'id'      => 'redux-help-tab-1',
				'title'   => esc_html__( 'Theme Information 1', 'wp-coupon' ),
				'content' => esc_html__( '<p>This is the tab content, HTML is allowed.</p>', 'wp-coupon' ),
			);

			$this->args['help_tabs'][] = array(
				'id'      => 'redux-help-tab-2',
				'title'   => esc_html__( 'Theme Information 2', 'wp-coupon' ),
				'content' => esc_html__( '<p>This is the tab content, HTML is allowed.</p>', 'wp-coupon' ),
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = esc_html__( '<p>This is the sidebar content, HTML is allowed.</p>', 'wp-coupon' );
		}

		/**
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 * */
		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name'           => 'st_options',
				// This is where your data is stored in the database and also becomes your global variable name.
				'display_name'       => $theme->get( 'Name' ),
				// Name that appears at the top of your panel
				'display_version'    => false,
				// Version that appears at the top of your panel
				'menu_type'          => 'menu', // submenu , menu
				// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'     => false,
				// Show the sections below the admin menu item or not
				'menu_title'         => esc_html__( 'Coupon WP', 'wp-coupon' ),
				'page_title'         => esc_html__( 'Theme Options', 'wp-coupon' ),
				// You will need to generate a Google API key to use this feature.
				// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
				'google_api_key'     => '',
				// Must be defined to add google fonts to the typography module
				'async_typography'   => false,
				// Use a asynchronous font on the front end or font string
				'admin_bar'          => false,
				// Show the panel pages on the admin bar
				'global_variable'    => 'st_option',
				// Set a different name for your global variable other than the opt_name
				'dev_mode'           => false,
				// Show the time the page took to load, etc
				'customizer'         => false,
				// Enable basic customizer support
				// OPTIONAL -> Give you extra features
				// 'page_priority'      => 65,
				// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent'        => 'themes.php', // themes.php
				// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'   => 'manage_options',
				// Permissions needed to access the options panel.
				'menu_icon'          => '',
				// Specify a custom URL to an icon
				'last_tab'           => '',
				// Force your panel to always open to a specific tab (by id)
				'page_icon'          => 'icon-themes',
				// Icon displayed in the admin panel next to your menu_title
				'page_slug'          => 'wpcoupon_options',
				// Page slug used to denote the panel
				'save_defaults'      => true,
				// On load save the defaults to DB before user clicks save or not
				'default_show'       => false,
				// If true, shows the default value next to each field that is not the default value.
				'default_mark'       => '',
				// What to print by the field's title if the value shown is default. Suggested: *
				'show_import_export' => true,
				// Shows the Import/Export panel when not used as a field.
				// CAREFUL -> These options are for advanced use only
				'transient_time'     => 60 * MINUTE_IN_SECONDS,

				'output'             => true,
				// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'         => true,
				// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				'footer_credit'     => ' ',
				// Disable the footer credit of Redux. Please leave if you can help it.
				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'           => '',
				// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'system_info'        => false,
				// REMOVE
				// HINTS
				'hints'              => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'   => 'light',
						'shadow'  => true,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position'  => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'    => array(
						'show' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'click mouseleave',
						),
					),
				),
			);

			// Panel Intro text -> before the form
			if ( ! isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
				if ( ! empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace( '-', '_', $this->args['opt_name'] );
				}
				// $this->args['intro_text'] = sprintf( __( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'wp-coupon' ), $v );
			} else {
				// $this->args['intro_text'] = __( '<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'wp-coupon' );
			}

			// Add content after the form.
			// $this->args['footer_text'] = __( '<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'wp-coupon' );
		}

		public function setSections() {

			/*
			--------------------------------------------------------*/
			/*
			 GENERAL SETTINGS
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'General', 'wp-coupon' ),
				// 'desc'   => sprintf( esc_html__( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: %d', 'wp-coupon' ), '<a href="' . 'https://' . 'github.com/ReduxFramework/Redux-Framework">' . 'https://' . 'github.com/ReduxFramework/Redux-Framework</a>' ),
				'desc'   => '',
				'icon'   => 'el-icon-cog el-icon-large',
				'submenu' => true, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields' => array(

					array(
						'id'       => 'site_logo',
						'url'      => false,
						'type'     => 'media',
						'title'    => esc_html__( 'Site Logo', 'wp-coupon' ),
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/logo.png' ),
						'subtitle' => esc_html__( 'Upload your logo here.', 'wp-coupon' ),
					),
					array(
						'id'       => 'site_logo_retina',
						'url'      => false,
						'type'     => 'media',
						'title'    => esc_html__( 'Site Logo Retina', 'wp-coupon' ),
						'default'  => '',
						'subtitle' => esc_html__( 'Upload at exactly 2x the size of your standard logo (optional), the name should include @2x at the end, example logo@2x.png', 'wp-coupon' ),
					),

					array(
						'id'      => 'layout',
						'title'   => esc_html__( 'Site Layout', 'wp-coupon' ),
						'desc'    => esc_html__( 'Default site layout', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'right-sidebar',
						// 'required' => array('footer_widgets','=',true, ),
						'options' => array(
							'left-sidebar'   => esc_html__( 'Left sidebar', 'wp-coupon' ),
							'no-sidebar'     => esc_html__( 'No sidebar', 'wp-coupon' ),
							'right-sidebar'  => esc_html__( 'Right sidebar', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'coupons_listing_page',
						'url'      => false,
						'type'     => 'select',
						'data'     => 'page',
						'title'    => esc_html__( 'Coupon categories listing page', 'wp-coupon' ),
						'default'  => '',
					),

					array(
						'id'       => 'stores_listing_page',
						'url'      => false,
						'type'     => 'select',
						'data'     => 'page',
						'title'    => esc_html__( 'Stores listing page', 'wp-coupon' ),
						'default'  => '',
					),

					array(
						'id'       => 'stores_listing_hide_child',
						'url'      => false,
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Hide child stores on listing page', 'wp-coupon' ),
						'default'  => 0,
					),

					array(
						'id'   => 'divider_r',
						'type' => 'divide',
					),

					array(
						'id'       => 'search_only_coupons',
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Show only coupons on search results', 'wp-coupon' ),
						'default'  => 1,
					),

					array(
						'id'   => 'divider_r_2',
						'desc' => '',
						'type' => 'divide',
					),

					array(
						'id'       => 'rewrite_store_slug',
						'url'      => false,
						'type'     => 'text',
						'title'    => esc_html__( 'Custom Store rewrite slug', 'wp-coupon' ),
						'subtitle'    => esc_html__( 'Default: store', 'wp-coupon' ),
						'default'  => 'store',
						'desc'     => sprintf( esc_html__( 'If you change this option please go to Settings &#8594; %1$s and refresh your permalink structure before your custom post type will show the correct structure.', 'wp-coupon' ), '<a href="' . admin_url( 'options-permalink.php' ) . '">' . esc_html__( 'Permalinks', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'       => 'rewrite_category_slug',
						'url'      => false,
						'type'     => 'text',
						'title'    => esc_html__( 'Custom coupon category rewrite slug', 'wp-coupon' ),
						'subtitle'    => esc_html__( 'Default: coupon-category', 'wp-coupon' ),
						'default'  => 'coupon-category',
						'desc'     => sprintf( esc_html__( 'If you change this option please go to Settings &#8594; %1$s and refresh your permalink structure before your custom post type will show the correct structure.', 'wp-coupon' ), '<a href="' . admin_url( 'options-permalink.php' ) . '">' . esc_html__( 'Permalinks', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'       => 'rewrite_tag_slug',
						'url'      => false,
						'type'     => 'text',
						'title'    => esc_html__( 'Custom coupon tag rewrite slug', 'wp-coupon' ),
						'subtitle'    => esc_html__( 'Default: coupon-tag', 'wp-coupon' ),
						'default'  => 'coupon-tag',
						'desc'     => sprintf( esc_html__( 'If you change this option please go to Settings &#8594; %1$s and refresh your permalink structure before your custom post type will show the correct structure.', 'wp-coupon' ), '<a href="' . admin_url( 'options-permalink.php' ) . '">' . esc_html__( 'Permalinks', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'   => 'divider_r_2',
						'desc' => '',
						'type' => 'divide',
					),

					array(
						'id'       => 'disable_feed_links',
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Disable feed links.', 'wp-coupon' ),
						'subtitle' => esc_html__( 'If you want to disable feed links just check this option.', 'wp-coupon' ),
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 STYLING
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Styling', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-idea',
				'submenu' => true,
				'fields' => array(

					array(
						'id'       => 'style_primary',
						'type'     => 'color',
						'title'    => esc_html__( 'Primary', 'wp-coupon' ),
						'default'  => '#00979d',
						'output'    => array(
							'background-color' => '
                                #header-search .header-search-submit, 
                                .newsletter-box-wrapper.shadow-box .input .ui.button,
                                .wpu-profile-wrapper .section-heading .button,
                                input[type="reset"], input[type="submit"], input[type="submit"],
                                .site-footer .widget_newsletter .newsletter-box-wrapper.shadow-box .sidebar-social a:hover,
                                .ui.button.btn_primary,
                                .site-footer .newsletter-box-wrapper .input .ui.button,
                                .site-footer .footer-social a:hover,
                                .site-footer .widget_newsletter .newsletter-box-wrapper.shadow-box .sidebar-social a:hover,
								.coupon-filter .ui.menu .item .offer-count,
								.coupon-filter .filter-coupons-buttons .store-filter-button .offer-count,
                                .newsletter-box-wrapper.shadow-box .input .ui.button,
                                .newsletter-box-wrapper.shadow-box .sidebar-social a:hover,
                                .wpu-profile-wrapper .section-heading .button,
                                .ui.btn.btn_primary,
								.ui.button.btn_primary,
								.coupon-filter .filter-coupons-buttons .submit-coupon-button:hover,
								.coupon-filter .filter-coupons-buttons .submit-coupon-button.active,
								.coupon-filter .filter-coupons-buttons .submit-coupon-button.active:hover,
								.coupon-filter .filter-coupons-buttons .submit-coupon-button.current::after,
                                .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce button.button.alt,
                                .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt
                            ',

							'color'            => '
                                .primary-color,
                                    .primary-colored,
                                    a,
                                    .ui.breadcrumb a,
                                    .screen-reader-text:hover,
                                    .screen-reader-text:active,
                                    .screen-reader-text:focus,
                                    .st-menu a:hover,
                                    .st-menu li.current-menu-item a,
                                    .nav-user-action .st-menu .menu-box a,
                                    .popular-stores .store-name a:hover,
                                    .store-listing-item .store-thumb-link .store-name a:hover,
                                    .store-listing-item .latest-coupon .coupon-title a,
                                    .store-listing-item .coupon-save:hover,
                                    .store-listing-item .coupon-saved,
                                    .coupon-modal .coupon-content .user-ratting .ui.button:hover i,
                                    .coupon-modal .coupon-content .show-detail a:hover,
                                    .coupon-modal .coupon-content .show-detail .show-detail-on,
                                    .coupon-modal .coupon-footer ul li a:hover,
                                    .coupon-listing-item .coupon-detail .user-ratting .ui.button:hover i,
                                    .coupon-listing-item .coupon-detail .user-ratting .ui.button.active i,
                                    .coupon-listing-item .coupon-listing-footer ul li a:hover, .coupon-listing-item .coupon-listing-footer ul li a.active,
                                    .coupon-listing-item .coupon-exclusive strong i,
                                    .cate-az a:hover,
                                    .cate-az .cate-parent > a,
                                    .site-footer a:hover,
                                    .site-breadcrumb .ui.breadcrumb a.section,
                                    .single-store-header .add-favorite:hover,
                                    .wpu-profile-wrapper .wpu-form-sidebar li a:hover,
                                    .ui.comments .comment a.author:hover       
                                ',
							'border-color' => '
                                textarea:focus,
                                input[type="date"]:focus,
                                input[type="datetime"]:focus,
                                input[type="datetime-local"]:focus,
                                input[type="email"]:focus,
                                input[type="month"]:focus,
                                input[type="number"]:focus,
                                input[type="password"]:focus,
                                input[type="search"]:focus,
                                input[type="tel"]:focus,
                                input[type="text"]:focus,
                                input[type="time"]:focus,
                                input[type="url"]:focus,
                                input[type="week"]:focus
                            ',
							'border-top-color' => '
                                .sf-arrows > li > .sf-with-ul:focus:after,
                                .sf-arrows > li:hover > .sf-with-ul:after,
                                .sf-arrows > .sfHover > .sf-with-ul:after
                            ',
							'border-left-color' => '
                                .sf-arrows ul li > .sf-with-ul:focus:after,
                                .sf-arrows ul li:hover > .sf-with-ul:after,
                                .sf-arrows ul .sfHover > .sf-with-ul:after,
                                .entry-content blockquote
							',
							'border-bottom-color' => '
								.coupon-filter .filter-coupons-buttons .submit-coupon-button.current::after
							',
							'border-right-color' => '
								.coupon-filter .filter-coupons-buttons .submit-coupon-button.current::after
							',
						),
					),

					array(
						'id'       => 'style_secondary',
						'type'     => 'color',
						'title'    => esc_html__( 'Secondary', 'wp-coupon' ),
						'default'  => '#ff9900',
						'output'    => array(
							'background-color' => '
                               .ui.btn,
                               .ui.btn:hover,
                               .ui.btn.btn_secondary,
                               .coupon-button-type .coupon-deal, .coupon-button-type .coupon-print, 
							   .coupon-button-type .coupon-code .get-code,
							   .coupon-filter .filter-coupons-buttons .submit-coupon-button.active.current
                            ',

							'color' => '
                                .a:hover,
                                .secondary-color,
                               .nav-user-action .st-menu .menu-box a:hover,
                               .store-listing-item .latest-coupon .coupon-title a:hover,
                               .ui.breadcrumb a:hover
                            ',

							'border-color' => '
                                .store-thumb a:hover,
                                .coupon-modal .coupon-content .modal-code .code-text,
                                .single-store-header .header-thumb .header-store-thumb a:hover
                            ',
							'border-left-color' => '
                                .coupon-button-type .coupon-code .get-code:after 
                            ',
						),
					),

					array(
						'id'       => 'style_c_code',
						'type'     => 'color',
						'title'    => esc_html__( 'Coupon code', 'wp-coupon' ),
						'default'  => '#b9dc2f',
						'output'    => array(
							'background-color' => '
                                .coupon-listing-item .c-type .c-code,
								.coupon-filter .ui.menu .item .code-count,
								.coupon-filter .filter-coupons-buttons .store-filter-button .offer-count.code-count
                            ',
						),
					),

					array(
						'id'       => 'style_c_sale',
						'type'     => 'color',
						'title'    => esc_html__( 'Coupon sale', 'wp-coupon' ),
						'default'  => '#ea4c89',
						'output'    => array(
							'background-color' => '
                                .coupon-listing-item .c-type .c-sale,
								.coupon-filter .ui.menu .item .sale-count,
								.coupon-filter .filter-coupons-buttons .store-filter-button .offer-count.sale-count
                            ',
						),
					),

					array(
						'id'       => 'style_c_print',
						'type'     => 'color',
						'title'    => esc_html__( 'Coupon print', 'wp-coupon' ),
						'default'  => '#2d3538',
						'output'    => array(
							'background-color' => '
                                .coupon-listing-item .c-type .c-print,
								.coupon-filter .ui.menu .item .print-count,
								.coupon-filter .filter-coupons-buttons .store-filter-button .offer-count.print-count
                            ',
						),
					),

					array(
						'id'       => 'style_body_bg',
						'type'     => 'background',
						'title'    => esc_html__( 'Body background', 'wp-coupon' ),
						'default'  => array(
							'background-color' => '#f8f9f9',
						),
						'output' => array( 'body' ),
					),
				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 HEADER
			/*--------------------------------------------------------*/

			$this->sections[] = array(
				'title'  => esc_html__( 'Header', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-file',
				'submenu' => true,
				'fields' => array(

					array(
						'id'       => 'header_sticky',
						'url'      => false,
						'type'     => 'checkbox',
						'title'    => esc_html__( 'Enable Header Sticky', 'wp-coupon' ),
						'default'  => '',
						'subtitle' => esc_html__( 'This function apply for desktop only.', 'wp-coupon' ),
					),

					array(
						'id'          => 'header_icons',
						'type'        => 'slides_v2',
						'title'       => esc_html__( 'Header Icons', 'wp-coupon' ),
						// 'subtitle'    => esc_html__('Unlimited slides with drag and drop sortings.', 'wp-coupon'),
						'desc'        => sprintf( esc_html__( 'You can find icon code at %s', 'wp-coupon' ), '<a target="_blank" href="http://semantic-ui.com/elements/icon.html">http://semantic-ui.com/elements/icon.html</a>.' ),
						'placeholder' => array(
							'title'           => esc_html__( 'Label', 'wp-coupon' ),
							'description'     => esc_html__( 'Enter you icon code, Ability use HTML code here.', 'wp-coupon' ),
							'url'             => esc_html__( 'URL', 'wp-coupon' ),
						),
						'show' => array(
							'image' => false,
							'title' => true,
							'description' => true,
							'url' => true,
						),
						'content_title' => esc_html__( 'Item', 'wp-coupon' ),
					),

					array(
						'id'       => 'top_search_stores',
						'type'     => 'select',
						'title'    => esc_html__( 'Top Search Stores', 'wp-coupon' ),
						'data' => 'terms',
						'sortable' => true,
						'multi' => false,
						'width' => '100%',
						'args' => array(
							'taxonomies' => 'coupon_store',
							'args' => array(),
						),
						// Must provide key => value pairs for select options
					),

					array(
						'id'       => 'header_custom_color',
						'type'     => 'switch',
						'title'    => esc_html__( 'Custom your header style?', 'wp-coupon' ),
						'default'  => false,
					),
					array(
						'id'       => 'header_bg',
						'type'     => 'background',
						// 'compiler' => true,
						'output'   => array( '.primary-header' ),
						'title'    => esc_html__( 'Header Background', 'wp-coupon' ),
						'required' => array( 'header_custom_color', '=', true ),
						'default'  => array(),
					),

					array(
						'id'       => 'header_color',
						'type'     => 'color',
						'title'    => esc_html__( 'Color', 'wp-coupon' ),
						'output'   => array( '.header-highlight a .highlight-icon', '.header-highlight a .highlight-text', '.primary-header', '.primary-header a', '#header-search .search-sample a' ),
						'required' => array( 'header_custom_color', '=', true ),
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 TYPOGRAPHY
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'      => esc_html__( 'Typography', 'wp-coupon' ),
				'header'     => '',
				'desc'       => '',
				'icon_class' => 'el-icon-large',
				'icon'       => 'el-icon-font',
				'submenu'    => true,
				'fields'     => array(
					array(
						'id'             => 'font_body',
						'type'           => 'typography',
						'title'          => esc_html__( 'Body', 'wp-coupon' ),
						'compiler'       => true,
						'google'         => true,
						'font-backup'    => false,
						'font-weight'    => true,
						'all_styles'     => true,
						'font-style'     => false,
						'subsets'        => true,
						'font-size'      => true,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'color'          => true,
						'preview'        => true,
						'output'         => array( 'body, p' ),
						'units'          => 'px',
						'subtitle'       => esc_html__( 'Select custom font for your main body text.', 'wp-coupon' ),
						'default'        => array(
							'font-family' => 'Open Sans',
						),
					),
					array(
						'id'             => 'font_heading',
						'type'           => 'typography',
						'title'          => esc_html__( 'Heading', 'wp-coupon' ),
						'compiler'       => true,
						'google'         => true,
						'font-backup'    => false,
						'all_styles'     => true,
						'font-weight'    => false,
						'font-style'     => false,
						'subsets'        => true,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => true,
						'color'          => true,
						'preview'        => true,
						'output'         => array( 'h1,h2,h3,h4,h5,h6' ),
						'units'          => 'px',
						'subtitle'       => esc_html__( 'Select custom font for heading like h1, h2, h3, ...', 'wp-coupon' ),
						'default'        => array(),
					),
				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 MENU BAR
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Menu Bar', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-credit-card',
				'submenu' => true,
				'fields' => array(
					array(
						'id'             => 'primary_menu_typography',
						'type'           => 'typography',
						'output'         => array(
							'.primary-navigation .st-menu > li > a,
                                                    .nav-user-action .st-menu > li > a,
                                                    .nav-user-action .st-menu > li > ul > li > a
                                                    ',
						),
						'title'          => esc_html__( 'Primary Menu Typography', 'wp-coupon' ),
						'compiler'       => true,
						'google'         => true,
						'font-backup'    => false,
						'text-align'     => false,
						'text-transform' => true,
						'font-weight'    => true,
						'all_styles'     => false,
						'font-style'     => true,
						'subsets'        => true,
						'font-size'      => true,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => true,
						'color'          => true,
						'preview'        => true,
						'units'          => 'px',
						'subtitle'       => esc_html__( 'Custom typography for primary menu.', 'wp-coupon' ),
						'default'        => array(),
					),
				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 PAGE
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Page', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-file',
				'submenu' => true,
				'fields' => array(

					// Page settings
					array(
						'id'      => 'page_header',
						'title'   => esc_html__( 'Page header', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'on',
						// 'required' => array('footer_widgets','=',true, ),
						'options' => array(
							'on'    => esc_html__( 'Show page header', 'wp-coupon' ),
							'off'   => esc_html__( 'Hide page header ', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'page_header_breadcrumb',
						'type'    => 'switch',
						'title'   => esc_html__( 'Show header breadcrumb', 'wp-coupon' ),
						'required' => array( 'page_header', '=', array( 'on' ) ),
						'default'  => true,
						'desc'  => esc_html__( 'Check this if you want to show breadcrumb. NOTE: you must install plugin Breadcrumb Navxt to use this function.', 'wp-coupon' ),
					),

					array(
						'id'      => 'page_header_cover',
						'type'    => 'switch',
						'title'   => esc_html__( 'Show cover image', 'wp-coupon' ),
						'required' => array( 'page_header', '=', array( 'on' ) ),
						// 'subtitle' => esc_html__('Look, it\'s on!', 'redux-framework-demo'),
						'default'  => false,
					),

					array(
						'id'      => 'page_header_cover_img',
						'type'    => 'media',
						'title'   => esc_html__( 'Header cover image', 'wp-coupon' ),
						'required' => array( 'page_header_cover', '=', array( true ) ),
						// 'subtitle' => esc_html__('Look, it\'s on!', 'redux-framework-demo'),
						'default'  => '',
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 BLOG
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Blog', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-pencil',
				'submenu' => true,
				'fields' => array(

					array(
						'id'      => 'blog_header',
						'title'   => esc_html__( 'Page header', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'on',
						'options' => array(
							'on'    => esc_html__( 'Show page header', 'wp-coupon' ),
							'off'   => esc_html__( 'Hide page header ', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'blog_header_title',
						'type'    => 'text',
						'title'   => esc_html__( 'Custom blog title', 'wp-coupon' ),
						'required' => array( 'blog_header', '=', array( 'on' ) ),
						'default'  => '',
					),

					array(
						'id'      => 'blog_header_breadcrumb',
						'type'    => 'switch',
						'title'   => esc_html__( 'Show header breadcrumb', 'wp-coupon' ),
						'required' => array( 'blog_header', '=', array( 'on' ) ),
						'default'  => true,
						'desc'  => esc_html__( 'Check this if you want to show breadcrumb. NOTE: you must install plugin Breadcrumb Navxt to use this function.', 'wp-coupon' ),
					),

					array(
						'id'      => 'blog_header_cover',
						'type'    => 'switch',
						'title'   => esc_html__( 'Show cover image', 'wp-coupon' ),
						'required' => array( 'blog_header', '=', array( 'on' ) ),
						// 'subtitle' => esc_html__('Look, it\'s on!', 'redux-framework-demo'),
						'default'  => false,
					),

					array(
						'id'      => 'blog_header_cover_img',
						'type'    => 'media',
						'title'   => esc_html__( 'Header cover image', 'wp-coupon' ),
						'required' => array( 'blog_header_cover', '=', array( true ) ),
						'default'  => '',
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 STORE
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Single Store', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-shopping-cart',
				'submenu' => true,
				'fields' => array(

					array(
						'id'      => 'store_loop_tpl',
						'title'   => esc_html__( 'Coupon Store template', 'wp-coupon' ),
						'desc'    => esc_html__( 'Select template for store coupons.', 'wp-coupon' ),
						'type'    => 'select',
						'default' => 'full',
						'options' => array(
							'full'  => esc_html__( 'Full', 'wp-coupon' ),
							'cat'   => esc_html__( 'Less', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'coupon_store_show_thumb',
						'type'     => 'select',
						'default' => 'default',
						'title'    => esc_html__( 'Show coupon item thumbnails', 'wp-coupon' ),
						'options' => array(
							'default'                   => esc_html__( 'Default, Show if has thumbnail else store thumbnail instead.', 'wp-coupon' ),
							'hide_if_no_thumb'          => esc_html__( 'Show if has thumbnail', 'wp-coupon' ),
							'save_value'                => esc_html__( 'Show discount value as coupon thumbnail', 'wp-coupon' ),
							'hide'                      => esc_html__( 'Hide All', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'store_layout',
						'title'   => esc_html__( 'Single Store Layout', 'wp-coupon' ),
						'desc'    => esc_html__( 'Default single store layout.', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'left-sidebar',
						// 'required' => array('footer_widgets','=',true, ),
						'options' => array(
							'left-sidebar'   => esc_html__( 'Left sidebar', 'wp-coupon' ),
							'right-sidebar'  => esc_html__( 'Right sidebar', 'wp-coupon' ),
						),
					),
					array(
						'id'       => 'store_socialshare',
						'type'     => 'switch',
						'title'    => esc_html__( 'Store social sharing', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Enable social share under store description', 'wp-coupon' ),
						'default'  => true,
					),
					array(
						'id'       => 'store_heading',
						'type'     => 'textarea',
						'default' => '<strong>%store_name%</strong> Coupons & Promo Codes',
						'title'    => esc_html__( 'Store custom heading', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Custom heading text for display on single store page. Use %store_name% to replace with current store name.', 'wp-coupon' ),
					),
					array(
						'id'       => 'store_unpopular_coupon',
						'type'     => 'text',
						'default' => 'Unpopular %store_name% Coupons',
						'title'    => esc_html__( 'Store unpopular coupon text', 'wp-coupon' ),
					),
					array(
						'id'       => 'store_expired_coupon',
						'type'     => 'text',
						'default' => 'Recently Expired %store_name% Coupons',
						'title'    => esc_html__( 'Store expired coupon text.', 'wp-coupon' ),
					),
					array(
						'id'       => 'store_number_active',
						'type'     => 'text',
						'default' => '15',
						'title'    => esc_html__( 'Number coupons to show', 'wp-coupon' ),
					),
					array(
						'id'       => 'go_store_slug',
						'type'     => 'text',
						'default' => 'go-store',
						'title'    => esc_html__( 'Custom goto store slug', 'wp-coupon' ),
						'desc'    => sprintf( esc_html__( 'When you enable this option maybe the permalinks will effect, to resolve this go to %1$s and hit "Save Changes" button.', 'wp-coupon' ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Permalinks Settings', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'       => 'store_enable_sidebar_filter',
						'type'     => 'switch',
						'title'    => esc_html__( 'Enable store filter', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Enable the filter in sidebar of store page.', 'wp-coupon' ),
						'default'  => true,
					),
					array(
						'id'       => 'store_sidebar_filter_title',
						'type'     => 'text',
						'title'    => esc_html__( 'Store filter title', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Set title for the store filter in sidebar.', 'wp-coupon' ),
						'default'  => 'Filter Store',
						'required' => array('store_enable_sidebar_filter','=',1)
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 COUPON CATEGORY
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Coupon Category', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-tags',
				'submenu' => true,
				'fields' => array(

					array(
						'id'      => 'coupon_cate_tpl',
						'title'   => esc_html__( 'Coupon Category template', 'wp-coupon' ),
						'desc'    => esc_html__( 'Select template for coupon category.', 'wp-coupon' ),
						'type'    => 'select',
						'default' => 'cat',
						'options' => array(
							'cat'   => esc_html__( 'Less', 'wp-coupon' ),
							'full'  => esc_html__( 'Full', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'coupon_cate_show_thumb',
						'type'     => 'select',
						'default' => 'default',
						'title'    => esc_html__( 'Show coupon item thumbnails', 'wp-coupon' ),
						'options' => array(
							'default'                   => esc_html__( 'Default, Show if has thumbnail else store thumbnail instead.', 'wp-coupon' ),
							'hide_if_no_thumb'          => esc_html__( 'Show if has thumbnail', 'wp-coupon' ),
							'save_value'                => esc_html__( 'Show discount value as coupon thumbnail', 'wp-coupon' ),
							'hide'                      => esc_html__( 'Hide All', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'coupon_cate_layout',
						'title'   => esc_html__( 'Coupon Category Layout', 'wp-coupon' ),
						'desc'    => esc_html__( 'Default coupon category layout.', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'left-sidebar',
						'options' => array(
							'left-sidebar'   => esc_html__( 'Left sidebar', 'wp-coupon' ),
							'right-sidebar'  => esc_html__( 'Right sidebar', 'wp-coupon' ),
						),
					),
					array(
						'id'       => 'coupon_cate_socialshare',
						'type'     => 'switch',
						'title'    => esc_html__( 'Coupon category social sharing', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Enable social share under coupon category description', 'wp-coupon' ),
						'default'  => true,
					),
					array(
						'id'       => 'coupon_cate_heading',
						'type'     => 'textarea',
						'default' => '<strong>%coupon_cate%</strong> Coupons & Promo Codes',
						'title'    => esc_html__( 'Coupon category custom heading', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Custom heading text for display on coupon category page. You can use %coupon_cate% to display category name.', 'wp-coupon' ),
					),
					array(
						'id'       => 'coupon_cate_subheading',
						'type'     => 'text',
						'default'  => esc_html__( 'Newest %coupon_cate% Coupons', 'wp-coupon' ),
						'title'    => esc_html__( 'Coupon category sub-heading', 'wp-coupon' ),
						'subtitle' => esc_html__( 'You can use %coupon_cate% to display category name.', 'wp-coupon' ),
					),
					array(
						'id'       => 'coupon_cate_number',
						'type'     => 'text',
						'default'  => '15',
						'title'    => esc_html__( 'How many coupons display by default?', 'wp-coupon' ),
					),
					array(
						'id'      => 'coupon_cate_paging',
						'title'   => esc_html__( 'Coupon listing paging', 'wp-coupon' ),
						'type'    => 'button_set',
						'default' => 'ajax_loadmore',
						'options' => array(
							'paging_navigation' => esc_html__( 'Paging Navigation', 'wp-coupon' ),
							'ajax_loadmore'     => esc_html__( 'Load more with ajax', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'coupon_cate_ads',
						'title'   => esc_html__( 'Category advertisement', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Display custom ads after coupons listing on single category.', 'wp-coupon' ),
						'type'    => 'textarea',
						'default' => '',
					),
					array(
						'id'       => 'coupon_cate_sidebar_filter',
						'type'     => 'switch',
						'title'    => esc_html__( 'Enable category filter', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Enable the filter in sidebar of category page.', 'wp-coupon' ),
						'default'  => true,
					),
					array(
						'id'       => 'coupon_cate_filter_title',
						'type'     => 'text',
						'title'    => esc_html__( 'Category filter title', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Set title for the category filter in sidebar.', 'wp-coupon' ),
						'default'  => 'Filter',
						'required' => array( 'coupon_cate_sidebar_filter', '=', 1 ),
					),
				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 COUPON ITEM
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Coupons', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-tag',
				'submenu' => true,
				'fields' => array(

					array(
						'id'       => 'enable_single_coupon',
						'type'     => 'checkbox',
						'default' => false,
						'title'    => esc_html__( 'Enable single page for coupon.', 'wp-coupon' ),
						'desc'    => sprintf( esc_html__( 'When you enable this option maybe the permalinks will effect, to resolve this go to %1$s and hit "Save Changes" button.', 'wp-coupon' ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Permalinks Settings', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'      => 'coupon_filter_tabs',
						'type'    => 'sorter',
						'title'   => esc_html__( 'Coupon Filter', 'wp-coupon' ),
						'subtitle'    => esc_html__( 'Custom your coupon filter tabs.', 'wp-coupon' ),
						'options' => array(
							'enabled'  => array_merge( array( 'all' => __( 'All', 'wp-coupon' ) ), wpcoupon_get_coupon_types( true ) ),
							'disabled' => array(),
						),
					),

					array(
						'id'       => 'auto_open_coupon_modal',
						'type'     => 'checkbox',
						'default' => false,
						'title'    => esc_html__( 'Auto open coupon modal on single coupon.', 'wp-coupon' ),
						'required' => array( 'enable_single_coupon', 'equals', '1' ),
					),

					array(
						'id'       => 'enable_single_popular',
						'type'     => 'checkbox',
						'default' => true,
						'title'    => esc_html__( 'Enable popular coupons on single page.', 'wp-coupon' ),
						'required' => array( 'enable_single_coupon', 'equals', '1' ),
					),

					array(
						'id'       => 'single_popular_text',
						'type'     => 'text',
						'default'   => esc_html__( 'Most popular {store} coupons.', 'wp-coupon' ),
						'title'    => esc_html__( 'Custom popular text.', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Use {store} to display store name.', 'wp-coupon' ),
						'required' => array( 'enable_single_popular', 'equals', '1' ),
					),

					array(
						'id'       => 'single_popular_number',
						'type'     => 'text',
						'default' => 3,
						'title'    => esc_html__( 'Number popular coupons on single page.', 'wp-coupon' ),
						'required' => array( 'enable_single_popular', 'equals', '1' ),
					),

					array(
						'id'       => 'coupon_item_logo',
						'type'     => 'select',
						'default' => 'default',
						'title'    => esc_html__( 'Show coupon item thumbnails', 'wp-coupon' ),
						'options' => array(
							'default'                   => esc_html__( 'Default, Show if has thumbnail else store thumbnail instead.', 'wp-coupon' ),
							'hide_if_no_thumb'          => esc_html__( 'Show if has thumbnail', 'wp-coupon' ),
							'save_value'                => esc_html__( 'Show discount value as coupon thumbnail', 'wp-coupon' ),
							'hide'                      => esc_html__( 'Hide All', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'coupon_more_desc',
						'type'     => 'checkbox',
						'default' => 1,
						'title'    => esc_html__( 'Show coupon read more description.', 'wp-coupon' ),
					),

					array(
						'id'       => 'coupon_time_zone_local',
						'type'     => 'checkbox',
						'default' => false,
						'title'    => esc_html__( 'Use Local Timezone', 'wp-coupon' ),
						'desc'    => esc_html__( 'Making the coupons get expired based on selected timezone.', 'wp-coupon' ),
					),

					array(
						'id'       => 'coupon_human_time',
						'type'     => 'checkbox',
						'default' => 0,
						'title'    => esc_html__( 'Coupon human time diff', 'wp-coupon' ),
						'desc'    => esc_html__( 'Show human time diff such as 3 days left, 2 days left,...', 'wp-coupon' ),
					),

					array(
						'id'       => 'coupon_item_exclusive',
						'type'     => 'text',
						'default'  => '<strong><i class="protect icon"></i>Exclusive:</strong> This coupon can only be found at our website.',
						'title'    => esc_html__( 'Exclusive Coupon Message', 'wp-coupon' ),
					),

					array(
						'id'      => 'coupon_expires_action',
						'title'   => esc_html__( 'When coupon expires', 'wp-coupon' ),
						'type'    => 'select',
						'default' => 'do_nothing',
						'options' => array(
							'do_nothing' => esc_html__( 'Do Nothing', 'wp-coupon' ),
							'set_status' => esc_html__( 'Disable', 'wp-coupon' ),
							'remove'     => esc_html__( 'Remove', 'wp-coupon' ),
						),
					),

					array(
						'id'      => 'coupon_expires_time',
						'title'   => esc_html__( 'Run expires action time', 'wp-coupon' ),
						'desc'    => esc_html__( 'Run action after coupon expires x seconds., default: 604800 (1 week)', 'wp-coupon' ),
						'type'    => 'text',
						'default' => 604800, // 1 week
					),

					array(
						'id'       => 'print_prev_tab',
						'type'     => 'checkbox',
						// 'required' => array( 'enable_single_coupon','!=','1'),
						'default' => false,
						'title'    => esc_html__( 'Open store website in new tab when click on print button.', 'wp-coupon' ),
					),

					array(
						'id'       => 'sale_prev_tab',
						'type'     => 'checkbox',
						// 'required' => array( 'enable_single_coupon','!=','1'),
						'default' => true,
						'title'    => esc_html__( 'Open store website in new tab when click on "Get Deal" button.', 'wp-coupon' ),
					),

					array(
						'id'       => 'code_prev_tab',
						'type'     => 'checkbox',
						// 'required' => array( 'enable_single_coupon','!=','1'),
						'default' => true,
						'title'    => esc_html__( 'Open store website in new tab when click on "Get Code" button.', 'wp-coupon' ),
					),

					array(
						'id'       => 'coupon_click_action',
						'type'     => 'button_set',
						'default' => 'prev',
						'options' => array(
							'prev' => __( 'Previous Tab', 'wp-coupon' ),
							'next' => __( 'Next Tab', 'wp-coupon' ),
						),
						'title'    => esc_html__( 'Action when open store website.', 'wp-coupon' ),
					),

					array(
						'id'      => 'coupon_num_words_excerpt',
						'title'   => esc_html__( 'Default coupon excerpt length', 'wp-coupon' ),
						'type'    => 'text',
						'default' => 10,
					),

					array(
						'id'       => 'go_out_slug',
						'type'     => 'text',
						'default' => 'out',
						'title'    => esc_html__( 'Custom coupon go out slug', 'wp-coupon' ),
						'desc'    => sprintf( esc_html__( 'When you enable this option maybe the permalinks will effect, to resolve this go to %1$s and hit "Save Changes" button.', 'wp-coupon' ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Permalinks Settings', 'wp-coupon' ) . '</a>' ),
					),

					array(
						'id'       => 'use_deal_txt',
						'type'     => 'checkbox',
						'default'  => 0,
						'title'    => esc_html__( 'Use "Deal" text instead of "Sale"', 'wp-coupon' ),
					),

				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 FOOTER
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Footer', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-photo',
				'submenu' => true,
				'fields' => array(

					array(
						'id'       => 'before_footer',
						'type'     => 'editor',
						'title'    => esc_html__( 'Before footer', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Note: This field only display on homepage', 'wp-coupon' ),
						'default'  => '',
					),

					array(
						'id'      => 'before_footer_apply',
						'type'    => 'radio',
						'title'   => esc_html__( 'Before Footer Display', 'wp-coupon' ),
						'desc'    => esc_html__( 'Note: Setting home page goto Settings -> Reading -> Front page displays -> Check static page -> Select a page', 'wp-coupon' ),
						'default' => 'home',
						'required' => array( 'footer_widgets', '=', true ),
						'options' => array(
							'home'   => esc_html__( 'Apply for home page only.', 'wp-coupon' ),
							'all'   => esc_html__( 'Apply for all pages.', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'footer_widgets',
						'type'     => 'switch',
						'title'    => esc_html__( 'Enable footer widgets area.', 'wp-coupon' ),
						'default'  => true,
					),
					array(
						'id'      => 'footer_columns',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Footer Columns', 'wp-coupon' ),
						'desc'    => esc_html__( 'Select the number of columns you would like for your footer widgets area.', 'wp-coupon' ),
						'default' => '4',
						'required' => array( 'footer_widgets', '=', true ),
						'options' => array(
							'1'   => esc_html__( '1 Column', 'wp-coupon' ),
							'2'   => esc_html__( '2 Columns', 'wp-coupon' ),
							'3'   => esc_html__( '3 Columns', 'wp-coupon' ),
							'4'   => esc_html__( '4 Columns', 'wp-coupon' ),
						),
					),

					array(
						'id'       => 'footer_columns_layout_2',
						'type'     => 'text',
						'required' => array( 'footer_columns', '=', 2 ),
						'default' => '8+8',
						'title'    => esc_html__( 'Footer 2 Columns layout', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Custom footer columns width', 'wp-coupon' ),
						'desc'     => esc_html__( 'Enter int numbers and sum of them must smaller or equal 16, separated by "+"', 'wp-coupon' ),
					),

					array(
						'id'       => 'footer_columns_layout_3',
						'type'     => 'text',
						'default' => '6+5+5',
						'required' => array( 'footer_columns', '=', 3 ),
						'title'    => esc_html__( 'Footer 3 Columns layout', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Custom footer columns width', 'wp-coupon' ),
						'desc'     => esc_html__( 'Enter int numbers and sum of them must smaller or equal 16, separated by "+"', 'wp-coupon' ),
					),

					array(
						'id'       => 'footer_columns_layout_4',
						'type'     => 'text',
						'default' => '4+4+4+4',
						'required' => array( 'footer_columns', '=', 4 ),
						'title'    => esc_html__( 'Footer 4 Columns layout', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Custom footer columns width', 'wp-coupon' ),
						'desc'     => esc_html__( 'Enter int numbers and sum of them must smaller or equal 16, separated by "+"', 'wp-coupon' ),
					),

					array(
						'id'       => 'footer_copyright',
						'type'     => 'textarea',
						'title'    => esc_html__( 'Footer Copyright', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Enter the copyright section text.', 'wp-coupon' ),
					),

					array(
						'id'       => 'enable_footer_author',
						'type'     => 'switch',
						'title'    => esc_html__( 'Enable theme author links.', 'wp-coupon' ),
						'default'  => true,
					),

					array(
						'id'       => 'footer_custom_color',
						'type'     => 'switch',
						'title'    => esc_html__( 'Custom your footer style?', 'wp-coupon' ),
						'default'  => false,
					),
					array(
						'id'       => 'footer_bg',
						'type'     => 'background',
						// 'compiler' => true,
						'output'   => array( '.site-footer ' ),
						'title'    => esc_html__( 'Footer Background', 'wp-coupon' ),
						'required' => array( 'footer_custom_color', '=', true ),
						'default'  => array(
							'background-color' => '#222222',
						),
					),
					array(
						'id'       => 'footer_text_color',
						'type'     => 'color',
						'compiler' => true,
						'output'   => array( '.site-footer, .site-footer .widget, .site-footer p' ),
						'title'    => esc_html__( 'Footer Text Color', 'wp-coupon' ),
						'default'  => '#777777',
						'required' => array( 'footer_custom_color', '=', true ),
					),
					array(
						'id'       => 'footer_link_color',
						'type'     => 'color',
						'compiler' => true,
						'output'   => array( '.site-footer a, .site-footer .widget a' ),
						'title'    => esc_html__( 'Footer Link Color', 'wp-coupon' ),
						'default'  => '#CCCCCC',
						'required' => array( 'footer_custom_color', '=', true ),
					),
					array(
						'id'       => 'footer_link_color_hover',
						'type'     => 'color',
						'compiler' => true,
						'output'   => array( '.site-footer a:hover, .site-footer .widget a:hover' ),
						'title'    => esc_html__( 'Footer Link Color Hover', 'wp-coupon' ),
						'default'  => '#ffffff',
						'required' => array( 'footer_custom_color', '=', true ),
					),
					array(
						'id'       => 'footer_widget_title_color',
						'type'     => 'color',
						'compiler' => true,
						'output'   => array( '.site-footer .footer-columns .footer-column .widget .widget-title, .site-footer #wp-calendar caption' ),
						'title'    => esc_html__( 'Footer Widget Title Color', 'wp-coupon' ),
						'default'  => '#777777',
						'required' => array( 'footer_custom_color', '=', true ),
					),
				),
			);

			/*
			--------------------------------------------------------*/
			/*
			 EMAIL
			/*--------------------------------------------------------*/
			$this->sections[] = array(
				'title'  => esc_html__( 'Email', 'wp-coupon' ),
				'desc'   => '',
				'icon'   => 'el-icon-envelope',
				'submenu' => true,
				'fields' => array(

					array(
						'id'       => 'email_share_coupon_title',
						'type'     => 'text',
						'title'    => esc_html__( 'Share coupon code email title', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Available Tags: {coupon_title}, {coupon_description}, {coupon_destination_url}, {coupon_print_image_url}, {coupon_code}, {store_name}, {store_go_out_url}, {store_url}, {store_aff_url}, {home_url}, {share_email}', 'wp-coupon' ),
						'default'  => '{coupon_title}',
					),

					array(
						'id'       => 'email_share_coupon_code',
						'type'     => 'editor',
						'title'    => esc_html__( 'Share coupon code email template', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Available Tags: {coupon_title}, {coupon_description}, {coupon_destination_url}, {coupon_print_image}, {coupon_print_image_url}, {coupon_code}, {store_name}, {store_image}, {store_go_out_url}, {store_url}, {store_aff_url}, {home_url}, {share_email}', 'wp-coupon' ),
						'default'  => wpcoupon_get_share_email_template( 'code' ),
					),

					array(
						'id'       => 'email_share_coupon_sale',
						'type'     => 'editor',
						'title'    => esc_html__( 'Share coupon sale email template', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Available Tags: {coupon_title}, {coupon_description}, {coupon_destination_url}, {coupon_print_image}, {coupon_print_image_url}, {coupon_code}, {store_name}, {store_image}, {store_go_out_url}, {store_url}, {store_aff_url}, {home_url}, {share_email}', 'wp-coupon' ),
						'default'  => wpcoupon_get_share_email_template( 'sale' ),
					),

					array(
						'id'       => 'email_share_coupon_print',
						'type'     => 'editor',
						'title'    => esc_html__( 'Share coupon print email template', 'wp-coupon' ),
						'subtitle' => esc_html__( 'Available Tags: {coupon_title}, {coupon_description}, {coupon_destination_url}, {coupon_print_image}, {coupon_print_image_url}, {coupon_code}, {store_name}, {store_image}, {store_go_out_url}, {store_url}, {store_aff_url}, {home_url}, {share_email}', 'wp-coupon' ),
						'default'  => wpcoupon_get_share_email_template( 'print' ),
					),
				),
			);

			$this->sections = apply_filters( 'wpcoupon_more_options_settings', $this->sections );

		}

	}

	global $reduxConfig;
	function wpcoupon_options_init() {
		global $reduxConfig;
		// force remove sample redux demo option
		delete_option( 'ReduxFrameworkPlugin' );
		$reduxConfig = new WPCoupon_Theme_Options_Config();
	}
	add_action( 'init', 'wpcoupon_options_init' );

}


/**
 * Removes the demo link and the notice of integrated demo from the redux-framework plugin
 */
if ( ! function_exists( 'wp_coupon_remove_demo' ) ) {
	function wp_coupon_remove_demo() {
		// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
		if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
			remove_filter(
				'plugin_row_meta',
				array(
					ReduxFrameworkPlugin::instance(),
					'plugin_metalinks',
				),
				null,
				2
			);

			// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
			remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
		}
	}
}
wp_coupon_remove_demo();



/*
 * Load Redux extensions
 */
function wpcoupon_register_redux_extensions( $ReduxFramework ) {
	$path    = get_template_directory() . '/inc/redux-extensions/';

	$folders = scandir( $path, 1 );

	foreach ( $folders as $folder ) {
		if ( $folder === '.' or $folder === '..' or ! is_dir( $path . $folder ) ) {
			continue;
		}
		$extension_class = 'ReduxFramework_extension_' . $folder;
		if ( ! class_exists( $extension_class ) ) {
			// In case you wanted override your override, hah.
			$class_file = $path . $folder . '/extension_' . $folder . '.php';

			if ( is_file( $class_file ) ) {
				require_once $class_file;
			}
		}

		if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
			$ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
		}
	}
}
add_action( 'redux/extensions/before', 'wpcoupon_register_redux_extensions' );
