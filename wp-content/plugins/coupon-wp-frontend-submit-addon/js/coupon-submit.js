

jQuery( document ).ready( function( $ ) {

    var user_can_submit = true;
    if ( coupon_submit.who_can_submit != 'anyone' ) {
        if ( coupon_submit.is_logged_in == '1' || coupon_submit.is_logged_in == 1 ) {
            user_can_submit = true;
        } else {
            user_can_submit = false;
        }
    }

    $( '.st-datepicker').each( function () {
        var f = $( this );
        var format = f.data('format') || 'dd/mm/yy',
            alt = f.data('alt');

        f.datepicker( {
            dateFormat: format,
            altField: alt,
            altFormat: "yy-mm-dd"
        } );

    } );
    // switch input printable image/upload
    $( '.c-input-switcher').each( function() {
        var p = $( this );
        p.on( 'click', '.for-input',function ( e ) {
            e.preventDefault();
            $( this).hide();
            $( '.file-input', p ).hide();
            $( '.for-upload, .text-input', p ).show();
            $('input[name=coupon_image_type]', p ).val('url');
        } );
        p.on( 'click', '.for-upload' ,function ( e ) {
            e.preventDefault();
            $( this).hide();
            $( '.text-input, .for-input', p ).hide();
            $( '.for-input, .file-input', p ).show();
            $('input[name=coupon_image_type]', p ).val('upload');
        } );
    } );

    $( 'form .field-coupon-type select').change( function ( ) {
        var  s = $( this );
        var p = s.parents('form');
        var v = s.val();
        //alert( v );
        $( '.field-code, .c-tile-print' ,p).hide();
        $('.c-tile-others', p ).show();
        if ( v == 'sale' ) {
            $( '.field-print, .field-code' ,p).hide();
        } else if( v == 'print' ) {
            $( '.field-code, .c-tile-others' ,p).hide();
            $( '.c-tile-print' ,p).show();
        }  else {
            $( '.field-code' ,p).show();
        }
    } );

    $( 'form .field-coupon-type select').trigger('change');

    // validate coupon form
    $( '.st-coupon-form' ).submit( function () {

        if ( ! user_can_submit ) {
            alert( coupon_submit.login_notice );
            return false;
        }

        var f = $( this );
        f.addClass('loading');
        f.removeClass( 'success error' );
        var values = f.form('get values');
        var is_error = false;

        if ( typeof values.coupon_store !== undefined ) {
            if ( values.coupon_store  == '' || values.coupon_store  == 0 ) {
                $( 'input[name=coupon_store]', f).closest('.field').addClass('error');
                is_error = true;
            } else {
                $( 'input[name=coupon_store]', f).closest('.field').removeClass('error');
            }
        }

        // New store added
        if ( typeof values.new_store_name !== undefined && typeof values.new_store_url !== undefined ) {
            if ( values.new_store_name  != '' && values.new_store_url != '' ) {
                $('input[name=coupon_store]', f).closest('.field').removeClass('error');
                is_error = false;
            }
        }

        if ( typeof values.coupon_cat !== undefined ) {
            if ( values.coupon_cat  == '' || values.coupon_cat <= 0 ) {
                $( 'select[name=coupon_cat]', f).closest('.field').addClass('error');
                is_error = true;
            } else {
                $( 'select[name=coupon_cat]', f).closest('.field').removeClass('error');
            }
        }

        if ( typeof values.coupon_type !== undefined ) {
            // If is new the check image
            if ( typeof values.coupon_id === undefined || values.coupon_id == ''  ) {
                //alert( values.coupon_type.length );
                if (values.coupon_type == '') {
                    $('select[name=coupon_type]', f).closest('.field').addClass('error');
                    is_error = true;
                } else {
                    $('select[name=coupon_type]', f).closest('.field').removeClass('error');
                }

                if (values.coupon_type == 'print') {
                    // if is unput url
                    if (values.coupon_image_type == 'url') {
                        // check url if fill
                        if (values.coupon_image_url == '') {
                            $('input[name=coupon_image_url]', f).closest('.field').addClass('error');
                            is_error = true;
                        } else {
                            $('input[name=coupon_image_url]', f).closest('.field').removeClass('error');
                        }

                    } else if (values.coupon_image_file == '') { // check if select a file o not
                        $('input[name=coupon_image_file]', f).closest('.field').addClass('error');
                        is_error = true;
                    } else {
                        $('input[name=coupon_image_file]', f).closest('.field').removeClass('error');
                    }

                } else {
                    $('input[name=coupon_image_url]', f).closest('.field').removeClass('error');
                    $('input[name=coupon_image_file]', f).closest('.field').removeClass('error');
                }
            }
        }

        if ( typeof values.coupon_expires !== undefined ) {
            if ( values.coupon_expires == '' ) {
                if ( typeof values.coupon_expires_unknown !== undefined && values.coupon_expires_unknown == true ) {
                    $( 'input[name=coupon_expires]', f ).closest('.field').removeClass('error');
                } else {
                    $( 'input[name=coupon_expires]', f ).closest('.field').addClass('error');
                    is_error = true;
                }

            }
        }

        if ( typeof values.coupon_title !== undefined ) {
            if ( values.coupon_title == '' ) {
                $( 'input[name=coupon_title]', f ).closest('.field').addClass('error');
                is_error = true;
            } else {
                $( 'input[name=coupon_title]', f ).closest('.field').removeClass('error');
            }
        }

        if ( typeof values.coupon_title !== undefined ) {
            if ( values.coupon_title == '' ) {
                $( 'input[name=coupon_title]', f ).closest('.field').addClass('error');
                is_error = true;
            } else {
                $( 'input[name=coupon_title]', f ).closest('.field').removeClass('error');
            }
        }

        if ( typeof values.coupon_description !== undefined ) {
            if ( values.coupon_description == '' ) {
                $( 'textarea[name=coupon_description]', f ).closest('.field').addClass('error');
                is_error = true;
            } else {
                $( 'textarea[name=coupon_description]', f ).closest('.field').removeClass('error');
            }
        }

        if ( is_error ) {
            f.removeClass('loading');
            return false;
        }

        var processData = true, contentType = false;

        /**
         * Check if browsers support FormData object
         *
         * @see https://developer.mozilla.org/en-US/docs/Web/API/FormData/Using_FormData_Objects
         */
        var form_data = values;
        if ( typeof FormData !== "undefined" ) {
            form_data = new FormData( this );
            processData = false;
            contentType = false;
        } else {
            return true;
        }

        $.ajax( {
            url: ST.ajax_url,
            type: 'post',
            data: form_data,
            dataType: 'json',
            processData:  processData,
            contentType:  contentType,
            success: function( r ){
                f.removeClass('loading');
                if ( $( '.st-response-msg' ,f ).length > 0 ) {
                    $( '.st-response-msg' ,f).remove();
                }

                $( "input[name=coupon_image_file], input[name=coupon_image_url]", f).val('');

                if ( r.success ) {
                    f.addClass( 'success' );
                    // clear data if is add new
                    if (  typeof values.coupon_id == "undefined" || values.coupon_id == '' ) {
                        $('.st-datepicker').val('');
                        f.form('set values', {
                            coupon_image_url        : '',
                            coupon_aff_url          : '',
                            coupon_expires          : '',
                            coupon_expires_unknown  : false,
                            coupon_title            : '',
                            coupon_description      : '',
                            coupon_code             : '',
                            new_store_name          : '',
                            //coupon_store           : '',
                            new_store_url           : ''
                        });
                    } else {
                        //$( 'input[name=coupon_id]' ).val( r.coupon_id );
                        if ( typeof r.image_url !== "undefined" ) {
                            if ( $( '.image-thumb', f ).length ) {
                                $( '.image-thumb', f).remove();
                            }
                            $( '.field-image', f).append('<p class="image-thumb"><img src="'+r.image_url+'" alt=""></p>');
                        }
                    }

                } else {
                    f.addClass( 'error' );
                }
                $( r.data ).insertBefore( $( ".btn_primary" , f ) );

                $( '.message .close', f ).click( function() {
                    $(this).closest('.message').transition('fade');
                });

                // auto close message
                setTimeout( function(){
                    $( '.st-response-msg', f ).remove();
                }, 5000 );

            }
        } );
        return false;

    } );

    // icon-popup
    $('.icon-popup').popup();
    //-----------------------------------------------------------------------------------------
    var store_select_modal = $( '#wp-submit-coupon-stores');

    $( '.select-store-input').on( 'click', function( e ){
        e.preventDefault();
        store_select_modal.modal('show');
    } );

    $( 'body').on( 'click', '.save-sm-coupon', function( e ) {
        e.preventDefault();
        console.log( 'click' );
    } );

    $( 'body').on( 'click', '.show-new-store-form', function( e ){
        e.preventDefault();
        $( '.new-store-form').show( 500, function(){
            $( window).resize();
        } );

        $( '.new-store-name').focus();
    } );

    var _ajax_xhr = null;

    $( 'body' ).on( 'keyup', '.search-stores .search-store-input', function( e ){
        var s =  $( this).val();
        var p = $( this).closest( '.search-stores');
        if ( _ajax_xhr ) {
            _ajax_xhr.abort();
        }

        var list = $( '.cs-ajax-results');
        list.hide().html( '' );
        list.removeClass( 'not-found' );

        if ( s.length > 0 ) {
            p.addClass( 'loading' );
        } else {
            p.removeClass( 'loading' );
            list.hide( 200, function ( ) {
                $( window).resize();
            } );
        }

        _ajax_xhr = $.ajax( {
            url: ST.home_url,
            data: {
                ajax_sc: s,
            },
            type: 'GET',
            cache: false,
            success: function( res ) {
                _ajax_xhr = null;
                p.removeClass( 'loading' );
                $( '.store-add-new').show();
                if ( res.success ) {
                    if ( res.results.length > 0 ) {
                        // cs-ajax-results
                        $( '#wp-submit-coupon-stores .store-search-header').html( coupon_submit.still_not_found );

                        $.each( res.results, function( index, store ) {
                            var li = $( '<div></div>' );
                            li.attr( 'data-id', store.id );
                            li.addClass( 'item' );
                            li.append( '<div class="store-name">'+store.title+'</div><div class="store-home-url">'+store.home+'</div>' );
                            list.append( li );
                        } );

                        list.show( 500, function ( ) {
                            $( window).resize();
                        } );

                    } else {
                        if ( coupon_submit.new_store == '1' || coupon_submit.new_store == 1 ) {
                            $( '#wp-submit-coupon-stores .store-search-header').html( coupon_submit.not_found );
                        } else {
                            list.html( '<div class="ui warning message"><p>'+coupon_submit.not_found+'</p></div>' );
                            list.addClass( 'not-found' );
                            list.show( 200, function ( ) {
                                $( window).resize();
                            } );


                        }

                    }
                }
            }
        } );

    } );


    // Select store
    $( 'body').on( 'click', '.cs-ajax-results .item', function( e ) {
        e.preventDefault();
        var id = $( this ).attr( 'data-id' );
        var name = $( this).find( '.store-name' ).text( );
        $( '.select-coupon-store-id').val( id );
        $( '.select-coupon-store').val( name );
        store_select_modal.modal('hide');

        var list = $( '.cs-ajax-results');
        list.html( '' );
        list.hide();
        $( '.new-store-form, .store-add-new').hide();
        $( '.search-store-input').val( '' );

        // Clear upload file, URL
        $( 'input[name="coupon_store_file"], .new-store-url, .new-store-name, .new-store-home-url').val( '' );

    } );

    // Upload new store
    $( 'body').on( 'click', '#wp-submit-coupon-stores .new-store-logo', function( e ) {
        e.preventDefault();
        $( 'input[name="coupon_store_file"]').click();
    } );

    $( 'body').on( 'change', 'input[name="coupon_store_file"]', function(){
        var v = $( this).val();
        $('#wp-submit-coupon-stores .new-store-logo').closest('.field').removeClass( 'error' );
        $( '#wp-submit-coupon-stores .new-store-logo-placeholder').val( v );
    } );

    $( 'body').on( 'click', '.save-new-store-submit' , function( e ){
        e.preventDefault();
        var name    = $( '#wp-submit-coupon-stores .new-store-name').val();
        var url     = $( '#wp-submit-coupon-stores .new-store-home-url').val();
        var img     = $( '#wp-submit-coupon-stores .new-store-logo-placeholder').val();
        // Remove store selected ID
        $( '.select-coupon-store-id').val( '' );
        var can_submit = true;

        if ( name == '' ) {
            $( '#wp-submit-coupon-stores .new-store-name').closest( '.field' ).addClass( 'error' );
            can_submit = false;
        } else {
            $( '#wp-submit-coupon-stores .new-store-name').closest( '.field' ).removeClass( 'error' );
        }

        if ( url == '' ) {
            $( '#wp-submit-coupon-stores .new-store-home-url').closest( '.field' ).addClass( 'error' );
            can_submit = false;
        } else {
            $( '#wp-submit-coupon-stores .new-store-home-url').closest( '.field' ).removeClass( 'error' );
        }

        // Validate image
        if ( coupon_submit.new_store_logo == '1' || coupon_submit.new_store_logo == 1 ) {
            if ( img != '' ) {
                switch (img.substring(img.lastIndexOf('.') + 1).toLowerCase()) {
                    case 'gif':
                    case 'jpg':
                    case 'png':
                        break;
                    default:
                        // file invaild, clear selected file
                        $('input[name="coupon_store_file"]').val('');
                        can_submit = false;
                        $('#wp-submit-coupon-stores .new-store-logo').closest('.field').addClass('error');
                        break;
                }
            }
        }

        if ( can_submit ) {
            $( '.select-coupon-store').val( name );
            $( '.new-store-url').val( url );

            store_select_modal.modal('hide');
        }

    });


    //-----------------------------------------------------------------------------------------

} ) ;