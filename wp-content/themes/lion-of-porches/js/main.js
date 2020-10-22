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

/* Дерево категорий в моб. меню */
$(document).on('click', '.mobile > ul > li > a', function () {
    var _this = $(this);
    var category = $(_this).data('category');

    $('.mobile-submenu div').each(function () {
        if($(this).data('category') === category) {
            $('div.'+category).fadeToggle(500);
        } else {
            if($(this).css('display') === 'block') {
                $(this).css('display', 'none');
            }
        }
    });

    return false;
});

$(document).on('click', '.mobile-submenu > div > ul > li > a', function () {

    if($(this).next('.children').css('display') === 'block') {
        $(this).next('.children').fadeToggle(500);

        return false;
    } else {
        $('.children').each(function() {
            if($(this).css('display') === 'block') {
                $(this).css('display', 'none');
            }
        });

        if ($(this).next('.children').length) {
            $(this).next('.children').fadeToggle(500);

            return false;
        }
    }
});

$(document).on('click', '.big-banner-text .list-inline > li > a', function () {
    if (isMobile()) {
        var category = $(this).data('category');
        $('.mobile-submenu .container').hide();

        $('.threebar').fadeToggle(500);
        $('.main-menu.mobile').addClass('show');
        $('.mobile-submenu .'+category).show();

        return false;
    }
});

$(document).on('click', '.close-menu', function () {
    $('.main-menu.mobile').removeClass('show');
    $('.threebar').fadeToggle(500);
    $('.mobile-submenu .container').hide();
});

function isMobile() {
    if ($(document).width() <= 768 ) {
        return true;
    }

    return false;
}
/* /Дерево категорий в моб. меню */

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

    /* прокрутка до активной картинки в карточке товара сразу после загрузки страницы */
    window.setTimeout(function() {
        var active_slide = $('.flex-active-slide').data('thumb');

        $('.flex-control-thumbs li').each(function(index, element) {

            var img = $(this).find('img');

            if($(img).attr('src') == active_slide) {
                $(img).addClass('flex-active');
            }
        })

        activeThumb();
        setPersonalWindow();

    }, 1000);
    /* /прокрутка до активной картинки в карточке товара сразу после загрузки страницы */
});



$('.button-variable-wrapper[data-attribute_name="attribute_pa_color"] .button-variable-item').on('click', function() {
    $('.flex-control-thumbs li img').removeClass('flex-active');
    activeThumb();
});

$('.button-variable-wrapper .button-variable-item').on('click', function() {
    setPersonalWindow();
});

/* подстановка пересчета цен в персональном блоке  */
function setPersonalWindow() {
    window.setTimeout(function(){

        var personal_discount = $('.personal-data').data('personal-discount');
        var personal_price = $('.personal-data').data('personal-price');
        var personal_economy = $('.personal-data').data('personal-economy');

        $('.data-personal-discount').text(personal_discount);
        $('.data-personal-price .val').text(personal_price);
        $('.data-personal-economy .val').text(personal_economy);

    }, 500);
}
/* /подстановка пересчета цен в персональном блоке  */

/* прокрутка до активной картинки в карточке товара */
function activeThumb() {

    //$('.flex-control-thumbs li img').removeClass('flex-active');

    var h = $('.flex-control-thumbs li').height() + 10;

    var active = 0;

    window.setTimeout(function(){

        $('.flex-control-thumbs li').each(function(index, element){
            if($(this).find('img').hasClass('flex-active')) {
                active = index;
                //console.log(index)
            }
        })

        $('.flex-control-thumbs').animate({
            scrollTop: active * h
        }, 500);

    }, 500);
}
/* /прокрутка до активной картинки в карточке товара */

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

/* подстановка номера заказ в форму возврата */
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
        return null;
    }
    else{
        return decodeURI(results[1]) || 0;
    }
}

if($('#order-number')) {
    $('#order-number').val($.urlParam('order-number'));
}
/* /подстановка номера заказ в форму возврата */




$(window).load(function() {

    /*if($('.single_variation .woocommerce-variation-price').html() == undefined || $('.single_variation .woocommerce-variation-price').html() == '') {
          $('.summary > .discount-personal').css('display', 'block');
    }

    $('.variations_form select').change(function() {


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
    });*/
});
