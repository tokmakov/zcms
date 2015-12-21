$(document).ready(function() {

    /*
     * Добавление товаров ранее сделанного заказа в корзину
     */
    $('#user-orders-list form').ajaxForm({
        target: '#side-basket > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            var sideBasketHeight = $('#side-basket > .side-content').height()+30/*padding*/;
            var sideBasketWidth = $('#side-basket > .side-content').width();
            $('<div></div>')
                .prependTo('#side-basket > .side-content')
                .addClass('overlay')
                .height(sideBasketHeight)
                .width(sideBasketWidth);
                
            // определаем координаты кнопки «Повторить заказ»
            var button = jqForm.children('input[type="submit"]');
            var buttonTop = Math.round(button.offset().top);
            var buttonLeft = Math.round(button.offset().left);
            // определаем размеры заголовка
            var buttonWidth = Math.round(button.width());
            var buttonHeight = Math.round(button.height());
            // определяем координаты корзины: либо в правой колонке, либо в шапке сайта
            var basket;
            if ($('#side-basket > .side-heading').is(':visible')) {
                basket = $('#side-basket > .side-heading > span > i');
            } else {
                basket = $('#top-menu > a:nth-child(1) > i') ;
            }
            var basketTop = basket.offset().top + 11;
            var basketLeft = basket.offset().left + 9;
            button
                .clone()
                .prependTo(jqForm)
                .css({
                    'position' : 'absolute',
                    'width' : buttonWidth,
                    'height' : buttonHeight,
                    'left' : buttonLeft,
                    'top' : buttonTop,
                    'background' : '#e9751f',
                    'z-index' : 5,
                    'color' : '#fff'
                })
                .delay(200)
                .animate(
                    {left: basketLeft, top: basketTop, width: 0, height: 0},
                    500,
                    function() {
                        // удаляем клона
                        $(this).remove();
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