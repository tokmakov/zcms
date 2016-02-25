$(document).ready(function() {
    /*
     * Свернуть/развернуть технические характеристики для страницы сравнения
     */
    $('.product-list-line > div > .product-list-techdata > div:last-child').hide();
    $('.product-list-line > div > .product-list-techdata > div:first-child > span:last-child > span').click(function() {
        $(this).parent().parent().next().slideToggle();
        if ($(this).text() == 'показать') {
            $(this).text('скрыть');
        } else {
            $(this).text('показать');
        }
    });
});
