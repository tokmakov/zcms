$(document).ready(function(){

    addBasket();

    /*
     * Поиск по каталогу в шапке сайта
     */
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function () {
        if ($(this).val().length > 1) {
            $('#top-search > div').html('<div class="top-search-loader"></div>');
            $('#top-search > div > div').show();
            $('#top-search > form').ajaxSubmit({
                target: '#top-search > div > div',
                url: '/catalog/ajax-search',
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
    })

    /*
     * ФИЛЬТР ДЛЯ ТОВАРОВ ВЫБРАННОЙ КАТЕГОРИИ
     */
    $('#category-filters form > div:last-child').hide();
    /*
     * назначаем обработчик события при выборе функциональной группы
     */
    $('#category-filters form select[name="group"]').change(function() {
        var url = $('#category-filters form').attr('action').replace('/catalog/', '/catalog/ajax-filter/');
        $('#category-filters form').ajaxSubmit({ // отправляем данные формы
            dataType: 'json',
            url: url,
            beforeSubmit: function() {
                /*
                 * перед отправкой формы удаляем содержимое трех блоков
                 */
                var childsHeight = $('#category-childs > div:last-child').height();
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').height(childsHeight).empty().addClass('ajax-childs-loader');
                // второй блок: несколько select для подбора по параметрам
                $('#category-filters form > div:nth-child(3)').empty().addClass('ajax-fields-loader');
                // третий блок: товары выбранной категории
                $('#category-products').empty().addClass('ajax-filter-loader');
            },
            success: function(data) {
                /*
                 * получен ответ от сервера, вставляем содержимое трех блоков
                 */
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').removeClass('ajax-childs-loader').html(data.childs);
                // второй блок: несколько select для подбора по параметрам
                $('#category-filters form > div:nth-child(3)').removeClass('ajax-fields-loader').html(data.fields);
                // для вставленных select назначаем обработчик события
                $('#category-filters form > div:nth-child(3) select').change(function() {
                    var url = $('#category-filters form').attr('action').replace('/catalog/', '/catalog/ajax-filter/');
                    $('#category-filters form > div:last-child > input[name="change"]').val($(this).attr('name').replace(/[^0-9]/g, ''));
                    $('#category-filters form').ajaxSubmit({
                        dataType:  'json',
                        url: url,
                        beforeSubmit: function() {
                            var childsHeight = $('#category-childs > div:last-child').height();
                            $('#category-childs > div:last-child').height(childsHeight).empty().addClass('ajax-childs-loader');
                            $('#category-products').empty().addClass('ajax-filter-loader');
                        },
                        success: function(dt) {
                            $('#category-childs > div:last-child').removeClass('ajax-childs-loader').html(dt.childs);
                            $('#category-products').removeClass('ajax-filter-loader').html(dt.products);
                            /*
                             * выделяем стилями элементы option, выбор которых приводит к пустому результату
                             */
                            // выбор функциональной группы
                            $('#category-filters form select[name="group"] option').slice(1).removeClass('empty-option');
                            dt.options.groups.forEach(function(option) {
                                $('#category-filters form select[name="group"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                                if (option.count == 0) {
                                    $('#category-filters form select[name="group"] option[value=' + option.id + ']').addClass('empty-option');
                                }
                            });
                            // выбор производителя
                            $('#category-filters form select[name="maker"] option').slice(1).removeClass('empty-option');
                            dt.options.makers.forEach(function(option) {
                                $('#category-filters form select[name="maker"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                                if (option.count == 0) {
                                    $('#category-filters form select[name="maker"] option[value=' + option.id + ']').addClass('empty-option');
                                }
                            })
                            // выбор параметра подбора
                            dt.options.params.forEach(function(param) {
                                $('#category-filters form select[name="param[' + param.id + ']"] option').slice(1).removeClass('empty-option');
                                param.values.forEach(function(option) {
                                    $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                                    if (option.count == 0) {
                                        $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').addClass('empty-option');
                                    }
                                });
                            });

                            addBasket();
                        }
                    });
                });
                // третий блок: товары выбранной категории
                $('#category-products').removeClass('ajax-filter-loader').html(data.products);

                /*
                 * выделяем стилями элементы option, выбор которых приводит к пустому результату
                 */
                // выбор функциональной группы
                $('#category-filters form select[name="group"] option').slice(1).removeClass('empty-option');
                data.options.groups.forEach(function(option) {
                    $('#category-filters form select[name="group"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                    if (option.count == 0) {
                        $('#category-filters form select[name="group"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                // выбор производителя
                $('#category-filters form select[name="maker"] option').slice(1).removeClass('empty-option');
                data.options.makers.forEach(function(option) {
                    $('#category-filters form select[name="maker"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                    if (option.count == 0) {
                        $('#category-filters form select[name="maker"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                // выбор параметра подбора
                /*
                data.options.params.forEach(function(param) {
                    $('#category-filters form select[name="param[' + param.id + ']"] option').slice(1).removeClass('empty-option');
                    param.values.forEach(function(option) {
                        $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                        if (option.count == 0) {
                            $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').addClass('empty-option');
                        }
                    });
                });
                */
                addBasket();
            }
        });
    });
    /*
     * назначаем обработчик события при выборе производителя
     */
    $('#category-filters form select[name="maker"]').change(function() {
        var url = $('#category-filters form').attr('action').replace('/catalog/', '/catalog/ajax-filter/');
        $('#category-filters form').ajaxSubmit({ // отправляем данные формы
            dataType:  'json',
            url: url,
            beforeSubmit: function() {
                /*
                 * перед отправкой формы удаляем содержимое двух блоков
                 */
                var childsHeight = $('#category-childs > div:last-child').height();
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').height(childsHeight).empty().addClass('ajax-childs-loader');
                // второй блок: товары выбранной категории
                $('#category-products').empty().addClass('ajax-filter-loader');
            },
            success: function(data) {
                /*
                 * получен ответ от сервера, вставляем содержимое двух блоков
                 */
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').removeClass('ajax-childs-loader').html(data.childs);
                // второй блок: товары выбранной категории
                $('#category-products').removeClass('ajax-filter-loader').html(data.products);

                /*
                 * выделяем стилями элементы option, выбор которых приводит к пустому результату
                 */
                // выбор функциональной группы
                $('#category-filters form select[name="group"] option').slice(1).removeClass('empty-option');
                data.options.groups.forEach(function(option) {
                    $('#category-filters form select[name="group"] option[value=' + option.id + ']').text(option.name + ' : [' + option.count + ']');
                    if (option.count == 0) {
                        $('#category-filters form select[name="group"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                // выбор производителя
                /*
                $('#category-filters form select[name="maker"] option').slice(1).removeClass('empty-option');
                data.options.makers.forEach(function(option) {
                    $('#category-filters form select[name="maker"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                    if (option.count == 0) {
                        $('#category-filters form select[name="maker"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                */
                // выбор параметра подбора
                data.options.params.forEach(function(param) {
                    $('#category-filters form select[name="param[' + param.id + ']"] option').slice(1).removeClass('empty-option');
                    param.values.forEach(function(option) {
                        $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                        if (option.count == 0) {
                            $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').addClass('empty-option');
                        }
                    });
                });

                addBasket();
            }
        });
    });
    /*
     * назначаем обработчик события при выборе параметра подбора
     */
    $('#category-filters form > div:nth-child(3) select').change(function() {
        var url = $('#category-filters form').attr('action').replace('/catalog/', '/catalog/ajax-filter/');
        $('#category-filters form > div:last-child > input[name="change"]').val($(this).attr('name').replace(/[^0-9]/g, ''));
        $('#category-filters form').ajaxSubmit({ // отправляем данные формы
            dataType:  'json',
            url: url,
            beforeSubmit: function() {
                /*
                 * перед отправкой формы удаляем содержимое двух блоков
                 */
                var childsHeight = $('#category-childs > div:last-child').height();
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').height(childsHeight).empty().addClass('ajax-childs-loader');
                // второй блок: товары выбранной категории
                $('#category-products').empty().addClass('ajax-filter-loader');
            },
            success: function(data) {
                /*
                 * получен ответ от сервера, вставляем содержимое двух блоков
                 */
                // первый блок: дочерние категории текущей категории
                $('#category-childs > div:last-child').removeClass('ajax-childs-loader').html(data.childs);
                // второй блок: товары выбранной категории
                $('#category-products').removeClass('ajax-filter-loader').html(data.products);

                /*
                 * выделяем стилями элементы option, выбор которых приводит к пустому результату
                 */
                // выбор функциональной группы
                /*
                $('#category-filters form select[name="group"] option').slice(1).removeClass('empty-option');
                data.options.groups.forEach(function(option) {
                    // $('#category-filters form select[name="group"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                    if (option.count === '0') {
                        $('#category-filters form select[name="group"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                */
                // выбор производителя
                /*
                $('#category-filters form select[name="maker"] option').slice(1).removeClass('empty-option');
                data.options.makers.forEach(function(option) {
                    // $('#category-filters form select[name="maker"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                    if (option.count === '0') {
                        $('#category-filters form select[name="maker"] option[value=' + option.id + ']').addClass('empty-option');
                    }
                });
                */
                // выбор параметра подбора
                data.options.params.forEach(function(param) {
                    $('#category-filters form select[name="param[' + param.id + ']"] option').slice(1).removeClass('empty-option');
                    param.values.forEach(function(option) {
                        $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').text(option.name + ' [' + option.count + ']');
                        if (option.count === '0') {
                            $('#category-filters form select[name="param[' + param.id + ']"] option[value=' + option.id + ']').addClass('empty-option');
                        }
                    });
                });
                addBasket();
            }
        });
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

function addBasket() {

    /*
     * Добавление товара в корзину, ajax
     */
    $('.add-basket-form').ajaxForm({
        target: '#side-basket',
        url: '/basket/ajax/addprd',
        beforeSubmit: function(formData, jqForm, options) {
            var sideBasketHeight = $('#side-basket').height();
            $('#side-basket').css('min-height', sideBasketHeight).empty().addClass('ajax-loader');
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
            $('#side-basket').removeClass('ajax-loader');
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
            $('#side-wished').css('min-height', sideWishedHeight).empty().addClass('ajax-loader');
        },
        success: function() {
            $('#side-wished').removeClass('ajax-loader');
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
            $('#side-compared').css('min-height', sideComparedHeight).empty().addClass('ajax-loader');
        },
        success: function() {
            $('#side-compared').removeClass('ajax-loader');
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
