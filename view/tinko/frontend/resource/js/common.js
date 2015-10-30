if (window.location.hash.substr(0, 2) === '#!' && /^\/catalog\/category\/[0-9]+/.test(window.location.pathname)) {
    var pathname = window.location.pathname.match(/^\/catalog\/category\/[0-9]+/i)[0] + window.location.hash.slice(2);
    window.location.replace(pathname);
}

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
    $('#category-filters form select, #category-filters form input[type="checkbox"]').change(filterSelectHandler);

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
    // всплывающее окно с подсказкой для названия профиля
    $('#add-edit-profile #profile-title-help').click(function() {
        $('<div><p>Введите название профиля, например, «ИП&nbsp;Иванов» или «ООО&nbsp;Восход» или «Доставка на Онежскую улицу».</p></div>')
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
    // всплывающее окно с подсказкой для юридического лица
    $('#add-edit-profile #legal-person-help').click(function() {
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

            $('#category-filters form select, #category-filters form input[type="checkbox"]').change(filterSelectHandler);
            addBasketHandler();
        }
    });
    addFilterHash();
}

function addFilterHash() {
    var hash = '';
    var group = $('#category-filters form select[name="group"]').val();
    if (group !== '0') {
        hash = '/group/' + group;
    }
    var maker = $('#category-filters form select[name="maker"]').val();
    if (maker !== '0') {
        hash = hash + '/maker/' + maker;
    }
    if ($('#category-filters form input[name="hit"]').prop('checked')) {
        hash = hash + '/hit/1';
    }
    if ($('#category-filters form input[name="new"]').prop('checked')) {
        hash = hash + '/new/1';
    }
    var paramSelect = $('#category-filters form select').slice(2);
    var param = [];
    if (paramSelect.length > 0) {
        paramSelect.each(function(index, element){
            if ($(element).val() !== '0') {
                var paramSelectId = $(element).attr('name').replace(/[^0-9]/g, '');
                var paramSelectVal = $(element).val();
                param.push(paramSelectId + '.' + paramSelectVal);
            }
        });
    }
    if (param.length > 0) {
        hash = hash + '/param/' + param.join('-');
    }
    var sortInput = $('#category-filters form input[name="sort"]');
    if (sortInput.length > 0 && sortInput.val() !== '0') {
        hash = hash + '/sort/' + sortInput.val();
    }
    if (hash !== '') {
        window.location.hash = '#!' + hash;
    }
}
