$(document).ready(function() {
    $('#library > div, #library > div:first-of-type > div').hide();
    $('#library > h2 > span, #library > div:first-of-type > h3 > span').click(function() {
        $(this).parent().next().slideToggle();
    });
});