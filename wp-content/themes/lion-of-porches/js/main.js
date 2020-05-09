// custom scripts
$ = jQuery.noConflict();

$(document).on('click', '.hamburger', function() {
    $('.threebar')
        .removeClass('hamburger')
        .addClass('cross');
    toggleMobileMenu();

});

// Arrow to Hamburger
$(document).on('click', '.cross', function() {
    $('.threebar')
        .removeClass('cross')
        .addClass('hamburger');
    toggleMobileMenu();
});

function toggleMobileMenu() {
    $('.main-menu.mobile').toggleClass('hidden');
}

$(document).ready(function() {
    /** слайдер Магазины */
    $('.slider1').slick({
        infinite: true,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        adaptiveHeight: true,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    /*$('.woocommerce-product-gallery ol').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        vertical: true,
        arrows: true,
    })*/
});

$( 'body' ).on( 'change', '.qty', function() { // поле с количеством имеет класс .qty
    $( '[name="update_cart"]' ).trigger( 'click' );
} );

$('li a.btn-alt').on('mouseover', function() {

    $('.sub-menu > div').addClass('hidden');
    $('li a.btn-alt').removeClass('active');
    /*$('.sub-menu > div').animate({
        opacity: 0,
        visibility: 'hidden'
    });*/

    var cls = $(this).data('subcategory');
    $('.'+cls).removeClass('hidden');
    $(this).addClass('active');
   /* console.log('.'+cls+' a::after');*/
    $('.'+cls+' a::after').show();

   /* $('.'+cls).animate({
        opacity: 1,
        visibility: 'visible'
    });;*/

    /*if($(this).data('subcategory') === 'womenswear') {
        //alert($(this).data('subcategory'));
        $('.for-women').removeClass('hidden');
    }*/
})

$('.sub-menu').on('mouseleave', function() {
    /*console.log('out');*/
    $('.sub-menu > div').addClass('hidden');
    $('li a.btn-alt').removeClass('active');

})
