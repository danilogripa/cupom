/* jshint asi: true */
var $deactivateButton

jQuery(document).ready(function($){
	
	$deactivateButton = $('#the-list tr.active').filter( function() { return $(this).data('plugin') === 'analytics-cat/fca-ga.php' } ).find('.deactivate a')
		
	$deactivateButton.click(function(e){
		e.preventDefault()
		$deactivateButton.unbind('click')
		$('body').append(fca_ga.html)
		fca_ga_uninstall_button_handlers()
		
	})
}) 

function fca_ga_uninstall_button_handlers() {
	var $ = jQuery
	$('#fca-deactivate-skip').click(function(){
		window.location.href = $deactivateButton.attr('href')
	})
	$('#fca-deactivate-send').click(function(){
		$(this).html('...')
		$('#fca-deactivate-skip').hide()
		$.ajax({
			url: fca_ga.ajaxurl,
			type: 'POST',
			data: {
				"action": "fca_ga_uninstall",
				"nonce": fca_ga.nonce,
				"msg": $('#fca-deactivate-textarea').val()
			}
		}).done( function( response ) {
			console.log ( response )
			window.location.href = $deactivateButton.attr('href')			
		})	
	})
	
}