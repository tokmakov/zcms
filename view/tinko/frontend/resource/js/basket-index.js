$(document).ready(function() {
    upsellHandler();
});

function upsellHandler() {
    $('.upsell-form').ajaxForm({
        url: '/basket/upsell',
        dataType: 'json',
        beforeSubmit: function(formData, jqForm, options) {
            // добавляем overlay для корзины в правой колонке
            $('<div></div>')
                .prependTo('#side-basket > .side-content')
                .addClass('overlay')
                .height($('#side-basket > .side-content').height()+30/*padding*/)
                .width($('#side-basket > .side-content').width())
                .offset({
                    top : $('#side-basket > .side-content').offset().top,
                    left : $('#side-basket > .side-content').offset().left
                });
            // определаем координаты изображения товара, который добавляется в корзину
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = Math.round(image.offset().top);
            var imageLeft = Math.round(image.offset().left);
            // определаем размеры изображения товара, который добавляется в корзину
            var imageWidth = Math.round(image.width());
            var imageHeight = Math.round(image.height());
            // определяем координаты корзины: либо в правой колонке, либо в шапке сайта
            var sideBasket;
            if ($('#side-basket > .side-heading').is(':visible')) {
                sideBasket = $('#side-basket > .side-heading > span > i');
            } else {
                sideBasket = $('#top-menu > a:nth-child(1) > i') ;
            }
            var sideBasketTop = sideBasket.offset().top + 11;
            var sideBasketLeft = sideBasket.offset().left + 9;
            image
                .clone()
                .find('span')
                .remove()
                .end()
                .prependTo('body')
                .addClass('image-clone')
                .css({
                    'width' : imageWidth,
                    'height' : imageHeight,
                    'left' : imageLeft,
                    'top' : imageTop
                })
                .delay(200)
                .animate(
                {left: sideBasketLeft, top: sideBasketTop, width: 0, height: 0, padding: 0},
                500,
                function() {
                    // удаляем клона
                    $(this).remove();
                    // показываем окно с сообщением
                    $('<div>Товар добавлен в корзину</div>')
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
            // добавляем overlay для корзины в центральной колонке
            $('<div></div>')
                .prependTo('#basket')
                .addClass('overlay')
                .height($('#basket').height()+25)
                .width($('#basket').width())
                .offset({
                    top : $('#basket').offset().top-25,
                    left : $('#basket').offset().left
                });
            // добавляем overlay для рекомендованных товаров
            $('<div></div>')
                .prependTo('#upsell')
                .addClass('products-overlay')
                .height($('#upsell').height())
                .width($('#upsell').width())
                .offset({
                    top : $('#upsell').offset().top,
                    left : $('#upsell').offset().left
                });
        },
        success: function(dt) {
            /*
             * получен ответ от сервера, вставляем содержимое  трех блоков:
             * корзины в правой колонке, корзины в центральной колонке и
             * рекомендуемые товары
             */
            // удаляем два overlay
            $('.overlay, products-overlay').remove();
            // первый блок: корзина в правой колонке
            $('#side-basket > .side-content').html(dt.side);
            // второй блок: корзина в центральной колонке
            $('#basket').html(dt.center);
            // третий блок: рекомендованные товары
            $('#upsell').html(dt.upsell);
            // добавляем обработчики для добавления рекомендованных
            // товаров в корзину
            upsellHandler();
        },
        error: function() {
            alert('Ошибка при добавлении товара в корзину');
        }
    });
}