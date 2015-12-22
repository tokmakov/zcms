$(document).ready(function() {
    $('.tabs > div > div:not(:first-child)').hide();
    $('.tabs > ul > li:first-child').addClass('current');

    $('.tabs > ul > li > a').click(function(e) {
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
});
