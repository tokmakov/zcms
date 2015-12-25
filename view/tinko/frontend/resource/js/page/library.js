$(document).ready(function() {
    $('#library > div:first-of-type > div').hide();
    $('#library > div:first-of-type > h3 > span').click(function() {
        $(this).parent().next().slideToggle();
    });
    $('#library > div').hide();
    $('#library > h2 > span').click(function() {
        $(this).parent().next().slideToggle();
    });
});