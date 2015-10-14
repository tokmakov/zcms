$(document).ready(function(){

    /*
     * Поиск по каталогу в шапке сайта
     */
    $('#top-search > form > input[name="query"]').attr('autocomplete', 'off').keyup(function () {
        if ($(this).val().length > 1) {
            $('#top-search > div').html('<div class="top-search-loader"></div>');
            $('#top-search > div > div').show();
            $('#top-search > form').ajaxSubmit({
                target: '#top-search > div > div',
                url: '/catalog/ajax',
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

    /*
     * Форма для редактирования личных данных пользователя
     */
    if (!$('#edit-user input[name="change"]').prop('checked')) {
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
