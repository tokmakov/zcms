$(document).ready(function() {
    /*
     * Свернуть/развернуть таблицу рейтинга продаж
     */
    $('#rating > div > div').hide();
    $('#rating > div > p > span').click(function () {
        $(this).parent().next().slideToggle();
    });
});
