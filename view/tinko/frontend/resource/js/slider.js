$(document).ready(function() {
    $('#banner-slider').bxSlider({
        controls: false,
        startSlide: 0
    });

    $('#new-hit-tabs > div > div:not(:first-child)').hide();
    $('#new-hit-tabs > ul > li:first-child').addClass('current');

    $('#new-hit-tabs > ul > li > a').click(function(e) {
        e.preventDefault();
        if ($(this).parent().hasClass('current')) {
            return;
        }
        $(this).parent().siblings().removeClass('current');
        $(this).parent().addClass('current');
        $(this).parent().parent().next().children().hide();
        var content = $(this).attr('href');
        $(content).show();
    });

    var hitSlider =
    $('#hit-products > ul').bxSlider({
        controls: false,
        slideWidth: 120,
        minSlides: 2,
        maxSlides: 5,
        slideMargin: 5
    });

    var newSlider =
    $('#new-products > ul').bxSlider({
        controls: false,
        slideWidth: 150,
        minSlides: 2,
        maxSlides: 5,
        slideMargin: 5
    });
});
