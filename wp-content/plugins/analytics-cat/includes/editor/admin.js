/* jshint asi: true */
jQuery(document).ready(function($) {
	
	$('.fca_ga_multiselect').select2()
	$('#fca-ga-helptext').tooltipster( {trigger: 'custom', timer: 6000, maxWidth: 350, theme: ['tooltipster-borderless', 'tooltipster-fca-ga'] } )
	$('#fca_ga_main_form').show()
	
/*	$('.fca-ga-id').on('input', function(e){
		var value = $(this).val()
		if ( !(/^\d+$/.test(value)) && value !== '' ) {
			$(this).val('')
			$('#fca-ga-helptext').tooltipster('open')
		}
	})
*/
	if ( $('.fca-ga-id').val() !== '' ) {
		$('#fca-ga-setup-notice').hide()
	}
	
})
