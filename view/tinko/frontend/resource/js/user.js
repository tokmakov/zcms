$(document).ready(function() {

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
                $(this).delay(3500).fadeOut(500, function() {
                    $(this).remove();
                });
            });
    });
    // всплывающее окно с подсказкой для юридического лица
    $('#add-edit-profile #company-checkbox-help').click(function() {
        $('<div><p>Отметьте флажок, чтобы использовать этот профиль для оформления заявок на юридическое лицо.</p><p>Укажите название компании, юридический адрес, ИНН, название банка, номер расчетного счета.</p></div>')
            .prependTo('body')
            .hide()
            .addClass('modal-window')
            .center()
            .fadeIn(500, function() {
                $(this).delay(3500).fadeOut(500, function() {
                    $(this).remove();
                });
            });
    });

    // подсказки для юр.лица, банка, адреса доставки
    $('#add-edit-profile input[name="company_name"]').suggestions({ // юр.лицо
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            if (suggestion.data.type === 'LEGAL') { // юридическое лицо
                $('#add-edit-profile input[name="company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#add-edit-profile input[name="company_ceo"]').val(suggestion.data.name.full);
            }
            $('#add-edit-profile input[name="company_address"]').val(suggestion.data.address.value);
            $('#add-edit-profile input[name="company_inn"]').val(suggestion.data.inn);
            $('#add-edit-profile input[name="company_kpp"]').val(suggestion.data.kpp);
        }
    });
    $('#add-edit-profile input[name="company_ceo"]').suggestions({ // юр.лицо
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            if (suggestion.data.type === 'LEGAL') { // юридическое лицо
                $(this).val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $(this).val(suggestion.data.name.full);
            }
            $('#add-edit-profile input[name="company_name"]').val(suggestion.value);
            $('#add-edit-profile input[name="company_address"]').val(suggestion.data.address.value);
            $('#add-edit-profile input[name="company_inn"]').val(suggestion.data.inn);
            $('#add-edit-profile input[name="company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#add-edit-profile input[name="company_inn"]').suggestions({ // юр.лицо
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.inn);
            $('#add-edit-profile input[name="company_name"]').val(suggestion.value);
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#add-edit-profile input[name="company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#add-edit-profile input[name="company_ceo"]').val(suggestion.data.name.full);
            }
            $('#add-edit-profile input[name="company_address"]').val(suggestion.data.address.value);
            $('#add-edit-profile input[name="company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#add-edit-profile input[name="bank_name"]').suggestions({ // банк
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#add-edit-profile input[name="bank_bik"]').val(suggestion.data.bic);
            $('#add-edit-profile input[name="corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#add-edit-profile input[name="bank_bik"]').suggestions({ // банк
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.bic);
            $('#add-edit-profile input[name="bank_name"]').val(suggestion.value);
            $('#add-edit-profile input[name="corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#add-edit-profile input[name="shipping_address"]').suggestions({ // адрес доставки
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "ADDRESS",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#add-edit-profile input[name="shipping_city"]').val(suggestion.data.city);
            $('#add-edit-profile input[name="shipping_index"]').val(suggestion.data.postal_code);
        }
    });

    // если не отмечен checkbox «Юридическое лицо», скрываем часть формы, связанную с юридическим лицом
    if ( ! $('#add-edit-profile input[name="company"]').prop('checked')) {
        $('#add-edit-profile > #company').hide();
    }
    // при изменении checkbox «Юридическое лицо», скрываем/показываем часть формы, связанную с юридическим лицом
    $('#add-edit-profile input[name="company"]').change(function() {
        $('#add-edit-profile > #company').slideToggle();
    });
    // если отмечен checkbox «Самовывоз со склада», скрываем часть формы, связанную с адресом доставки
    if ($('#add-edit-profile input[name="shipping"]').prop('checked')) {
        $('#add-edit-profile > #shipping-address').hide();
    } else {
        $('#add-edit-profile select[name="office"]').css('display','inline-block').hide(); // css()для MS IE
    }
    // при изменении checkbox «Самовывоз со склада», скрываем/показываем часть формы, связанную с адресом доставки
    $('#add-edit-profile input[name="shipping"]').change(function() {
        $('#add-edit-profile > #shipping-address').slideToggle('normal', function() {
            $('#add-edit-profile select[name="office"]').toggle();
        });
    });

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
                        // изменяем цвет иконки в шапке
                        if ( ! $('#top-menu > a:nth-child(1) > i').hasClass('selected')) {
                            $('#top-menu > a:nth-child(1) > i').addClass('selected');
                        }
                    }
                );
        },
        success: function() {},
        error: function() {
            alert('Ошибка при добавлении товаров в корзину');
        }
    });
});