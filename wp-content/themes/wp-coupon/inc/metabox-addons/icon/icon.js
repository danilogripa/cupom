/**
 * Created by truongsa on 12/25/15.
 */

jQuery( document ).ready( function( $ ){
    var icons = CMB2_ICON.icons.split(',');
   // $( '.' )
    var icon_html ='';
    $.each( icons, function( i ){
        icon_html += '<div class="icon-wrapper" title="'+icons[i]+'"><i class="'+icons[i]+'"></i></div>';
    } );
    if ( icon_html != '' ){
        icon_html = '<div class="icon-wrapper" title=""><i class="icon none-icon">&nbsp;</i></div>'+icon_html;
    }
    $( '.cmb2-icon-picker').each( function(){
        var picker = $( this );
        $( '.cmb2-list-icons', picker ).html( icon_html );

        picker.on( 'keyup', '.cmb2-search-icons' ,function(){
            $( '.cmb2-list-icons', picker ).removeClass( 'hide' );
            var v = $( this ).val();
            if ( v != '' ){
                v = v.toLocaleLowerCase();
                $( '.icon-wrapper', picker ).addClass( 'hide' );
                $( '.icon-wrapper[title*="'+v+'"]',  picker ).removeClass( 'hide' );

            } else {
                $( '.icon-wrapper', picker ).removeClass( 'hide' );
            }
        });


        picker.on( 'blur', '.cmb2-search-icons' ,function(){
            var v = $( this ).val();
            if ( v != '' ){

            } else {
                $( '.cmb2-list-icons', picker ).addClass( 'hide' );
            }
        });

        picker.on( 'click', '.cmb2-icon-selected' ,function( e ){
            e.preventDefault();
            $( '.cmb2-list-icons', picker ).toggleClass( 'hide' );
        });

        picker.on( 'click', '.icon-wrapper' ,function( e ){
            e.preventDefault();
            var v =  $( this).attr( 'title' );
            $( '.cmb2-icon-value', picker).val( v );
            $( '.cmb2-icon-selected', picker).html( '<i class="'+v+'"></i>' );
        });

    } );


} );


