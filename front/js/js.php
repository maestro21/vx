$( document ).ready(function() {

    var tmwidth = $('.topmenu').width();

    function resizeMenu() {
        if(tmwidth < ($('.menu').width() - $('.logo').width() - $('.langs').width())) {
            $('.hamburger').hide();
            $('.mainmenu').removeClass('responsive');
            $('.mainmenu').removeClass('open');
            $('.header .wrap').removeClass('open');
        } else {
            $('.hamburger').show();
            $('.mainmenu').addClass('responsive');
        }
    }

    $( window ).resize(function() {
        resizeMenu();
    });
    resizeMenu();

    $('.hamburger').click(function() {
        $('.mainmenu').toggleClass('open');
        $('.header .wrap').toggleClass('open');
    });

});