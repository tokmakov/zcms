$(document).ready(function() {
    /*
     * Свернуть/развернуть краткое описание товара распродажи
     */
    $('#sale-products table tr td:nth-child(3) > div').hide();
    $('#sale-products table tr td:nth-child(3) > span').click(function () {
        $(this).next().slideToggle();
    });
});
