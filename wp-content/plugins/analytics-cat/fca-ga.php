<?php
/*
	Plugin Name: Analytics Cat Free
	Plugin URI: https://fatcatapps.com/analytics-cat
	Description: Add Your Google Analytics / Universal Analytics Tracking Code To Your Site With Ease.
	Text Domain: fca-ga
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 1.0.4
*/


// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined('FCA_GA_PLUGIN_DIR') ) {
	
	//DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_GA_PLUGIN_VER', '1.0.4' );
	define( 'FCA_GA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_GA_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_GA_PLUGINS_BASENAME', plugin_basename(__FILE__) );
	define( 'FCA_GA_PLUGIN_FILE', __FILE__ );
	define( 'FCA_GA_PLUGIN_PACKAGE', 'Free' ); //DONT CHANGE THIS - BREAKS AUTO UPDATER
	
	//LOAD CORE
	include_once( FCA_GA_PLUGIN_DIR . '/includes/api.php' );
	
	//LOAD MODULES
	include_once( FCA_GA_PLUGIN_DIR . '/includes/editor/editor.php' );

	//ACTIVATION HOOK
	function fca_ga_activation() {
		fca_ga_api_action( 'Activated Google Analytics by Fatcat Apps Free' );
	}
	register_activation_hook( FCA_GA_PLUGIN_FILE, 'fca_ga_activation' );
	
	//DEACTIVATION HOOK
	function fca_ga_deactivation() {
		fca_ga_api_action( 'Deactivated Google Analytics by Fatcat Apps Free' );
	}
	register_deactivation_hook( FCA_GA_PLUGIN_FILE, 'fca_ga_deactivation' );
	
	//INSERT SCRIPT
	function fca_ga_maybe_add_script() {

		$roles = wp_get_current_user()->roles;
		
		$options = get_option( 'fca_ga', true );
		$id = empty ( $options['id'] ) ? '' : $options['id'];
		$exclude = empty ( $options['exclude'] ) ? array() : $options['exclude'];
		$do_script = count( array_intersect( array_map( 'strtolower', $roles), array_map( 'strtolower', $exclude ) ) ) == 0;
				
		if ( !empty( $options['id'] ) && $do_script ) {
			
			ob_start(); ?>
			
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo $id ?>', 'auto');
			ga('send', 'pageview');

			</script>
			
			<?php
			echo ob_get_clean();
		}
	}
	add_action('wp_head', 'fca_ga_maybe_add_script');
	
	////////////////////////////
	// LOCALIZATION
	////////////////////////////
	
	function fca_ga_load_localization() {
		load_plugin_textdomain( 'fca-ga', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'fca_ga_load_localization' );
	
	////////////////////////////
	// FUNCTIONS
	////////////////////////////
		
	//RETURN GENERIC INPUT HTML
	function fca_ga_input ( $name, $placeholder = '', $value = '', $type = 'text' ) {
	
		$html = "<div class='fca-ga-field fca-ga-field-$type'>";
		
			switch ( $type ) {
				
				case 'checkbox':
					$checked = !empty( $value ) ? "checked='checked'" : '';
					
					$html .= "<div class='onoffswitch'>";
						$html .= "<input style='display:none;' type='checkbox' id='fca_ga[$name]' class='onoffswitch-checkbox fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]' $checked>"; 
						$html .= "<label class='onoffswitch-label' for='fca_ga[$name]'><span class='onoffswitch-inner' data-content-on='ON' data-content-off='OFF'><span class='onoffswitch-switch'></span></span></label>";
					$html .= "</div>";
					break;
					
				case 'textarea':
					$html .= "<textarea placeholder='$placeholder' class='fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]'>$value</textarea>";
					break;
					
				case 'image':
					$html .= "<input type='hidden' class='fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]' value='$value'>";
					$html .= "<button type='button' class='button-secondary fca_ga_image_upload_btn'>" . __('Add Image', 'fca-ga') . "</button>";
					$html .= "<img class='fca_ga_image' style='max-width: 252px' src='$value'>";
			
					$html .= "<div class='fca_ga_image_hover_controls'>";
						$html .= "<button type='button' class='button-secondary fca_ga_image_change_btn'>" . __('Change', 'fca-ga') . "</button>";
						$html .= "<button type='button' class='button-secondary fca_ga_image_revert_btn'>" . __('Remove', 'fca-ga') . "</button>";
					$html .=  '</div>';
					break;
				case 'color':
					$html .= "<input type='hidden' placeholder='$placeholder' class='fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]' value='$value'>";
					break;
				case 'editor':
					ob_start();
					wp_editor( $value, $name, array() );
					$html .= ob_get_clean();
					break;
				case 'datepicker':
					$html .= "<input type='text' placeholder='$placeholder' class='fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]' value='$value'>";
					break;
				case 'roles':
					$roles = get_editable_roles();
					forEach ( $roles as $role ) {
						$options[] = $role['name'];
					}

					$html = "<select name='fca_ga[$name][]' data-placeholder='$placeholder' multiple='multiple' style='width: 100%; border: 1px solid #ddd; border-radius: 0;' class='fca_ga_multiselect'>";
						forEach ( $options as $role ) {
							if ( in_array($role, $value) ) {
								$html .= "<option value='$role' selected='selected'>$role</option>";
							} else {
								$html .= "<option value='$role'>$role</option>";
							}
						}
					
					$html .= "</select>";
					break;
					
				default: 
					$html .= "<input type='$type' placeholder='$placeholder' class='fca-ga-input-$type fca-ga-$name' name='fca_ga[$name]' value='$value'>";
			}
		
		$html .= '</div>';
		
		return $html;
	}
	
	function fca_ga_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
		return "<span class='$icon fca_ga_tooltip' title='" . htmlentities( $text ) . "'></span>";
	}
	
	function fca_ga_convert_entities ( $array ) {
		$array = is_array($array) ? array_map('fca_ga_convert_entities', $array) : html_entity_decode( $array, ENT_QUOTES );
		return $array;
	}

	function fca_ga_escape_input ($data) {
		
		if ( is_array ( $data ) ) {
			forEach ( $data as $k => $v ) {
				$data[$k] = fca_ga_escape_input($v);
			}
			return $data;
		}
		
		$data = wp_kses_post( $data );
			
		return $data;

	}
	
	function fca_ga_add_plugin_action_links( $links ) {
		
		$url = admin_url('options-general.php?page=fca_ga_settings_page');
		
		$new_links = array(
			'configure' => "<a href='$url' >" . __('Configure Google Analytics', 'fca-ga' ) . '</a>'
		);
		
		$links = array_merge( $new_links, $links );
	
		return $links;
		
	}
	add_filter( 'plugin_action_links_' . FCA_GA_PLUGINS_BASENAME, 'fca_ga_add_plugin_action_links' );
	
	//ADD NAG IF NO GA TRACKING CODE IS SET
	function fca_ga_admin_notice() {
		$options = get_option( 'fca_ga', true );
 
		if ( empty( $options['id'] ) ) {
			$url = admin_url( 'options-general.php?page=fca_ga_settings_page' );
		
			echo '<div id="fca-ga-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<img style="float:left; margin-right: 16px;" height="120" width="120" src="' . FCA_GA_PLUGINS_URL . '/assets/googlecat_icon128_128_360.png' . '">';
				echo '<p><strong>' . __( "Thank you for installing Analytics Cat.", 'fca-ga' ) . '</strong></p>';
				echo '<p>' . __( "Ready to get started?", 'fca-ga' ) . '</p>';
				echo "<a href='$url' type='button' class='button button-primary' style='margin-top: 25px;'>" . __( 'Set up Google Analytics', 'fca-ga' ) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';
		}
	
	}
	add_action( 'admin_notices', 'fca_ga_admin_notice' );
	
	//DEACTIVATION SURVEY
	function fca_ga_admin_deactivation_survey( $hook ) {
		if ( $hook === 'plugins.php' ) {
			
			ob_start(); ?>
			
			<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
				<h3 style="font-size: 14px; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php _e( 'Sorry to see you go', 'fca-ga' ) ?></h3>
				<p><?php _e( 'Hi, this is David, the creator of Google Analytics by Fatcat Apps. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'fca-ga' ) ?>
				</p>
				<p><?php _e( 'I have a quick question that I hope you’ll answer to help us make Google Analytics by Fatcat Apps better: what made you deactivate?', 'fca-ga' ) ?>
				</p>
				<p><?php _e( 'You can leave me a message below. I’d really appreciate it.', 'fca-ga' ) ?>
				</p>
				
				<p><textarea style='width: 100%;' id='fca-deactivate-textarea' placeholder='<?php _e( 'What made you deactivate?', 'fca-ga' ) ?>'></textarea></p>
				
				<div style='float: right;' id='fca-deactivate-nav'>
					<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-deactivate-skip'><?php _e( 'Skip', 'fca-ga' ) ?></button>
					<button type='button' class='button button-primary' id='fca-deactivate-send'><?php _e( 'Send Feedback', 'fca-ga' ) ?></button>
				</div>
			
			</div>
			
			<?php
				
			$html = ob_get_clean();
			
			$data = array(
				'html' => $html,
				'nonce' => wp_create_nonce( 'fca_ga_uninstall_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
						
			wp_enqueue_script('fca_ga_deactivation_js', FCA_GA_PLUGINS_URL . '/includes/deactivation.min.js', false, FCA_GA_PLUGIN_VER, true );
			wp_localize_script( 'fca_ga_deactivation_js', "fca_ga", $data );
		}
		
		
	}	
	add_action( 'admin_enqueue_scripts', 'fca_ga_admin_deactivation_survey' );
	
}