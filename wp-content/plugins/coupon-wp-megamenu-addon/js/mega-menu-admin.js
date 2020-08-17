


//------------------------------------------------------------------------

jQuery( document).ready( function( $ ){

   // console.log( mm_admin_object.taxs_data );
    var xhr;

   var mega = {
        init:  function(){
            this.tax_term_render( );
            this._template( );
            this.addSettings( );
            this.handelEvents( );
        },
       tax_term_render: function(){
           var html = {};
           var tpl_data = {};
           $.each( mm_admin_object.taxs_data, function( key, terms ) {

               html[ key ] = '';
               tpl_data[ key ] = {};
               $.each( terms, function( index, term ) {
                    html[ key ] += '<div class="mm-child-item" data-tax="'+term.taxonomy+'" data-id="'+term.term_id+'">'+term.name+' <span class="dashicons dashicons-plus"></span></div>';
                    tpl_data[ key ][ term.term_id ] = term;
               } );
               mm_admin_object.taxs_data = tpl_data;
               window.mm_taxs_html = html;
           } );

       },

        _template: function(){
            /**
             * Function that loads the Mustache template
             */
            var repeaterTemplate = _.memoize(function () {
                var compiled,
                /*
                 * Underscore's default ERB-style templates are incompatible with PHP
                 * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
                 *
                 * @see trac ticket #22344.
                 */
                    options = {
                        evaluate: /<#([\s\S]+?)#>/g,
                        interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                        escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                        variable: 'data'
                    };

                return function ( data ) {
                    compiled = _.template( jQuery( '#mm-item-settings-tpl').html(), null, options);
                    return compiled( data );
                };
            });

            return this.template = repeaterTemplate();
        },

        addSettings: function(){
            jQuery('li', wpNavMenu.menuList ).each(function () {
                var li = $( this );
                if ( jQuery('.mm-item-settings', li ).length === 0) {

                    li.find('.item-title').append('<span class="mm-item-settings"><span class="">'+mm_admin_object.mm+'</span></span>');
                    //jQuery( this).find( '.menu-item-actions').append( '<input>' );
                    var item_data = mega.getWPMenuData( li );
                    var tpl = mega.template( item_data );
                    $( tpl ).insertAfter( li.find( '.field-description' ) );

                    if ( item_data.mega.enable === 1 ){
                        li.addClass( 'mm-enabled' );
                    } else {
                        li.removeClass( 'mm-enabled' );
                    }
                    console.log( item_data.mega.mega_items );
                    try {
                        if (  typeof item_data.mega.mega_items ){
                            var items;
                            if ( typeof item_data.mega.mega_items == 'string' ) {
                                items = JSON.parse( item_data.mega.mega_items );
                            } else {
                                items = item_data.mega.mega_items;
                            }

                           // var items = JSON.parse( item_data.mega.mega_items );
                            console.log( items );
                            var items_html = '';
                            if ( items.length ) {
                                $.each( items, function( index, term ){
                                    name = term.name;
                                    items_html += '<div class="mm-child-item" data-tax="'+term.taxonomy+'" data-id="'+term.term_id+'">'+term.name+' <span class="dashicons dashicons-no-alt"></span></div>';

                                } );

                            }
                            $( ".mm-child-items", li).append( items_html );
                            mega.updateItemsData( li );
                        }
                    } catch ( e ){

                    }



                    $( ".mm-child-items", li ).sortable({
                        update: function( event, ui ) {
                            mega.updateItemsData( li );
                        }
                    });

                }
            });
        },

        _getMMdata: function( item_id ){

            if ( typeof mm_admin_object.menu_data !== "undefined" ){
                if ( typeof mm_admin_object.menu_data[ item_id ] !== "undefined" ){
                    return  mm_admin_object.menu_data[ item_id ];
                }
            }
            return {};
        },

        getWPMenuData: function( $mi ){
            var menu_data = {};
            menu_data.id            = $mi.find( 'input.menu-item-data-db-id').val() || 0;
            menu_data.object_id     = $mi.find( 'input.menu-item-data-object-id').val() || 0;
            menu_data.parent_id     = $mi.find( 'input.menu-item-data-parent-id').val() || 0;
            menu_data.type          = $mi.find( '.menu-item-data-type').val() || '';
            menu_data.position      = $mi.find( '.menu-item-data-position').val() || 0;
            menu_data.title         = $mi.find( '.edit-menu-item-title').val() || '';
            menu_data.description   = $mi.find( '.edit-menu-item-description').val() || '';
            menu_data.classes       = $mi.find( '.edit-menu-item-classes').val() || '';
            menu_data.attr_title    = $mi.find( '.edit-menu-item-attr-title').val() || '';

            menu_data.mega = mega._getMMdata( menu_data.id  );

            return menu_data;
        },

       updateMMdata: function( item_id, key, value ){
           if ( ! item_id  ){
               return false;
           }
           if ( typeof mm_admin_object.menu_data[ item_id ] === "undefined" ){
               mm_admin_object.menu_data[ item_id ] = {};
           }
           mm_admin_object.menu_data[ item_id ][ key ] = value;
       },

       updateItemsData: function( $item ){
           var data = [];
           $( '.mm-child-items .mm-child-item', $item ).each( function( index ){
                var d = {};
                d.term_id = $( this).attr( 'data-id' ) || 0;
                d.taxonomy = $( this).attr( 'data-tax' ) || '';
                d.name = $( this).text();
                data.push( d );
           } );
           $item.find( '.menu-item-mega-items').val( JSON.stringify( data ) );
       },
        handelEvents: function( $item ){
           // var item_data = mega.getWPMenuData( $item );
            $( 'body' ).on( 'change', '.menu-item .menu-item-enable-mega', function(){
                var m = $( this).closest( '.menu-item' );
                var enable = $( this).is( ':checked' ) ? 1 : 0;
                var  item_data = mega.getWPMenuData( m );
                mega.updateMMdata( item_data.id, 'enable', enable );

                if ( enable === 1 ){
                    m.addClass( 'mm-enabled' );
                } else {
                    m.removeClass( 'mm-enabled' );
                }
            } );

            $( 'body' ).on( 'keyup', '.menu-item input.search-tax-add', function(){
                var m = $( this).closest( '.menu-item' );
                var s =  $( this).val();

                if(xhr && xhr.readyState != 4){
                    xhr.abort();
                }
                $('.search-tax-result').html( '<div class="search-loading">'+mm_admin_object.loading+'</div>' ).show(200);
                if ( s != '' ) {
                    xhr = $.ajax({
                        type: "get",
                        url: mm_admin_object.ajax_url,
                        data: {action: 'mm_search_terms', s: s},
                        success: function (html) {
                            $('.search-tax-result').html(html);
                        }
                    });
                } else {
                    $('.search-tax-result').html('').hide(200);
                }

            } );

            $( 'body' ).on( 'click', '.menu-item .search-tax-result .mm-child-item', function( e ){
                e.preventDefault();
                var m = $( this).closest( '.menu-item' );
                var item = $( this).clone();
                $( this ).hide();
                $( '.dashicons', item).removeClass( 'dashicons-plus').addClass( 'dashicons-no-alt' );
                $( '.mm-child-items').append( item );
                mega.updateItemsData( m );
            } );

            $( 'body' ).on( 'click', '.menu-item .mm-child-items .mm-child-item .dashicons', function( e ){
                e.preventDefault();
                var m = $( this).closest( '.menu-item' );
                $( this).closest( '.mm-child-item' ).remove();
                mega.updateItemsData( m );
            } );


            $( 'body' ).on( 'change keyup', '.field-query-posts input, .field-query-posts select, .field-query-posts textarea', function( e ){
                var m = $( this).closest( '.field-query-posts' );
                var data = {};
                $( 'input, select, textarea', m).each( function(){
                    var input = $( this );
                    var name = input.attr( 'data-name' ) || '';

                    if ( name !== '' ){
                        if ( input.is(':checkbox')  ) {
                            if ( input.is(':checked') ) {
                                data[ name ] = input.val();
                            } else {
                                data[ name ] = ''
                            }
                        } else {
                            data[ name ] = input.val();
                        }
                    }
                } );
                $( '.menu-item-mega-args', m ).val( JSON.stringify( data ) );
            } );


        },
    };

    mega.init( );

    $( 'body' ).on( 'nav-menu-item-added', function( ){
        mega.addSettings();
    } );

} );

//------------------------------------------------------------------------

wpNavMenu.addItemToMenu = function(menuItem, processMethod, callback) {
    var $ = jQuery;
    var menu = $('#menu').val(),
        nonce = $('#menu-settings-column-nonce').val(),
        params;

    processMethod = processMethod || function(){};
    callback = callback || function(){};

    params = {
        'action': 'add-menu-item',
        'menu': menu,
        'menu-settings-column-nonce': nonce,
        'menu-item': menuItem
    };

    $.post( ajaxurl, params, function(menuMarkup) {
        var ins = $('#menu-instructions');

        menuMarkup = $.trim( menuMarkup ); // Trim leading whitespaces
        processMethod(menuMarkup, params);

        // Make it stand out a bit more visually, by adding a fadeIn
        $( 'li.pending' ).hide().fadeIn('slow');
        $( '.drag-instructions' ).show();
        if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length ) {
            ins.addClass( 'menu-instructions-inactive' );
        }

        // Jus add a settings icon to mega menu
       $( 'body').trigger( 'nav-menu-item-added' );

        callback();
    });
};

//------------------------------------------------------------------------

