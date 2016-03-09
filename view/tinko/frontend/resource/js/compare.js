$(document).ready(function() {
    /*
     * Свернуть/развернуть краткое описание для страницы сравнения
     */
    $('#compare-products table tr td > span:last-child').hide();
    $('#compare-products table tr td > span:first-child').click(function() {
        $(this).next().slideToggle();
        if ($(this).text() == 'показать') {
            $(this).text('скрыть');
        } else {
            $(this).text('показать');
        }
    });
});
