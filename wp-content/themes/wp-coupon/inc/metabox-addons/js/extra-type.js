jQuery( document ).ready( function( $ ) {

    $( '.st-condition-field' ).each( function() {
        var f = $( this );
        var show_on = f.data( 'show-on' );
        var show_when = f.data( 'show-when' );

        if ( show_on != '' ){
            if (  $('[name="'+show_on+'"]').val() !== show_when ) {
                f.hide();
            }

            $('[name="'+show_on+'"]').change( function() {
                var e = $( this );
                var v = e.val();
                $('[data-show-on="'+show_on+'"]').hide();
                $('[data-show-on="'+show_on+'"][data-show-when="'+v+'"]').show();
            } );
        }

    } );

    $('#_wpc_show_cover').change( function(){

        var is_check  = $( this).is(':checked');
        // Page config condition
        var v = $( '#_wpc_show_header').val();

        if ( v == 'on' && is_check ) {
            $( '.cmb2-id--st-cover-image, .cmb2-id--st-cover-color').removeClass('display-none hide').show();
        } else{
            $( '.cmb2-id--st-cover-image, .cmb2-id--st-cover-color').addClass('display-none hide').hide();
        }

    } );

    // Page config condition
    $( '#_wpc_show_header').change( function( e ) {

        var v = $( this).val();
        if ( v == 'on' ) {
            $( '.cmb2-id--st-show-breadcrumb, .cmb2-id--st-custom-title, .cmb2-id--st-custom-title, .cmb2-id--st-cover-image, .cmb2-id--st-cover-color, .cmb2-id--st-show-cover').removeClass('display-none hide').show();
        } else{
            $( '.cmb2-id--st-show-breadcrumb, .cmb2-id--st-custom-title, .cmb2-id--st-custom-title, .cmb2-id--st-cover-image, .cmb2-id--st-cover-color, .cmb2-id--st-show-cover').addClass('display-none hide').hide();
        }

        $( '#_wpc_show_cover').trigger('change');

    } );

    $( '#_wpc_show_header').trigger('change');
    $( '#_wpc_show_cover').trigger('change');

} );