$(document).ready(function() {
    $('#tabs > div > div:not(:first-child)').hide();
    $('#tabs > ul > li:first-child').addClass('current');

    $('#tabs > ul > li > a').click(function(e) {
        e.preventDefault();
        $('#tabs > ul > li').removeClass('current');
        $(this).parent().addClass('current');
        $('#tabs > div > div').hide();
        var content = $(this).attr('href');
        $(content).show();
    });
});
