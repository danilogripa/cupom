
jQuery( document).ready(function ( $ ) {

    var wp_ci_number =  window.wp_ci_number ? window.wp_ci_number : 0;
    $( '#wp_coupon_import_file').change(  function(){
        var v = $( this).val();
        console.log( v );
        var ext = v.substr( ( v.lastIndexOf('.') + 1 ) );
        if ( ext.toLowerCase() != 'csv' &&  ext.toLowerCase() != 'xls' && ext.toLowerCase() != 'xlsx' ) {
            $( this).val( '' );
            alert( wp_coupon_import.warning_file + '| '+ext);
        } else {
            if ( v ) {
                $( '#wp_coupon_import_submit').removeAttr( 'disabled' );
            } else {
                $( '#wp_coupon_import_submit').attr( 'disabled', 'disabled' );
            }
        }
    } );

    $( '#wp_coupon_import_file').each( function(){
        var v = $( this ).val();
        if ( v ) {
            $( '#wp_coupon_import_submit').removeAttr( 'disabled' );
        } else {
            $( '#wp_coupon_import_submit').attr( 'disabled', 'disabled' );
        }

    } );

    function setProgressBar( index ){
        var p;
        if ( wp_ci_number > 0 ) {
            p = ( index / wp_ci_number ) * 100;
            if (p < 0) {
                p = 0;
            }
        } else {
            p = 0;
        }
        p = p.toFixed(2);

        $( '.wp-cie-progress .wp-cie-label').html( p + '%' );
        $( '.wp-cie-progress .wp-cie-percent').css( { width:  p + '%' }  );
    }

    function import_start(){
        $( '.wp-cie-actions' ).hide();
        $( '.wp-cie-progress-log').append( wp_coupon_import.import_start );
        $( '.wp-cie-progress-log').append( '<br/>' );
        if ( wp_ci_number > 0 ) {
            sendAjax( 1, true );
        }
        $( '.wp-coupon-import-wrapper').slideUp(300);
        $( '.wp-cie-settings').slideUp(300);
        $( '.wp-cie-settings').slideUp( 200, function (){
            $( '.wp-cie-progress-wrapper').slideDown(200);
        } );
    }
    function import_completed(){
        $( '.wp-cie-progress-log').append( wp_coupon_import.import_done );
        $( '.wp-cie-progress-log').append( '<br/>' );
        setTimeout( function(){
            $( '.wp-coupon-import-wrapper').slideDown(300);
            $( '.wp-cie-settings').slideUp(300);
        }, 1000 );
    }

    function sendAjax( index, is_first ){
        if (  typeof is_first === "undefined" ) {
            is_first = false;
        }
        var form_data = '';
        if ( is_first ) {
            form_data = $( 'form.wp-cie-settings').serialize();
        }

        $( window ).on( 'beforeunload', function( e ) {
            e = e || window.event;
            if (e) {
                e.returnValue = wp_coupon_import.confirm_close_window;
            }
            return wp_coupon_import.confirm_close_window;
        });

        $.ajax({
            url: wp_coupon_import.ajax_url,
            data: {
                action: 'coupons_import',
                index: index,
                nonce: wp_coupon_import.nonce,
                is_first: is_first ?  1: 0,
                form_data: form_data,
            },
            dataType: 'json',
            type: 'post',
            cache: false,
            success: function( response ){
                setProgressBar( index );
                $( '.wp-cie-progress-log').append( response.data );
                $( '.wp-cie-progress-log').append( '<br/>' );

                if ( index >= wp_ci_number) {
                    setProgressBar(wp_ci_number);
                    import_completed();
                    $( window ).off( 'beforeunload');
                } else {
                    sendAjax(index + 1);
                }
            }
        });
    }


    var l = $( '.wp-cie-settings select').length;

    function check_field_selected(){
        var n = 0;
        $( '.wp-cie-settings select').each( function(){
            var tr = $( this).closest( 'tr' );
            if ( $( this).val() != '' ) {
                n ++ ;
                tr.addClass( 'selected' );
            } else {
                tr.addClass( 'empty-selected' );
            }
        } );
        return n;
    }
    var have_selected =  check_field_selected();

    $( '.wp-cie-settings select').on( 'change',  function(){
        var tr = $( this).closest( 'tr' );
        if ( $( this).val() != '' ) {
            have_selected ++ ;
            tr.addClass( 'selected').removeClass( 'empty-selected' );
        } else {
            tr.addClass( 'empty-selected').removeClass( 'selected' );
        }
    } );


    $( '.wp-cie-start-import').on( 'click', function( e ){
        e.preventDefault();
        if ( check_field_selected() == l ) {
            import_start();
        } else {
            var c = confirm( wp_coupon_import.confirm_import );
            if ( c ) {
                import_start();
            }
        }

    } );

    // All fields already selected, let start import.
    if ( wp_ci_number > 0 && have_selected == l ) {
        $( '.wp-cie-start-import').trigger( 'click' );
    } else {
        if ( wp_ci_number > 0 ) {
            $( '.wp-coupon-import-wrapper').hide();
        }
    }


    $( '.wp-cie-cancel-import').on( 'click', function( e ){
        var c = confirm( wp_coupon_import.confirm_cancel );
        if ( c ) {
            return true;
        }
        return false;
    } );

} );