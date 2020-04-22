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