$(document).ready(function() {
    /*
     * Свернуть/развернуть краткое описание товара типового решения
     */
    $('#item-solutions table tr td:nth-child(3) > div').hide();
    $('#item-solutions table tr td:nth-child(3) > span').click(function () {
        $(this).next().slideToggle();
    });

    /*
     * Добавление товаров типового решения в корзину, ajax
     */
    $('#item-solutions form').ajaxForm({
        target: '#side-basket > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            var sideBasketHeight = $('#side-basket > .side-content').height()+30/*padding*/;
            var sideBasketWidth = $('#side-basket > .side-content').width();
            $('<div></div>')
                .prependTo('#side-basket > .side-content')
                .addClass('overlay')
                .height(sideBasketHeight)
                .width(sideBasketWidth);

            // определаем координаты изображения заголовка для списка товаров типового решения
            var heading = jqForm.children('h2');
            var headingTop = Math.round(heading.offset().top);
            var headingLeft = Math.round(heading.offset().left);
            // определаем размеры заголовка
            var headingWidth = Math.round(heading.width());
            var headingHeight = Math.round(heading.height());
            // определяем координаты корзины: либо в правой колонке, либо в шапке сайта
            var basket;
            if ($('#side-basket > .side-heading').is(':visible')) {
                basket = $('#side-basket > .side-heading > span > i');
            } else {
                basket = $('#top-menu > a:nth-child(1) > i') ;
            }
            var basketTop = basket.offset().top + 11;
            var basketLeft = basket.offset().left + 9;
            heading
                .clone()
                .prependTo(jqForm)
                .css({
                    'position' : 'absolute',
                    'width' : headingWidth,
                    'height' : headingHeight,
                    'left' : headingLeft,
                    'top' : headingTop,
                    'background' : '#fff',
                    'z-index' : 5,
                    'color' : '#e9751f',
                    'white-space' : 'nowrap'
                })
                .delay(200)
                .animate(
                    {left: basketLeft, top: basketTop, width: 0},
                    1000,
                    function() {
                        // удаляем клона
                        $(this).remove();
                        // изменяем цвет иконки в шапке
                        if ( ! $('#top-menu > a:nth-child(1) > i').hasClass('selected')) {
                            $('#top-menu > a:nth-child(1) > i').addClass('selected');
                        }
                        // показываем окно с сообщением
                        $('<div>Товары добавлены в корзину</div>')
                            .prependTo('body')
                            .hide()
                            .addClass('modal-window')
                            .center()
                            .fadeIn(300, function() {
                                $(this).delay(1000).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                    }
                );
        },
        success: function() {},
        error: function() {
            alert('Ошибка при добавлении товаров в корзину');
        }
    });
});