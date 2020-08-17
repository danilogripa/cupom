<?php
	
////////////////////////////
// SETTINGS PAGE 
////////////////////////////

function fca_ga_plugin_menu() {
	add_options_page( 
		__( 'Google Analytics Manager', 'fca-ga' ),
		__( 'Google Analytics Manager', 'fca-ga' ),
		'manage_options',
		'fca_ga_settings_page',
		'fca_ga_settings_page'
	);
}
add_action( 'admin_menu', 'fca_ga_plugin_menu' );

//ENQUEUE ANY SCRIPTS OR CSS FOR OUR ADMIN PAGE EDITOR
function fca_ga_admin_enqueue() {

	wp_enqueue_style('dashicons');
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'fca_ga_select2', FCA_GA_PLUGINS_URL . '/includes/select2/select2.min.js', array(), FCA_GA_PLUGIN_VER, true );
	wp_enqueue_style( 'fca_ga_select2', FCA_GA_PLUGINS_URL . '/includes/select2/select2.min.css', array(), FCA_GA_PLUGIN_VER );
	
	wp_enqueue_style( 'fca_ga_tooltipster_stylesheet', FCA_GA_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.css', array(), FCA_GA_PLUGIN_VER );
	wp_enqueue_style( 'fca_ga_tooltipster_borderless_css', FCA_GA_PLUGINS_URL . '/includes/tooltipster/tooltipster-borderless.min.css', array(), FCA_GA_PLUGIN_VER );
	wp_enqueue_script( 'fca_ga_tooltipster_js',FCA_GA_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.js', array('jquery'), FCA_GA_PLUGIN_VER, true );
				
	wp_enqueue_script('fca_ga_admin_js', FCA_GA_PLUGINS_URL . '/includes/editor/admin.min.js', array( 'jquery', 'fca_ga_select2' ), FCA_GA_PLUGIN_VER, true );		
	wp_enqueue_style( 'fca_ga_admin_stylesheet', FCA_GA_PLUGINS_URL . '/includes/editor/admin.min.css', array(), FCA_GA_PLUGIN_VER );
	
	$admin_data = array (
		'ajaxurl' => admin_url ( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'fca_ga_admin_nonce' ),
	);
	
	wp_localize_script( 'fca_ga_admin_js', 'adminData', $admin_data );
	
}

function fca_ga_admin_notice_save() {
	echo '<div id="fca-ga-notice-save" style="padding-bottom: 10px;" class="notice notice-success is-dismissible">';
		echo '<p><strong>' . __( "Settings saved.", 'fca-ga' ) . '</strong></p>';
	echo '</div>';
}

function fca_ga_settings_page() {
	
	fca_ga_admin_enqueue();
	
	if ( isSet( $_POST['fca_ga_save'] ) ) {
		fca_ga_settings_save();	
		fca_ga_admin_notice_save();		
	}	
	$options = get_option( 'fca_ga', true );
	$id = empty ( $options['id'] ) ? '' : $options['id'];
	$exclude = empty ( $options['exclude'] ) ? array() : $options['exclude'];
	//DEFAULT EXCLUDE TO ADMIN & EDITOR
	$exclude = empty ( $options['has_save'] ) ? array( 'Administrator', 'Editor' ) : $exclude;
	
	$html = '<form style="display: none" action="" method="post" id="fca_ga_main_form">';
		
		$html .= '<h1>' .  __('Google Analytics by Fatcat Apps', 'fca-ga') . '</h1>';
		$html .= '<p>' . sprintf(  __('Need help? %1$sRead our quick-start guide.%2$s', 'fca-ga'), '<a href="https://fatcatapps.com/analytics-cat-quick-start/" target="_blank">', '</a>' ) . '</p>';
		
		//ADD A HIDDEN INPUT TO DETERMINE IF WE HAVE AN EMPTY SAVE OR NOT
		$html .= fca_ga_input ( 'has_save', '', true, 'hidden' );
		
		$html .= '<table class="fca_ga_setting_table" >';
			$html .= "<tr>";
				$html .= '<th>' . __('Google Analytics ID', 'fca-ga') . '</th>';
				$html .= '<td id="fca-ga-helptext" title="' . __('Your Google Analytics ID should only contain numbers', 'fca-ga') . '" >' . fca_ga_input ( 'id', 'e.g. UA-12345678-1', $id, 'text' );
				$html .= '<a class="fca_ga_hint" href="https://fatcatapps.com/analytics-cat-quick-start#ga-id" target="_blank">' . __( 'What is my Google Analytics ID?', 'fca-ga' ) . '</a>';
				$html .= '</td>';
			$html .= "</tr>";
			$html .= "<tr>";
				$html .= '<th>' . __('Exclude Users', 'fca-ga') . '</th>';
				$html .= '<td>' . fca_ga_input ( 'exclude', '', $exclude, 'roles' );
				$html .= '<p class="fca_ga_hint">' . __( 'Logged in users selected above will not trigger analytics tracking.', 'fca-ga' ) . '</p>';
				$html .= '</td>';
			$html .= "</tr>";

		$html .= '</table>';
		
		$html .= '<button type="submit" name="fca_ga_save" class="button button-primary">' . __('Save', 'fca-ga') . '</button>';
	
	$html .= '</form>';
	
	
	echo $html;
}

function fca_ga_settings_save() {
	$data = fca_ga_escape_input ( $_POST['fca_ga'] );
	update_option( 'fca_ga', $data );
}