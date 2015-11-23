if (window.location.hash.substr(0, 2) === '#!' && /^\/catalog\/category\/[0-9]+/.test(window.location.pathname)) {
    var pathname = window.location.pathname.match(/^\/catalog\/category\/[0-9]+/i)[0] + window.location.hash.slice(2);
    window.location.replace(pathname);
}

$(document).ready(function() {

    // обработчик события добавления товара в корзину, к сравнению, в избранное
    addBasketHandler();
    // обработчик события удаления товара из избранного в правой колонке
    removeSideComparedHandler();

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
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function() {
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
     * Свернуть/развернуть список дочерних категорий меню каталога
     */
    $('#catalog-menu li.closed > ul').hide();
    $('#catalog-menu li.parent > div > span > span').click(menuClickHandler);

    /*
     * Свернуть/развернуть список дочерних категорий в каталоге
     */
    if ($.cookie('show_ctg_childs') !== undefined && $.cookie('show_ctg_childs') == 0) {
        $('#category-childs > div:last-child').hide();
        $('#category-childs > div:first-child > span:last-child > span').text('показать');
    }
    $('#category-childs > div:first-child > span:last-child > span').click(function() {
        var _this = $(this);
        $('#category-childs > div:last-child').slideToggle('normal', function() {
            if (_this.text() == 'показать') {
                _this.text('скрыть');
                $.cookie('show_ctg_childs', 1, {expires: 365});
            } else {
                _this.text('показать');
                $.cookie('show_ctg_childs', 0, {expires: 365});
            }
        });

    });

    /*
     * Показать/скрыть фильтры товаров в каталоге
     */
    if ($.cookie('show_ctg_filters') !== undefined && $.cookie('show_ctg_filters') == 0) {
        $('#category-filters > div:last-child').hide();
        $('#category-filters > div:first-child > span:last-child > span').text('показать');
    }
    $('#category-filters > div:first-child > span:last-child > span').click(function() {
        var _this = $(this);
        $('#category-filters > div:last-child').slideToggle('normal', function() {
            if (_this.text() == 'показать') {
                _this.text('скрыть');
                $.cookie('show_ctg_filters', 1, {expires: 365});
            } else {
                _this.text('показать');
                $.cookie('show_ctg_filters', 0, {expires: 365});
            }
        });
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
     * Свернуть/развернуть краткое описание товара типового решения
     */
    $('#item-solutions table tr td:nth-child(3) > div').hide();
    $('#item-solutions table tr td:nth-child(3) > span').click(function () {
        $(this).next().slideToggle();
    });

    /*
     * Добавление товаров типового решения в корзину, ajax
     */
    var solution_id = $('#add-solution-basket').data('id');
    $('#add-solution-basket').ajaxForm({
        target: '#side-basket',
        url: '/solutions/ajax-basket/' + solution_id,
        beforeSubmit: function() {
            var sideBasketHeight = $('#side-basket').height();
            var sideBasketWidth = $('#side-basket').width();
            $('<div></div>').prependTo('#side-basket').addClass('overlay').height(sideBasketHeight).width(sideBasketWidth);
        },
        success: function() {
            $('#side-basket > .overlay').remove();
            $('<div>Товары добавлены в корзину</div>')
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
            alert('Ошибка при добавлении товаров в корзину');
        }
    });

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
    if ( ! $('#add-edit-profile input[name="legal_person"]').prop('checked')) {
        $('#add-edit-profile > #legal-person').hide();
    }
    $('#add-edit-profile input[name="legal_person"]').change(function() {
        $('#add-edit-profile > #legal-person').slideToggle();
    });
    // если отмечен checkbox «Самовывоз со склада», скрываем часть формы, связанную с адресом доставки
    if ($('#add-edit-profile input[name="shipping"]').prop('checked')) {
        $('#add-edit-profile > #shipping-address-city-index').hide();
    } else {
        $('#add-edit-profile select[name="office"]').hide();
    }
    $('#add-edit-profile input[name="shipping"]').change(function() {
        $('#add-edit-profile > #shipping-address-city-index').slideToggle('normal', function() {
            $('#add-edit-profile select[name="office"]').toggle();
        });
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
            // добавляем overlay
            var sideBasketHeight = $('#side-basket').height();
            var sideBasketWidth = $('#side-basket').width();
            $('<div></div>').prependTo('#side-basket').addClass('overlay').height(sideBasketHeight).width(sideBasketWidth);
            // определаем координаты изображения товара, который добавляется в корзину
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = image.offset().top;
            var imageLeft = image.offset().left;
            // определяем координаты корзины: либо в правой колонке, либо в шапке сайта
            var basket = $('#side-basket').is(":visible") ? $('#side-basket') : $('#top-menu > a:first-child > i') ;
            var basketTop = basket.offset().top + 12;
            var basketLeft = basket.offset().left + 6;
            image.clone().find('span').remove().end().prependTo('body').css(
                {
                    'position' : 'absolute',
                    'left' : imageLeft,
                    'top' : imageTop,
                    'background' : '#fff',
                    'width' : '100px',
                    'height' : '100px',
                    'z-index' : 5,
                    'padding' : '9px',
                    'border' : '1px solid #e9751f'
                }
            ).delay(100).animate(
                {left: basketLeft, top: basketTop, width: 0, height: 0, padding: 0},
                700,
                function() {
                    $(this).remove();
                }
            );
        },
        success: function() {
            // удаляем overlay
            $('#side-basket > .overlay').remove();
            // модальное окно с сообщением
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
     * Добавление товара в избранное, ajax
     */
    $('.add-wished-form').ajaxForm({
        url: '/wished/ajax/addprd',
        beforeSubmit: function(formData, jqForm, options) {
            // определаем координаты изображения товара, который добавляется в «Избранное»
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = image.offset().top;
            var imageLeft = image.offset().left;
            // определяем координаты ссылки «Избранное» в шапке сайта
            var wished = $('#top-menu > a:nth-child(3) > i') ;
            var wishedTop = wished.offset().top + 12;
            var wishedLeft = wished.offset().left + 6;
            image.clone().find('span').remove().end().prependTo('body').css(
                {
                    'position' : 'absolute',
                    'left' : imageLeft,
                    'top' : imageTop,
                    'background' : '#fff',
                    'width' : '100px',
                    'height' : '100px',
                    'z-index' : 5,
                    'padding' : '9px',
                    'border' : '1px solid #e9751f'
                }
            ).delay(100).animate(
                {left: wishedLeft, top: wishedTop, width: 0, height: 0, padding: 0},
                700,
                function() {
                    $(this).remove();
                }
            );
        },
        success: function(response) {
            $('<div></div>')
                .html(response)
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
            alert('Ошибка при добавлении товара в избранное');
        }
    });

    /*
     * Добавление товара к сравнению, ajax
     */
    $('.add-compared-form').ajaxForm({
        target: '#side-compared',
        url: '/compared/ajax/addprd',
        beforeSubmit: function(formData, jqForm, options) {
            // добавляем overlay
            var sideComparedHeight = $('#side-compared').height();
            var sideComparedWidth = $('#side-compared').width();
            $('<div></div>').prependTo('#side-compared').addClass('overlay').height(sideComparedHeight).width(sideComparedWidth);
            // определаем координаты изображения товара, который добавляется к сравнению
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = image.offset().top;
            var imageLeft = image.offset().left;
            // определяем координаты: либо блока в правой колонке, либо ссылки в шапке сайта
            var compared = $('#side-compared').is(":visible") ? $('#side-compared') : $('#top-menu > a:nth-child(4) > i') ;
            var comparedTop = compared.offset().top + 12;
            var comparedLeft = compared.offset().left + 6;
            image.clone().find('span').remove().end().prependTo('body').css(
                {
                    'position' : 'absolute',
                    'left' : imageLeft,
                    'top' : imageTop,
                    'background' : '#fff',
                    'width' : '100px',
                    'height' : '100px',
                    'z-index' : 5,
                    'padding' : '9px',
                    'border' : '1px solid #e9751f'
                }
            ).delay(100).animate(
                {left: comparedLeft, top: comparedTop, width: 0, height: 0, padding: 0},
                700,
                function() {
                    $(this).remove();
                }
            );
        },
        success: function() {
            // обработчик события удаления товара из сравнения в правой колонке
            removeSideComparedHandler();
            // удаляем overlay
            $('#side-compared > .overlay').remove();
            // модальное окно с сообщением
            $('<div>Товар добавлен к сравнению</div>')
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
            alert('Ошибка при добавлении товара к сравнению');
        }
    });

    /*
     * Удаление товара из сравнения, ajax
     */
    $('.remove-compared-form').ajaxForm({
        target: '#side-compared',
        url: '/compared/ajax/rmvprd',
        beforeSubmit: function(formData, jqForm, options) {
            // добавляем overlay
            var sideComparedHeight = $('#side-compared').height();
            var sideComparedWidth = $('#side-compared').width();
            $('<div></div>').prependTo('#side-compared').addClass('overlay').height(sideComparedHeight).width(sideComparedWidth);
            // удаляем товар из сравнения
            jqForm.parent().parent().hide(500, function() {
                $(this).remove();
            });
        },
        success: function() {
            // обработчик события удаления товара из сравнения в правой колонке
            removeSideComparedHandler();
            // удаляем overlay
            $('#side-compared > .overlay').remove();
            // модальное окно с сообщением
            $('<div>Товар удален из сравнения</div>')
                .prependTo('body')
                .hide()
                .addClass('modal-window')
                .center()
                .fadeIn(500, function() {
                    $(this).delay(1000).fadeOut('normal', function() {
                        $(this).remove();
                        if ($('div.products-list-line > div').length == 0) {
                            $('div.products-list-line').html('<p>Нет товаров для сравнения</p>');
                        };
                    });
                });

        },
        error: function() {
            alert('Ошибка при добавлении товара к сравнению');
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
            // для третьего блока (товары после фильтрации) назначаем обработчики
            // событий добавления товара в корзину, к сравнению, в избранное
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
    } else {
        history.pushState('', document.title, window.location.pathname);
    }
}

function menuClickHandler(event) {
    event.stopPropagation();
    var item = $(this).parent().parent().parent();
    if (item.hasClass('opened')) {
        var opened = true;
        item.removeClass('opened');
    } else {
        var opened = false;
        item.removeClass('closed');
    }
    item.addClass('menu-loader');
    if (item.children('ul').length == 0) {
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: '/catalog/ajax-menu',
            dataType: 'html',
            data: 'id=' + id,
            success: function(html) {
                item.append(html);
                item.children('ul').hide().slideToggle('normal', function () {
                    item.removeClass('menu-loader');
                    if (opened) {
                        item.addClass('closed');
                    } else {
                        item.addClass('opened');
                    }
                    if (item.hasClass('root')) {
                        item.siblings('li.opened').children('ul').slideToggle('normal', function () {
                            $(this).parent().removeClass('opened').addClass('closed');
                        });
                    }
                });
                item.find('ul > li.parent > div > span > span').click(menuClickHandler);
            },
        });
    } else {
        item.children('ul').slideToggle('normal', function () {
            item.removeClass('menu-loader');
            if (opened) {
                item.addClass('closed');
            } else {
                item.addClass('opened');
            }
            if (item.hasClass('root')) {
                item.siblings('li.opened').children('ul').slideToggle('normal', function () {
                    $(this).parent().removeClass('opened').addClass('closed');
                });
            }
        });
    }
}

function removeSideComparedHandler() {
    /*
     * Удаление товара из сравнения в правой колонке
     */
    $('#side-compared form').ajaxForm({
        target: '#side-compared',
        url: '/compared/ajax/rmvprd',
        beforeSubmit: function() {
            var sideComparedHeight = $('#side-compared').height();
            var sideComparedWidth = $('#side-compared').width();
            $('<div></div>').prependTo('#side-compared').addClass('overlay').height(sideComparedHeight).width(sideComparedWidth);
        },
        success: function() {
            removeSideComparedHandler();
            $('#side-compared > .overlay').remove();
            $('<div>Товар удален из сравнения</div>')
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
            alert('Ошибка при удалении товара из сравнения');
        }
    });
}
