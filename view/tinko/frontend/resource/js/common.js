$(document).ready(function(){

    addBasketHandler();

    /*
     * Поиск по каталогу в шапке сайта
     */
    /*
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function () {
        if ($(this).val().length > 1) {
            var _this = $(this);
            $('#top-search > div').html('<div class="top-search-loader"></div>');
            $('#top-search > div > div').show();
            $('#top-search > form').ajaxSubmit({
                target: '#top-search > div > div',
                url: '/catalog/ajax-search',
                // временный костыль
                beforeSubmit: function() {
                    var length = _this.val().length;
                    var delay = new Date().getTime() + length;
                    if (length > 2) {
                        while(new Date().getTime() < delay) {}
                    }
                },
                success: function() {
                    $('#top-search > div > div').removeClass('top-search-loader');
                    if($('#top-search > div > div').is(':empty')) {
                        $('#top-search > div').empty();
                    }
                },
            });
        } else {
            $('#top-search > div').empty();
        }
    });
    */
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function () {
        var query = $(this).val();
        if (query.length > 1) {
            $('#top-search > div').html('<div class="top-search-loader"></div>');
            $('#top-search > div > div').show();
            $.ajax({
                type: 'POST',
                url: '/catalog/ajax-search',
                dataType: 'html',
                data: 'query=' + query,
                success: function(html) {
                    $('#top-search > div > div').removeClass('top-search-loader').html(html);
                    if($('#top-search > div > div').is(':empty')) {
                        $('#top-search > div').empty();
                    }
                },
            });
        } else {
            $('#top-search > div').empty();
        }
    });

    $('#top-search > div').on('click', 'div > span', function() {
        $('#top-search > form > input[name="query"]').val('');
        $('#top-search > div').empty();
    });

    /*
     * Свернуть/развернуть список производителей выбранной категории
     */
    $('#category-makers > div:last-child').hide();
    $('#category-makers > div:first-child > span:last-child > span').click(function() {
        $('#category-makers > div:last-child').slideToggle();
        if ($(this).text() == 'показать') {
            $(this).text('скрыть');
        } else {
            $(this).text('показать');
        }
    });

    /*
     * Свернуть/развернуть технические характеристики для страницы сравнения
     */
    $('.products-list-line > div > .product-line-techdata > div:last-child').hide();
    $('.products-list-line > div > .product-line-techdata > div:first-child > span:last-child > span').click(function() {
        $(this).parent().parent().next().slideToggle();
        if ($(this).text() == 'показать') {
            $(this).text('скрыть');
        } else {
            $(this).text('показать');
        }
    });

    /*
     * Фильтр для товаров выбранной категории
     */
    $('#category-filters form > div:last-child').hide();
    // назначаем обработчик события при выборе функционала, производителя, параметра подбора
    $('#category-filters form select').change(filterSelectHandler);

    /*
     * Форма для редактирования личных данных пользователя
     */
    if ( ! $('#edit-user input[name="change"]').prop('checked')) {
        $('#edit-user .password').hide();
    }
    $('#edit-user input[name="change"]').change(function() {
        $('#edit-user .password').slideToggle();
    });

    /*
     * Форма для добавления/редактирования профиля
     */
    // всплывающее окно с подсказкой для юридического лица
    $('#add-edit-profile .legal_person_help').click(function() {
        $('<div><p>Отметьте флажок, чтобы использовать этот профиль для оформления заказов на юридическое лицо.</p><p>Укажите название компании, юридический адрес, ИНН, название банка, номер расчетного счета.</p></div>')
        .prependTo('body')
        .hide()
        .addClass('modal-window')
        .center()
        .fadeIn(500, function() {
            $(this).delay(3000).fadeOut(500, function() {
                $(this).remove();
            });
        });
    });
    // если не отмечен checkbox «Юридическое лицо», скрываем часть формы, связанную с юридическим лицом
    if (!$('#add-edit-profile input[name="legal_person"]').prop('checked')) {
        $('#add-edit-profile > #legal-person').hide();
    }
    $('#add-edit-profile input[name="legal_person"]').change(function() {
        $('#add-edit-profile > #legal-person').slideToggle();
    });
    // если отмечен checkbox «Самовывоз со склада», скрываем часть формы, связанную с адресом доставки
    if ($('#add-edit-profile input[name="own_shipping"]').prop('checked')) {
        $('#add-edit-profile > #physical-address').hide();
    }
    $('#add-edit-profile input[name="own_shipping"]').change(function() {
        $('#add-edit-profile > #physical-address').slideToggle();
    });

});

function addBasketHandler() {

    /*
     * Добавление товара в корзину, ajax
     */
    $('.add-basket-form').ajaxForm({
        target: '#side-basket',
        url: '/basket/ajax/addprd',
        beforeSubmit: function(formData, jqForm, options) {
            var sideBasketHeight = $('#side-basket').height();
            var sideBasketWidth = $('#side-basket').width();
            $('<div></div>').prependTo('#side-basket').addClass('overlay').height(sideBasketHeight).width(sideBasketWidth);
            /*
            var basket = $('#side-basket').is(":visible") ? $('#side-basket') : $('#top-menu > a:first-child') ;
            var img = jqForm.parent().prevAll().find('img:first');
            var imgTop = img.offset().top;
            var imgLeft = img.offset().left;
            var basketTop = basket.offset().top + 30;
            var basketLeft = basket.offset().left + 20;
            img.clone().prependTo('body').css(
                {'position' : 'absolute', 'left' : imgLeft, 'top' : imgTop}
            ).animate(
                {left: basketLeft, top: basketTop, width: '10px', height: '10px'}, 400, function() {$(this).remove();}
            );
            */
        },
        success: function() {
            $('#side-basket > .overlay').remove();
            $('<div>Товар добавлен в корзину</div>')
                .prependTo('body')
                .hide()
                .addClass('modal-window')
                .center()
                .fadeIn(500, function() {
                    $(this).delay(1000).fadeOut(500, function() {
                        $(this).remove();
                    });
                });

        },
        error: function() {
            alert('Ошибка при добавлении товара в корзину');
        }
    });

    /*
     * Добавление товара в список отложенных, ajax
     */
    $('.add-wished-form').ajaxForm({
        target: '#side-wished',
        url: '/wished/ajax/addprd',
        beforeSubmit: function() {
            var sideWishedHeight = $('#side-wished').height();
            var sideWishedWidth = $('#side-wished').width();
            $('<div></div>').prependTo('#side-wished').addClass('overlay').height(sideWishedHeight).width(sideWishedWidth);
        },
        success: function() {
            $('#side-wished > .overlay').remove();
            $('<div>Товар добавлен в список отложенных</div>')
                .prependTo('body')
                .hide()
                .addClass('modal-window')
                .center()
                .fadeIn(500, function() {
                    $(this).delay(1000).fadeOut(500, function() {
                        $(this).remove();
                    });
                });

        },
        error: function() {
            alert('Ошибка при добавлении товара в список отложенных');
        }
    });

    /*
     * Добавление товара в список сравнения, ajax
     */
    $('.add-compared-form').ajaxForm({
        target: '#side-compared',
        url: '/compared/ajax/addprd',
        beforeSubmit: function() {
            var sideComparedHeight = $('#side-compared').height();
            var sideComparedWidth = $('#side-compared').width();
            $('<div></div>').prependTo('#side-compared').addClass('overlay').height(sideComparedHeight).width(sideComparedWidth);
        },
        success: function() {
            $('#side-compared > .overlay').remove();
            $('<div>Товар добавлен в список сравнения</div>')
                .prependTo('body')
                .hide()
                .addClass('modal-window')
                .center()
                .fadeIn(500, function() {
                    $(this).delay(1000).fadeOut(500, function() {
                        $(this).remove();
                    });
                });

        },
        error: function() {
            alert('Ошибка при добавлении товара в список сравнения');
        }
    });

}

function filterSelectHandler() {
    if ($(this).attr('name') == 'group') {
        $('#category-filters form input[name="change"]').val('1');
    } else {
        $('#category-filters form input[name="change"]').val('0');
    }
    var url = $('#category-filters form').attr('action').replace('/catalog/', '/catalog/ajax-filter/');
    $('#category-filters form').ajaxSubmit({
        dataType:  'json',
        url: url,
        beforeSubmit: function() {
            /*
             * перед отправкой формы добавляем оверлей для трех блоков
             */
            // первый блок: дочерние категории текущей категории
            var childsHeight = $('#category-childs > div:last-child').height();
            var childsWidth = $('#category-childs > div:last-child').width();
            $('<div></div>').prependTo('#category-childs > div:last-child').addClass('overlay').height(childsHeight).width(childsWidth);
            // второй блок: фильтр по функционалу, производителю и параметрам
            // $('#category-filters form > div:first-child').empty().addClass('ajax-fields-loader');
            var filtersHeight = $('#category-filters form > div:first-child').height();
            var filtersWidth = $('#category-filters form > div:first-child').width();
            $('<div></div>').prependTo('#category-filters form > div:first-child').addClass('overlay').height(filtersHeight).width(filtersWidth);
            // третий блок: товары выбранной категории
            var productsHeight = $('#category-products').height();
            var productsWidth = $('#category-products').width();
            $('<div></div>').prependTo('#category-products').addClass('products-overlay').height(productsHeight).width(productsWidth);
        },
        success: function(dt) {
            /*
             * получен ответ от сервера, вставляем содержимое трех блоков
             */
            // первый блок: дочерние категории текущей категории
            $('#category-childs > div:last-child').html(dt.childs);
            $('#category-childs > div:last-child > .overlay').remove();
            // второй блок: фильтр по функционалу, производителю и параметрам
            $('#category-filters form > div:first-child').html(dt.filters);
            $('#category-filters form > div:first-child > .overlay').remove();
            // третий блок: товары выбранной категории
            $('#category-products').html(dt.products);
            $('#category-products > .products-overlay').remove();

            $('#category-filters form select').change(filterSelectHandler);
            addBasketHandler();
        }
    });
}
