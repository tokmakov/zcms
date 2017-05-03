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

    /*
     * Удаление товара из сравнения, страница сравнения, ajax
     */
    $('.remove-compare-form').ajaxForm({
        target: '#side-compare > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            // добавляем overlay для правой колонки
            $('<div></div>')
                .prependTo('#side-compare > .side-content')
                .addClass('overlay')
                .height($('#side-compare > .side-content').height())
                .width($('#side-compare > .side-content').width())
                .offset({
                    top : $('#side-compare > .side-content').offset().top,
                    left : $('#side-compare > .side-content').offset().left
                });
            // удаляем товар из сравнения
            var index = jqForm.closest('td').index() + 1;
            var column = $('#compare-products table tr td:nth-child(' + index + ')');
            column.hide(500, function() {
                // удаляем колонку таблицы сравнения
                $(this).remove();
                // если эта колонка с товаром была последняя
                if ($('#compare-products table tr td .product-table-item').length === 0) {
                    $('#compare-products > div.table-responsive').hide(500, function() {
                        // удаляем таблицу сравнения
                        $(this).remove();
                        // изменяем цвет иконки в шапке
                        if ($('#top-menu > a:nth-child(4) > i').hasClass('selected')) {
                            $('#top-menu > a:nth-child(4) > i').removeClass('selected');
                        }
                        $('#compare-products > div:first-child > h2').remove();
                        $('#compare-products > a').remove();
                        $('#compare-products').append('<p>Нет товаров для сравнения</p>');
                    });
                };
                // показываем окно с сообщением
                $('<div>Товар удален из сравнения</div>')
                    .prependTo('body')
                    .hide()
                    .addClass('modal-window')
                    .center()
                    .fadeIn(300, function() {
                        $(this).delay(1000).fadeOut(300, function() {
                            $(this).remove();
                        });
                    });
            });
        },
        success: function() {
            // обработчик события удаления товара из сравнения в правой колонке
            removeSideCompareHandler();
        },
        error: function() {
            alert('Ошибка при удалении товара из сравнения');
        }
    });
});
