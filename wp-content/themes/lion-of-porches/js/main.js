// custom scripts
$ = jQuery.noConflict();

/* Мобильное меню (гамбургер) */
$(document).on('click', '.hamburger', function() {
    $('.threebar')
        .removeClass('hamburger')
        .addClass('cross');
    toggleMobileMenu();

});


$(document).on('click', '.cross', function() {
    $('.threebar')
        .removeClass('cross')
        .addClass('hamburger');
    toggleMobileMenu();
});

function toggleMobileMenu() {
    $('.main-menu.mobile').toggleClass('hidden');
}
/* /Мобильное меню (гамбургер) */


/* Фиксация главного меню при прокрутке страницы */
$(window).on("scroll", function() {
    if ($(window).scrollTop() > 115) {
        $('header').addClass('fixed');
    }
    else {
        $('header').removeClass('fixed');
    }
});
/* /Фиксация главного меню при прокрутке страницы */


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

$(window).load(function() {
    /*if($('.variations_form .woocommerce-Price-amount').length && $('.variations_form .woocommerce-Price-amount').text() != '') {
        window.setTimeout(function(){
            $('p.price .woocommerce-Price-amount').html($('.variations_form .woocommerce-Price-amount').html());
        }, 0);
    }*/

    /*console.log($('.variations_form .discount-personal .woocommerce-Price-amount'));*/
    //console.log($('.single_variation .woocommerce-variation-price').html());

    if($('.single_variation .woocommerce-variation-price').html() == undefined || $('.single_variation .woocommerce-variation-price').html() == '') {
          $('.summary > .discount-personal').css('display', 'block');
    }

    /*if($('.single_variation .woocommerce-variation-price').html() != undefined && $('.single_variation .woocommerce-variation-price').html() != '') {
        $('.summary > .discount-personal').css('display', 'none');
    }*/

    //$('.variations_form select').trigger('click');

    $('.variations_form select').change(function() {

        //console.log('change');

        if($('.variations_form .single_variation .woocommerce-Price-amount').length && $('.variations_form .single_variation .woocommerce-Price-amount').text() != '') {
            window.setTimeout(function(){

                var price = $('.variations_form .single_variation .woocommerce-Price-amount').html();
                //console.log(price.replace(/(<[^>]+>)|(₽)|,/g,''));

                price = price.replace(/(<[^>]+>)|(₽)|,/g,'') * 1;

                //console.log(price);

                var discount = $('.summary > .discount-personal > .discount-value > span').text();
                discount = discount * 1 / 100;
                //console.log(discount);

                var discount_price = price - price * discount;
                var s = price - discount_price;

                $('.single_variation p.price .woocommerce-Price-amount').html('₽ '+discount_price);
                $('.single_variation p.s .woocommerce-Price-amount').html('₽ '+s);

                $('.woocommerce-variation-price .discount-personal, .woocommerce-variation-price .price').css('display', 'block');
            }, 0);
        }
    });
});
