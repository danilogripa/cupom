

jQuery(document).ready(function() {
    jQuery(".st-carousel").each(  function( i ) {
        //return false ;
        var s = jQuery( this );
        if ( s.hasClass('slider-added' ) ) {
           return false ;
        }

        s.addClass( 'slider-added' );
        var settings = s.attr('data-settings');
        try {
            settings = JSON.parse( settings );
        }catch (e) {

        }

        var args =  {
            navigation : settings.navigation, // Show next and prev buttons
            navigationText : settings.navigationText,
            //pagination : true,
            //paginationNumbers: true,
            slideSpeed : settings.slideSpeed,
            paginationSpeed : settings.paginationSpeed,
            rewindNav: settings.rewindNav,
            //singleItem: false,
            autoPlay: settings.autoPlay,
            stopOnHover: settings.stopOnHover,
            // "singleItem:true" is a shortcut for:
            items : settings.items,

            itemsDesktop : [1199, settings.items ],
            itemsDesktopSmall : [979, settings.items ],
            itemsTablet : [768, settings.itemsDesktopSmall ],
            itemsTabletSmall : false,
            itemsMobile : [479, settings.itemsMobile ],

            lazyLoad : settings.lazyLoad,
            singleItem: settings.singleItem,
            afterInit: function(){
                setTimeout(  function(){
                    s.closest( '.widget').fadeIn(500);
                }, 100 );
            }
        };

        s.owlCarousel( args );

    } ) ;

});