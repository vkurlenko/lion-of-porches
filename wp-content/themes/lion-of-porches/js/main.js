// custom scripts

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
});
