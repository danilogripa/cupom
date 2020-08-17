/**
 * Created by truongsa on 10/15/15.
 */


var ST_Widgets =  function( id ){

    var $ = jQuery;
    var that = this;
    that.context = $( '#'+id );
    if ( that.context.hasClass('widget-ui-added') ){
        return that;
    } else {
        that.context.addClass('widget-ui-added');
    }

    that.template = $( '#'+id+'_template').html();
    that.uiItems = $( '.ui-items', that.context );


    that.uploader = function( $context ){

        var _item = $( '.image-upload', $context );
        _item.on( 'click', function(){
            var _media = _.clone( wp.media );
            var frame = wp.media({
                title : wp.media.view.l10n.addMedia,
                multiple : false,
                library : { type : 'image' },
                button : { text : 'Insert' }
            });

            frame.on('close',function() {
                // get selections and save to hidden input plus other AJAX stuff etc.
                var selection = frame.state().get('selection');
                // console.log(selection);
            });

            frame.on('select', function(){
                // Grab our attachment selection and construct a JSON representation of the model.
                var media_attachment = frame.state().get('selection').first().toJSON();
                // media_attachment= JSON.stringify(media_attachment);

                $( '.image_id', _item ).val( media_attachment.id  );
                var  preview, img_url;
                try {
                    if( typeof (media_attachment.sizes.thumbnail ) !== 'undefined'){
                        img_url = media_attachment.sizes.thumbnail.url;
                    }
                } catch( e ){
                    img_url = media_attachment.url;
                }

                $( '.image_url', _item ).val( img_url );
                preview = '<img src="'+img_url+'" alt="">';
                $( 'img', _item).remove();
                _item.append( preview );

            });

            frame.on('open',function() {

            });
            frame.open();

        } );


    };

    that._name =  function( item, index ){
        var itemName =  item.data('name') || '';

        $( '.item-index', item ).text( '#'+( index + 1 ) );
        if( itemName != '' ) {
            $( 'input, textarea, select', item ).each( function() {
                var input = $( this );
                var iName =  input.data('name') || '';
                if( iName != '' ) {
                    input.attr( 'name', itemName+'['+index+']['+iName+']' );
                }
            } );
        }
    };

    that._actions = function( item ) {
        // remove item
        $( '.remove', item ).on( 'click', function(){
            item.remove();
            that._rename();
            return false;
        } );

        // Close Item

        $( '.close', item ).on( 'click', function(){
            item.addClass('closed');
            return false;
        } );

        //toggle display
        $( '.toggle', item ).on( 'click', function(){
            item.toggleClass('closed');
            return false;
        } );

        // Remove thumbnail

        $( '.remove-thumbnail', item ).on( 'click', function(){

            $( '.thumb-preview img', item).remove();
            $( '.thumb-preview .image_url', item).val('');
            $( '.thumb-preview .image_id', item).val('');

            return false;
        } );

        // Live title
        $( 'input.live-title', item ).on( 'keyup', function(){
            $( '.live-item-title', item ).text( $( this).val() );
        } );



    };

    that._newItem = function(){
        var newItem =  $( that.template );
        that.uiItems.append( newItem );
        that.uploader( newItem );
        that._actions( newItem );
        that._rename();
    };

    that._rename = function(){
        $( '.item', that.uiItems ).each( function( i ){
            var item =  $( this );
            that._name( item, i );
        } );
    };

    //------------------

    $( '.new-item', that.context).click( function(){
        that._newItem();
        return false;
    } );


    $( '.item', that.uiItems ).each( function( i ){
        var item =  $( this );
        that._name( item, i );
        that.uploader( $( this ) );
        that._actions( item );
    } );


    that.uiItems.sortable( {
        placeholder: "item ui-sortable-placeholder",
        update: function( event, ui ) {
            // Re-update Item name
            that._rename();
        }
    });

};


jQuery( document ).ready( function( $ ){
    /*
     $( document).on('widget-updated widget-added', function( e, widget ){

     });
     */

    // widget-34_wpcoupon_sidebar-__i__
    // hide widgets on widget area
    var c = $( 'body.widgets-php #available-widgets #widget-list .widget');
    var l = c.length;
    if ( l > 0 ) {
        for( var i = 1; i <= l; i++ ){
            $( '#widget-'+i+'_wpcoupon_sidebar-__i__').remove();
        }
    }

    // $( document).on('widget-updated widget-added', function( e, widget ){

    $( document).on('widget-added', function( e, widget ){
        try {
            var ui = $( '.st-widget-ui-items', widget);
            var newId = 'widget-ui-added-'+ ( new Date().getTime() );

            ui.removeClass('widget-ui-added').attr( 'id', newId );
            $( '.widget-ui-template', ui ).attr( 'id',  newId+'_template' );
            $( '.st-widget-ui-items', widget ).each( function(){
                new ST_Widgets( $( this).attr( 'id' ) );
            } );
        } catch ( e ) {

        }

    });

} );




