$(document).ready(function() {

    // обработчик события добавления товара в корзину, к сравнению, в избранное
    addBasketHandler();
    // обработчик события удаления товара из сравнения в правой колонке
    removeSideCompareHandler();

    /*
     * Поиск по каталогу в шапке сайта
     */
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function () {
        if ($(this).val().length > 1) {
            $('#top-search > div').html('<div class="top-search-loader"></div>');
            $('#top-search > div > div').show();
            $('#top-search > form').ajaxSubmit({
                target: '#top-search > div > div',
                success: function() {
                    $('#top-search > div > div').removeClass('top-search-loader');
                    if($('#top-search > div > div').is(':empty')) {
                        $('#top-search > div').empty();
                    }
                }
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
     * Переключить представление списка товаров: линнейный или плитка
     */
    $('#switch-line-grid > i.fa-bars').click(function () {
        $(this).addClass('selected');
        $(this).next().removeClass('selected');
        $('.product-list-grid').removeClass('product-list-grid').addClass('product-list-line');
        $.cookie('view', 'line', {expires: 365, path: '/'});
    });
    $('#switch-line-grid > i.fa-th-large').click(function () {
        $(this).addClass('selected');
        $(this).prev().removeClass('selected');
        $('.product-list-line').removeClass('product-list-line').addClass('product-list-grid');
        $.cookie('view', 'grid', {expires: 365, path: '/'});
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
                $.cookie('show_ctg_childs', 1, {expires: 365, path: '/'});
            } else {
                _this.text('показать');
                $.cookie('show_ctg_childs', 0, {expires: 365, path: '/'});
            }
        });
    });

    /*
     * Показать/скрыть фильтры товаров в каталоге
     */
    if ($.cookie('show_ctg_filters') !== undefined && $.cookie('show_ctg_filters') == 0) {
        $('#catalog-filter > div:last-child').hide();
        $('#catalog-filter > div:first-child > span:last-child > span').text('показать');
    }
    $('#catalog-filter > div:first-child > span:last-child > span').click(function() {
        var _this = $(this);
        $('#catalog-filter > div:last-child').slideToggle('normal', function() {
            if (_this.text() == 'показать') {
                _this.text('скрыть');
                $.cookie('show_ctg_filters', 1, {expires: 365, path: '/'});
            } else {
                _this.text('показать');
                $.cookie('show_ctg_filters', 0, {expires: 365, path: '/'});
            }
        });
    });

    /*
     * Свернуть/развернуть блоки в правой колонке
     */
    if ($.cookie('show_side_login') !== undefined && $.cookie('show_side_login') == 0) {
        $('#right > #side-login > .side-content').hide();
    }
    $('#right > #side-login > .side-heading > span').click(function() {
        var content = $(this).parent().next();
        if (content.is(':visible')) {
            $.cookie('show_side_login', 0, {expires: 365, path: '/'});
        } else {
            $.cookie('show_side_login', 1, {expires: 365, path: '/'});
        }
        content.slideToggle();
    });
    if ($.cookie('show_side_basket') !== undefined && $.cookie('show_side_basket') == 0) {
        $('#right > #side-basket > .side-content').hide();
    }
    $('#right > #side-basket > .side-heading > span').click(function() {
        var content = $(this).parent().next();
        if (content.is(':visible')) {
            $.cookie('show_side_basket', 0, {expires: 365, path: '/'});
        } else {
            $.cookie('show_side_basket', 1, {expires: 365, path: '/'});
        }
        content.slideToggle();
    });
    if ($.cookie('show_side_wished') !== undefined && $.cookie('show_side_wished') == 0) {
        $('#right > #side-wished > .side-content').hide();
    }
    $('#right > #side-wished > .side-heading > span').click(function() {
        var content = $(this).parent().next();
        if (content.is(':visible')) {
            $.cookie('show_side_wished', 0, {expires: 365, path: '/'});
        } else {
            $.cookie('show_side_wished', 1, {expires: 365, path: '/'});
        }
        content.slideToggle();
    });
    if ($.cookie('show_side_compare') !== undefined && $.cookie('show_side_compare') == 0) {
        $('#right > #side-compare > .side-content').hide();
    }
    $('#right > #side-compare > .side-heading > span').click(function() {
        var content = $(this).parent().next();
        if (content.is(':visible')) {
            $.cookie('show_side_compare', 0, {expires: 365, path: '/'});
        } else {
            $.cookie('show_side_compare', 1, {expires: 365, path: '/'});
        }
        content.slideToggle();
    });
    if ($.cookie('show_side_viewed') !== undefined && $.cookie('show_side_viewed') == 0) {
        $('#right > #side-viewed > .side-content').hide();
    }
    $('#right > #side-viewed > .side-heading > span').click(function() {
        var content = $(this).parent().next();
        if (content.is(':visible')) {
            $.cookie('show_side_viewed', 0, {expires: 365, path: '/'});
        } else {
            $.cookie('show_side_viewed', 1, {expires: 365, path: '/'});
        }
        content.slideToggle();
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
            jqForm.parent().parent().hide(500, function() {
                // удаляем товар со страницы
                $(this).remove();
                // показываем окно с сообщением
                $('<div>Товар удален из сравнения</div>')
                    .prependTo('body')
                    .hide()
                    .addClass('modal-window')
                    .center()
                    .fadeIn(300, function() {
                        $(this).delay(1000).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#compare-products > .product-list-line > div').length == 0) {
                                $('#compare-products > div:first-child > h2').remove();
                                $('div.product-list-line').html('<p>Нет товаров для сравнения</p>');
                            };
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

    /*
     * Фильтр для товаров выбранной категории или производителя
     */
    $('#catalog-filter form > div:last-child').hide();
    // назначаем обработчик события при выборе функционала, производителя, параметра подбора
    $('#catalog-filter form select option:selected:not(:first-child)').parent().css('border', '1px solid #ff6d00');
    $('#catalog-filter form input[type="checkbox"]:checked').next().css({'color':'#ff6d00', 'border-bottom-color':'#ff6d00'});
    $('#catalog-filter form select, #catalog-filter form input[type="checkbox"]').change(filterSelectHandler);

    $('#catalog-filter form i').click(function() {
        var select = $(this).prev().children();
        select.children('option:selected').prop('selected', false);
        select.change();
    });
});

function addBasketHandler() {

    /*
     * Добавление товара в корзину, ajax
     */
    $('.add-basket-form').ajaxForm({
        target: '#side-basket > .side-content',
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
            var basket;
            if ($('#side-basket > .side-heading').is(':visible')) {
                basket = $('#side-basket > .side-heading > span > i');
            } else {
                basket = $('#top-menu > a:nth-child(1) > i') ;
            }
            var basketTop = basket.offset().top + 11;
            var basketLeft = basket.offset().left + 9;
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
                    'top' : imageTop,
                })
                .delay(200)
                .animate(
                    {left: basketLeft, top: basketTop, width: 0, height: 0, padding: 0},
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
        },
        success: function() {},
        error: function() {
            alert('Ошибка при добавлении товара в корзину');
        }
    });

    /*
     * Добавление товара в избранное, ajax
     */
    $('.add-wished-form').ajaxForm({
        target: '#side-wished > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            // добавляем overlay для правой колонки
            $('<div></div>')
                .prependTo('#side-wished > .side-content')
                .addClass('overlay')
                .height($('#side-wished > .side-content').height()+30/*padding*/)
                .width($('#side-wished > .side-content').width())
                .offset({
                    top : $('#side-wished > .side-content').offset().top,
                    left : $('#side-wished > .side-content').offset().left
                });
            // определаем координаты изображения товара, который добавляется в «Избранное»
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = Math.round(image.offset().top);
            var imageLeft = Math.round(image.offset().left);
            // определаем размеры изображения товара, который добавляется в «Избранное»
            var imageWidth = Math.round(image.width());
            var imageHeight = Math.round(image.height());
            // определяем координаты: либо блока в правой колонке, либо ссылки в шапке сайта
            var wished;
            if ($('#side-wished > .side-heading').is(':visible')) {
                wished = $('#side-wished > .side-heading > span > i');
            } else {
                wished = $('#top-menu > a:nth-child(3) > i') ;
            }
            var wishedTop = wished.offset().top + 11;
            var wishedLeft = wished.offset().left + 9;
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
                    'top' : imageTop,
                })
                .delay(200)
                .animate(
                    {left: wishedLeft, top: wishedTop, width: 0, height: 0, padding: 0},
                    500,
                    function() {
                        // удаляем клона
                        $(this).remove();
                        // показываем окно с сообщением
                        $('<div>Товар добавлен в избранное</div>')
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
            alert('Ошибка при добавлении товара в избранное');
        }
    });

    /*
     * Добавление товара к сравнению, ajax
     */
    $('.add-compare-form').ajaxForm({
        target: '#side-compare > .side-content',
        beforeSubmit: function(formData, jqForm, options) {
            var compare_count = 0;
            var compare_table = $('#side-compare > .side-content > table');
            if (compare_table.length > 0) {
                compare_count = compare_table.data('count');
            }
            if (compare_count > 4) {
                alert('Можно добавить к сравнению только пять товаров');
                return false;
            }
            var compare_group = jqForm.data('group');
            if ($.cookie('compare_group') !== undefined && $.cookie('compare_group') != compare_group) {
                alert('Можно сранивать только товары с одинаковым функционалом');
                return false;
            }
            // добавляем overlay для правой колонки
            $('<div></div>')
                .prependTo('#side-compare > .side-content')
                .addClass('overlay')
                .height($('#side-compare > .side-content').height()+30/*padding*/)
                .width($('#side-compare > .side-content').width())
                .offset({
                    top : $('#side-compare > .side-content').offset().top,
                    left : $('#side-compare > .side-content').offset().left
                });
            // определаем координаты изображения товара, который добавляется к сравнению
            var image = jqForm.parent().prevAll('div:has(img)');
            var imageTop = Math.round(image.offset().top);
            var imageLeft = Math.round(image.offset().left);
            // определаем размеры изображения товара, который добавляется к сравнению
            var imageWidth = Math.round(image.width());
            var imageHeight = Math.round(image.height());
            // определяем координаты: либо блока в правой колонке, либо ссылки в шапке сайта
            var compare;
            if ($('#side-compare > .side-heading').is(':visible')) {
                compare = $('#side-compare > .side-heading > span > i');
            } else {
                compare = $('#top-menu > a:nth-child(4) > i') ;
            }
            var compareTop = compare.offset().top + 11;
            var compareLeft = compare.offset().left + 9;
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
                    'top' : imageTop,
                })
                .delay(200)
                .animate(
                    {left: compareLeft, top: compareTop, width: 0, height: 0, padding: 0},
                    500,
                    function() {
                        // удаляем клона
                        $(this).remove();
                        // показываем окно с сообщением
                        $('<div>Товар добавлен к сравнению</div>')
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
        success: function() {
            // обработчик события удаления товара из сравнения в правой колонке
            removeSideCompareHandler();
        },
        error: function() {
            alert('Ошибка при добавлении товара к сравнению');
        }
    });

}

function filterSelectHandler() {
    if ($(this).attr('name') == 'group') {
        $('#catalog-filter form input[name="change"]').val('1');
    } else {
        $('#catalog-filter form input[name="change"]').val('0');
    }
    $('#catalog-filter form').ajaxSubmit({
        dataType:  'json',
        beforeSubmit: function() {
            /*
             * перед отправкой формы добавляем оверлей для трех блоков
             */
            // первый блок: дочерние категории текущей категории
            var childs = $('#category-childs > div:last-child');
            if (childs.length > 0) {
                $('<div></div>')
                    .prependTo('#category-childs > div:last-child')
                    .addClass('overlay')
                    .height(childs.height())
                    .width(childs.width());
            }
            // второй блок: фильтр по функционалу, производителю и параметрам
            var filtersHeight = $('#catalog-filter > div:last-child').height();
            var filtersWidth = $('#catalog-filter > div:last-child').width();
            $('<div></div>')
                .prependTo('#catalog-filter > div:last-child')
                .addClass('overlay')
                .height(filtersHeight)
                .width(filtersWidth);
            // третий блок: товары выбранной категории
            var productsHeight = $('#catalog-products').height();
            var productsWidth = $('#catalog-products').width();
            $('<div></div>')
                .prependTo('#catalog-products')
                .addClass('products-overlay')
                .height(productsHeight)
                .width(productsWidth);
        },
        success: function(dt) {
            /*
             * получен ответ от сервера, вставляем содержимое трех блоков
             */
            // удаляем три overlay
            $('.overlay, .products-overlay').remove();
            // первый блок: дочерние категории текущей категории
            $('#category-childs > div:last-child').html(dt.childs);
            // второй блок: фильтр по функционалу, производителю и параметрам
            $('#catalog-filter form > div:first-child').html(dt.filters);
            $('#catalog-filter form select option:selected:not(:first-child)').parent().css('border', '1px solid #ff6d00');
            $('#catalog-filter form input[type="checkbox"]:checked').next().css({'color':'#ff6d00', 'border-bottom-color':'#ff6d00'});
            // третий блок: товары выбранной категории
            $('#catalog-products').html(dt.products);

            // обработчики событий для второго блока
            $('#catalog-filter form select, #catalog-filter form input[type="checkbox"]').change(filterSelectHandler);
            $('#catalog-filter form i').click(function() {
                var select = $(this).prev().children();
                select.children('option:selected').prop('selected', false);
                select.change();
            });
            // для третьего блока (товары после фильтрации) назначаем обработчики
            // событий добавления товара в корзину, к сравнению, в избранное
            addBasketHandler();
            // добавляем хэш
            addFilterHash();
        }
    });
}

function addFilterHash() {
    var hash = '';
    var group = $('#catalog-filter form select[name="group"]').val();
    if (group !== '0') {
        hash = '/group/' + group;
    }
    var maker = $('#catalog-filter form select[name="maker"]').val();
    if (maker !== '0' && maker !== undefined) {
        hash = hash + '/maker/' + maker;
    }
    if ($('#catalog-filter form input[name="hit"]').prop('checked')) {
        hash = hash + '/hit/1';
    }
    if ($('#catalog-filter form input[name="new"]').prop('checked')) {
        hash = hash + '/new/1';
    }
    var paramSelect = $('#catalog-filter form select[name^="param"]');
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
    if (hash !== '') { // ссылка для сброса фильтра
        $('#catalog-filter > div:first-child > span:first-child > a').show();
    } else {
        $('#catalog-filter > div:first-child > span:first-child > a').hide();
    }
    var sortInput = $('#catalog-filter form input[name="sort"]');
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
    var id = $(this).data('id');
    var item = $(this).parent().parent().parent();
    if (item.hasClass('opened')) {
        var opened = true;
        item.removeClass('opened');
    } else {
        var opened = false;
        item.removeClass('closed');
    }
    item.addClass('menu-loader');
    /*
     * просто скрываем дочерние элементы
     */
    if (opened) {
        item.children('ul').slideUp('normal', function () {
            item.removeClass('menu-loader').addClass('closed');
            item.find('li.opened').removeClass('opened').addClass('closed').children('ul').hide();
        });
        return;
    }
    /*
     * дочерних элементов еще нет, их надо подгрузить, а потом показать
     */
    if (item.children('ul').length == 0) {
        $.ajax({
            type: 'POST',
            url: '/catalog/menu',
            dataType: 'html',
            data: 'id=' + id,
            success: function(html) {
                $(html).appendTo(item).hide();
                item.find('ul > li.parent > div > span > span').click(menuClickHandler);
                // если есть другие открытые ветки, сначала скрываем их, потом показываем подгруженные элементы
                if (item.siblings('li.opened').length > 0) {
                    // скрываем другие открытые ветки
                    item.siblings('li.opened').removeClass('opened').addClass('menu-loader').children('ul').slideUp('normal', function () {
                        $(this).parent().removeClass('menu-loader').addClass('closed');
                        $(this).find('li.opened').removeClass('opened').addClass('closed').children('ul').hide();
                        // показываем подгруженные элементы
                        item.children('ul').slideDown('normal', function () {
                            item.removeClass('menu-loader').addClass('opened');
                        })
                    });
                } else {
                    // других открытых веток нет, просто показываем подгруженные элементы
                    item.children('ul').slideDown('normal', function () {
                        item.removeClass('menu-loader').addClass('opened');
                    }); 
                }
            }
        });
        return;
    }
    /*
     * дочерние элементы уже есть, их надо показать
     */
    // если есть другие открытые ветки, сначала скрываем их, потом показываем дочерние элементы
    if (item.siblings('li.opened').length > 0) {
        item.siblings('li.opened').removeClass('opened').addClass('menu-loader').children('ul').slideUp('normal', function () {
            $(this).parent().removeClass('menu-loader').addClass('closed');
            $(this).find('li.opened').removeClass('opened').addClass('closed').children('ul').hide();
            item.children('ul').slideDown('normal', function () {
                item.removeClass('menu-loader').addClass('opened');
            })
        });
        return;
    }
    // других открытых веток нет, просто показываем дочерние элементы
    item.children('ul').slideDown('normal', function () {
        item.removeClass('menu-loader').addClass('opened');
    });
}

function removeSideCompareHandler() {
    /*
     * Удаление товара из сравнения в правой колонке
     */
    $('#side-compare form').ajaxForm({
        target: '#side-compare > .side-content',
        url: '/compare/rmvprd',
        beforeSubmit: function() {
            // добавляем overlay
            var sidecompareHeight = $('#side-compare > .side-content').height()+30/*padding*/;
            var sidecompareWidth = $('#side-compare > .side-content').width();
            $('<div></div>')
                .prependTo('#side-compare > .side-content')
                .addClass('overlay')
                .height(sidecompareHeight)
                .width(sidecompareWidth);
        },
        success: function() {
            // обработчик события удаления товара из сравнения в правой колонке
            removeSideCompareHandler();
            // показываем окно с сообщением
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
    /*
     * Удаление всех товаров из сравнения
     */
    $('#side-compare .side-content table tr th a').click(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: '/compare/clear',
            dataType: 'html',
            beforeSend: function() {
                // добавляем overlay
                var sidecompareHeight = $('#side-compare > .side-content').height()+30/*padding*/;
                var sidecompareWidth = $('#side-compare > .side-content').width();
                $('<div></div>')
                    .prependTo('#side-compare > .side-content')
                    .addClass('overlay')
                    .height(sidecompareHeight)
                    .width(sidecompareWidth);
            },
            success: function(html) {
                $('#side-compare .side-content').html(html);
                // показываем окно с сообщением
                $('<div>Товары удалены из сравнения</div>')
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
                alert('Ошибка при удалении товаров из сравнения');
            }
        });
    });
}
