

jQuery(document).ready(function() {
    jQuery(".st-slider").each(  function( i ) {
        //return false ;
        var s = jQuery( this );
        if ( s.hasClass('slider-added' ) ) {
           return false ;
        }

        s.addClass( 'slider-added' );
        var settings = s.attr('data-settings');

        var settings = s.attr('data-settings');
        try {
            settings = JSON.parse( settings );
        }catch (e) {

        }

        var _default =  {
            navigation : true, // Show next and prev buttons
            navigationText : false,
            //pagination : true,
            //paginationNumbers: true,
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem: true,
            autoPlay: true,
            stopOnHover: true,
            lazyLoad : true,
            // "singleItem:true" is a shortcut for:
            // items : 1,
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false,
            afterInit: function(){
               // s.closest( '.widget').addClass( 's-added').fadeIn(500);
                s.closest( '.widget').fadeIn(500);
            }
        };

        // Merge object2 into object1
        jQuery.extend( _default , settings );

        //console.log( _default );
        s.owlCarousel( _default );

    } ) ;

});