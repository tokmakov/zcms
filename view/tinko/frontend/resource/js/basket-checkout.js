$(document).ready(function() {

    /*
     * Форма для оформления заказа
     */

    // Для сохранения информации по доставке, полученных из профилей плательщика и получателя.
    // Когда пользователь отмечает/снимает checkbox «Плательщик и получатель различаются» мы
    // должны поменять содержимое полей формы, связанных с доставкой. Установил checkbox —
    // заполняем эти поля данными из профиля получателя. Снял checkbox — заполняем эти поля
    // данными из профиля плательщика. Чтобы не запрашивать каждый раз эти данные с сервера,
    // сохраняем их в этом объекте.
    const checkoutDelivery = {
        'payer' : {
            'shipping' : true,
            'office' : $('#checkout-order select[name="office"]').val(),
            'address' : '',
            'city' : '',
            'index' : ''
        },
        'getter' : {
            'shipping' : true,
            'office' : $('#checkout-order select[name="office"]').val(),
            'address' : '',
            'city' : '',
            'index' : ''
        }
    };

    // всплывающее окно с подсказкой для юридического лица
    $('#checkout-order .company-checkbox-help').click(function() {
        $('<div><p>Отметьте флажок, чтобы оформить заявку на юридическое лицо.</p><p>Укажите название компании, юр.адрес, ИНН, банк, БИК, номер счета.</p></div>')
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

    // всплывающее окно с подсказкой о создании профиля
    $('#checkout-order .make_profile_help').click(function() {
        $('<div><p>Отметьте флажок, чтобы создать профиль на основе введенных данных.</p><p>Вы сможете много раз использовать этот профиль для оформления заявок.</p></div>')
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

    // всплывающее окно с подсказкой о данных последнего заказа
    $('#checkout-order .customer-checkbox-help').click(function() {
        $('<div>Отметьте флажок, чтобы использовать для заполнения формы данные из последней заявки на оборудование.</div>')
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

    // если не отмечен checkbox «Плательщик и получатель различаются»,
    // скрываем часть формы, связанную с получателем
    if ( ! $('#checkout-order input[name="payer_getter_different"]').prop('checked')) {
        $('#checkout-order #getter-order').hide();
    }
    // при изменении состояния checkbox «Плательщик и получатель различаются»,
    // скрываем/показываем часть формы, связанную с получателем
    $('#checkout-order input[name="payer_getter_different"]').change(function() {
        $('#checkout-order #getter-order').slideToggle();
        // поля формы, связанные с доставкой
        if ($(this).prop('checked')) {
            // сохраняем данные по доставке
            checkoutDelivery.payer.shipping = $('#checkout-order input[name="shipping"]').prop('checked');
            checkoutDelivery.payer.office   = $('#checkout-order select[name="office"]').val();
            checkoutDelivery.payer.address  = $('#checkout-order input[name="shipping_address"]').val();
            checkoutDelivery.payer.city     = $('#checkout-order input[name="shipping_city"]').val();
            checkoutDelivery.payer.index    = $('#checkout-order input[name="shipping_index"]').val();
            // возвращаем все поля формы, связанные с доставкой, в исходное состояние
            $('#checkout-order select[name="office"] option:selected').prop('selected', false);
            if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
                $('#checkout-order input[name="shipping"]').prop('checked', true).change();
            }
            $('#checkout-order input[name="shipping_address"]').val('');
            $('#checkout-order input[name="shipping_city"]').val('');
            $('#checkout-order input[name="shipping_index"]').val('');
            // заполняем поля формы, связанные с доставкой, из переменной для их хранения
            if ( ! checkoutDelivery.getter.shipping) {
                $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                $('#checkout-order input[name="shipping_address"]').val(checkoutDelivery.getter.address);
                $('#checkout-order input[name="shipping_city"]').val(checkoutDelivery.getter.city);
                $('#checkout-order input[name="shipping_index"]').val(checkoutDelivery.getter.index);
            } else {
                $('#checkout-order select[name="office"] option').each(function(){
                    if (checkoutDelivery.getter.office == this.value) {
                        this.selected = true;
                    } else {
                        this.selected = false;
                    }
                });
            }
        } else {
            // сохраняем данные по доставке
            checkoutDelivery.getter.shipping = $('#checkout-order input[name="shipping"]').prop('checked');
            checkoutDelivery.getter.office   = $('#checkout-order select[name="office"]').val();
            checkoutDelivery.getter.address  = $('#checkout-order input[name="shipping_address"]').val();
            checkoutDelivery.getter.city     = $('#checkout-order input[name="shipping_city"]').val();
            checkoutDelivery.getter.index    = $('#checkout-order input[name="shipping_index"]').val();
            // возвращаем все поля формы, связанные с доставкой, в исходное состояние
            $('#checkout-order select[name="office"] option:selected').prop('selected', false);
            if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
                $('#checkout-order input[name="shipping"]').prop('checked', true).change();
            }
            $('#checkout-order input[name="shipping_address"]').val('');
            $('#checkout-order input[name="shipping_city"]').val('');
            $('#checkout-order input[name="shipping_index"]').val('');
            // заполняем поля формы, связанные с доставкой, из переменной для их хранения
            if ( ! checkoutDelivery.payer.shipping) {
                $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                $('#checkout-order input[name="shipping_address"]').val(checkoutDelivery.payer.address);
                $('#checkout-order input[name="shipping_city"]').val(checkoutDelivery.payer.city);
                $('#checkout-order input[name="shipping_index"]').val(checkoutDelivery.payer.index);
            } else {
                $('#checkout-order select[name="office"] option').each(function(){
                    if (checkoutDelivery.payer.office == this.value) {
                        this.selected = true;
                    } else {
                        this.selected = false;
                    }
                });
            }
        }
    });
    // если не отмечен checkbox «Юридическое лицо» для плательщика,
    // скрываем часть формы, связанную с юр.лицом плательщика
    if ( ! $('#checkout-order input[name="payer_company"]').prop('checked')) {
        $('#checkout-order #payer-company').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для плательщика,
    // скрываем/показываем часть формы, связанную с юр.лицом плательщика
    $('#checkout-order input[name="payer_company"]').change(function() {
        $('#checkout-order #payer-company').slideToggle();
    });

    // если отмечен checkbox «Самовывоз со склада», скрываем часть формы,
    // связанную с адресом доставки
    if ($('#checkout-order > #delivery input[name="shipping"]').prop('checked')) {
        $('#checkout-order > #delivery > fieldset').hide();
    } else {
        $('#checkout-order > #delivery select[name="office"]').css('display','inline-block').hide(); // css() для MS IE
    }
    // при изменении состояния checkbox «Самовывоз со склада», скрываем/показываем
    // часть формы, связанную с адресом доставки
    $('#checkout-order input[name="shipping"]').change(function() {
        $('#checkout-order > #delivery > fieldset').slideToggle();
        $('#checkout-order > #delivery select[name="office"]').slideToggle();
        /*
        $('#checkout-order input[name="shipping_address"]').val('');
        $('#checkout-order input[name="shipping_city"]').val('');
        $('#checkout-order input[name="shipping_index"]').val('');
        */
    });

    // если не отмечен checkbox «Юридическое лицо» для получателя,
    // скрываем часть формы, связанную с юр.лицом получателя
    if ( ! $('#checkout-order input[name="getter_company"]').prop('checked')) {
        $('#checkout-order #getter-company').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для получателя,
    // скрываем/показываем часть формы, связанную с юр.лицом получателя
    $('#checkout-order input[name="getter_company"]').change(function() {
        $('#checkout-order #getter-company').slideToggle();
    });

    /*
     * При изменении состояния checkbox «Копировать из последнего заказа»
     * получаем с сервера данные о последнем заказе и заполняем поля формы
     */
    $('#checkout-order input[name="customer"]').change(function() {

        /*
         * Возвращаем форму в исходное состояние
         */

        // возвращаем все поля формы, связанные с плательщиком, в исходное состояние
        $('#checkout-order #payer-order input[type="text"]').val('');
        if ($('#checkout-order input[name="payer_company"]').prop('checked')) {
            $('#checkout-order input[name="payer_company"]').prop('checked', false).change();
        }
        // возвращаем все поля формы, связанные с получателем, в исходное состояние
        $('#checkout-order #getter-order input[type="text"]').val('');
        if ($('#checkout-order input[name="getter_company"]').prop('checked')) {
            $('#checkout-order input[name="getter_company"]').prop('checked', false).change();
        }
        // сбрасываем checkbox «Плательщик и получатель различаются», скрываем часть
        // формы, связанную с плательщиком
        if ($('#checkout-order input[name="payer_getter_different"]').prop('checked')) {
            $('#checkout-order input[name="payer_getter_different"]').prop('checked', false).change();
        }
        // возвращаем все поля формы, связанные с доставкой, в исходное состояние
        $('#checkout-order select[name="office"] option:selected').prop('selected', false);
        if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
            $('#checkout-order input[name="shipping"]').prop('checked', true).change();
        }
        $('#checkout-order input[name="shipping_address"]').val('');
        $('#checkout-order input[name="shipping_city"]').val('');
        $('#checkout-order input[name="shipping_index"]').val('');
        // если checkbox был сброшен, больше ничего делать не надо
        if ( ! $(this).prop('checked')) {
            return;
        }

        /*
         * Получаем с сервера данные и заполняем поля формы оформления заказа
         */
        $.get('/user/customer', function(data) {
            // данные плательщика
            if (data.payer_name === undefined) {
                return;
            }
            // фамилия контактного лица плательщика
            $('#checkout-order input[name="payer_surname"]').val(data.payer_surname);
            // имя контактного лица плательщика
            $('#checkout-order input[name="payer_name"]').val(data.payer_name);
            // отчество контактного лица плательщика
            $('#checkout-order input[name="payer_patronymic"]').val(data.payer_patronymic);
            // e-mail контактного лица плательщика
            $('#checkout-order input[name="payer_email"]').val(data.payer_email);
            // телефон контактного лица плательщика
            $('#checkout-order input[name="payer_phone"]').val(data.payer_phone);
            if (data.payer_company == 1) { // плательщик - юридическое лицо?
                $('#checkout-order input[name="payer_company"]').prop('checked', true).change();

                $('#checkout-order input[name="payer_company_name"]').val(data.payer_company_name);
                $('#checkout-order input[name="payer_company_ceo"]').val(data.payer_company_ceo);
                $('#checkout-order input[name="payer_company_address"]').val(data.payer_company_address);
                $('#checkout-order input[name="payer_company_inn"]').val(data.payer_company_inn);
                $('#checkout-order input[name="payer_company_kpp"]').val(data.payer_company_kpp);
                $('#checkout-order input[name="payer_bank_name"]').val(data.payer_bank_name);
                $('#checkout-order input[name="payer_bank_bik"]').val(data.payer_bank_bik);
                $('#checkout-order input[name="payer_settl_acc"]').val(data.payer_settl_acc);
                $('#checkout-order input[name="payer_corr_acc"]').val(data.payer_corr_acc);
            }
            // данные получателя
            if (data.payer_getter_different == 1) { // получатель и плательщик различаются?
                // изменяем состояние checkbox «Плательщик и получатель различаются»
                $('#checkout-order input[name="payer_getter_different"]').prop('checked', true).change();
                // фамилия контактного лица получателя
                $('#checkout-order input[name="getter_surname"]').val(data.getter_surname);
                // имя контактного лица получателя
                $('#checkout-order input[name="getter_name"]').val(data.getter_name);
                // отчество контактного лица получателя
                $('#checkout-order input[name="getter_patronymic"]').val(data.getter_patronymic);
                // e-mail контактного лица получателя
                $('#checkout-order input[name="getter_email"]').val(data.getter_email);
                // телефон контактного лица получателя
                $('#checkout-order input[name="getter_phone"]').val(data.getter_phone);
                if (data.getter_company == 1) { // получатель - юридическое лицо?
                    $('#checkout-order input[name="getter_company"]').prop('checked', true).change();

                    $('#checkout-order input[name="getter_company_name"]').val(data.getter_company_name);
                    $('#checkout-order input[name="getter_company_ceo"]').val(data.getter_company_ceo);
                    $('#checkout-order input[name="getter_company_address"]').val(data.getter_company_address);
                    $('#checkout-order input[name="getter_company_inn"]').val(data.getter_company_inn);
                    $('#checkout-order input[name="getter_company_kpp"]').val(data.getter_company_kpp);
                    $('#checkout-order input[name="getter_bank_name"]').val(data.getter_bank_name);
                    $('#checkout-order input[name="getter_bank_bik"]').val(data.getter_bank_bik);
                    $('#checkout-order input[name="getter_settl_acc"]').val(data.getter_settl_acc);
                    $('#checkout-order input[name="getter_corr_acc"]').val(data.getter_corr_acc);
                }
            }
            // данные по доставке
            if (data.shipping == 0) { // доставка по адресу (не самовывоз)
                if ($('#checkout-order input[name="shipping"]').prop('checked')) {
                    $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                }
                $('#checkout-order input[name="shipping_address"]').val(data.shipping_address);
                $('#checkout-order input[name="shipping_city"]').val(data.shipping_city);
                $('#checkout-order input[name="shipping_index"]').val(data.shipping_index);
            } else {
                // из какого офиса забирать заказ?
                $('#checkout-order select[name="office"] option').each(function(){
                    if (data.shipping == this.value) {
                        this.selected = true;
                    } else {
                        this.selected = false;
                    }
                });
            }
        }, 'json');
    });

    /*
     * Если пользователь авторизован, подгружаем с сервера данные плательщика
     * при выборе профиля плательщика из выпадающего списка
     */
    $('#checkout-order select[name="payer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с плательщиком, в исходное состояние
        $('#checkout-order #payer-order input[type="text"]').val('');
        if ($('#checkout-order input[name="payer_company"]').prop('checked')) {
            $('#checkout-order input[name="payer_company"]').prop('checked', false).change();
        }
        // Возвращаем все поля формы, связанные с доставкой, в исходное состояние только в том
        // случае, если не отмечен checkbox «Плательщик и получатель различаются». Если checkbox
        // отмечен, ничего не делаем, чтобы не удалить данные по доставке, которые получены из
        // профиля получатели или покупатель заполнил эти поля самостоятельно.
        if ( ! $('#checkout-order input[name="payer_getter_different"]').prop('checked')) {
            $('#checkout-order select[name="office"] option:selected').prop('selected', false);
            if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
                $('#checkout-order input[name="shipping"]').prop('checked', true).change();
            }
        }

        var payerProfileId = $(this).val();
        if (payerProfileId === '0') {
            return;
        }
        $.get('/user/profile/' + payerProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            // фамилия контактного лица плательщика
            $('#checkout-order input[name="payer_surname"]').val(data.surname);
            // имя контактного лица плательщика
            $('#checkout-order input[name="payer_name"]').val(data.name);
            // отчество контактного лица плательщика
            $('#checkout-order input[name="payer_patronymic"]').val(data.patronymic);
            // e-mail контактного лица плательщика
            $('#checkout-order input[name="payer_email"]').val(data.email);
            // телефон контактного лица плательщика
            $('#checkout-order input[name="payer_phone"]').val(data.phone);
            if (data.company == 1) { // плательщик - юридическое лицо?
                if ( ! $('#checkout-order input[name="payer_company"]').prop('checked')) {
                    $('#checkout-order input[name="payer_company"]').prop('checked', true).change();
                }
                $('#checkout-order input[name="payer_company_name"]').val(data.company_name);
                $('#checkout-order input[name="payer_company_ceo"]').val(data.company_ceo);
                $('#checkout-order input[name="payer_company_address"]').val(data.company_address);
                $('#checkout-order input[name="payer_company_inn"]').val(data.company_inn);
                $('#checkout-order input[name="payer_company_kpp"]').val(data.company_kpp);
                $('#checkout-order input[name="payer_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="payer_bank_bik"]').val(data.bank_bik);
                $('#checkout-order input[name="payer_settl_acc"]').val(data.settl_acc);
                $('#checkout-order input[name="payer_corr_acc"]').val(data.corr_acc);
            }
            // Поля формы, связанные с доставкой, заполняем только в том случае, если не отмечен
            // checkbox «Плательщик и получатель различаются». Если checkbox отмечен, данные по
            // доставке будут получены из профиля получателя или покупатель введет их сам.
            if ( ! $('#checkout-order input[name="payer_getter_different"]').prop('checked')) {
                if (data.shipping == 0) { // доставка по адресу (не самовывоз)
                    if ($('#checkout-order input[name="shipping"]').prop('checked')) {
                        $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                    }
                    $('#checkout-order input[name="shipping_address"]').val(data.shipping_address);
                    $('#checkout-order input[name="shipping_city"]').val(data.shipping_city);
                    $('#checkout-order input[name="shipping_index"]').val(data.shipping_index);
                } else { // из какого офиса забирать заказ?
                    $('#checkout-order select[name="office"] option').each(function(){
                        if (data.shipping == this.value) {
                            this.selected = true;
                        } else {
                            this.selected = false;
                        }
                    });
                }
            }
        }, 'json');
    });

    /*
     * Если пользователь авторизован, подгружаем с сервера данные получателя
     * при выборе профиля получателя из выпадающего списка
     */
    $('#checkout-order select[name="getter_profile"]').change(function() {
        // возвращаем все поля формы, связанные с получателем, в исходное состояние
        $('#checkout-order #getter-order input[type="text"]').val('');
        if ($('#checkout-order input[name="getter_company"]').prop('checked')) {
            $('#checkout-order input[name="getter_company"]').prop('checked', false).change();
        }
        // возвращаем поля формы, связанные с доставкой, в исходное состояние
        $('#checkout-order select[name="office"] option:selected').prop('selected', false);
        if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
            $('#checkout-order input[name="shipping"]').prop('checked', true).change();
        }

        var getterProfileId = $(this).val();
        if (getterProfileId === '0') {
            return;
        }
        $.get('/user/profile/' + getterProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            // фамилия контактного лица получателя
            $('#checkout-order input[name="getter_surname"]').val(data.surname);
            // имя контактного лица получателя
            $('#checkout-order input[name="getter_name"]').val(data.name);
            // отчество контактного лица получателя
            $('#checkout-order input[name="getter_patronymic"]').val(data.patronymic);
            // e-mail контактного лица получателя
            $('#checkout-order input[name="getter_email"]').val(data.email);
            // телефон контактного лица получателя
            $('#checkout-order input[name="getter_phone"]').val(data.phone);
            if (data.company == 1) { // получатель - юридическое лицо?
                if ( ! $('#checkout-order input[name="getter_company"]').prop('checked')) {
                    $('#checkout-order input[name="getter_company"]').prop('checked', true).change();
                }
                $('#checkout-order input[name="getter_company_name"]').val(data.company_name);
                $('#checkout-order input[name="getter_company_ceo"]').val(data.company_ceo);
                $('#checkout-order input[name="getter_company_address"]').val(data.company_address);
                $('#checkout-order input[name="getter_company_inn"]').val(data.company_inn);
                $('#checkout-order input[name="getter_company_kpp"]').val(data.company_kpp);
                $('#checkout-order input[name="getter_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="getter_bank_bik"]').val(data.bank_bik);
                $('#checkout-order input[name="getter_settl_acc"]').val(data.settl_acc);
                $('#checkout-order input[name="getter_corr_acc"]').val(data.corr_acc);
            }
            // данные по доставке
            if (data.shipping == 0) { // доставка по адресу (не самовывоз)
                if ($('#checkout-order input[name="shipping"]').prop('checked')) {
                    $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                }
                $('#checkout-order input[name="shipping_address"]').val(data.shipping_address);
                $('#checkout-order input[name="shipping_city"]').val(data.shipping_city);
                $('#checkout-order input[name="shipping_index"]').val(data.shipping_index);
            } else { // из какого офиса забирать заказ?
                $('#checkout-order select[name="office"] option').each(function(){
                    if (data.shipping == this.value) {
                        this.selected = true;
                    } else {
                        this.selected = false;
                    }
                });
            }
        }, 'json');
    });

    /*
     * подсказки для юр.лица, банка, адреса доставки с использованием сервиса dadata.ru
     */
    $('#checkout-order input[name="payer_company_name"]').suggestions({ // юр.лицо плательщика, поиск по названию компании
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="payer_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="payer_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="payer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="payer_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="payer_company_kpp"]').val(suggestion.data.kpp);
        }
    });
    $('#checkout-order input[name="payer_company_ceo"]').suggestions({ // юр.лицо, поиск по ФИО ген.директора
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
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниматель
                $(this).val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="payer_company_name"]').val(suggestion.value);
            $('#checkout-order input[name="payer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="payer_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="payer_company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#checkout-order input[name="payer_company_inn"]').suggestions({ // юр.лицо получателя, поиск по ИНН компании
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.inn);
            $('#checkout-order input[name="payer_company_name"]').val(suggestion.value);
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="payer_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниматель
                $('#checkout-order input[name="payer_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="payer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="payer_company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#checkout-order input[name="payer_bank_name"]').suggestions({ // банк получателя, поиск по названию банка
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#checkout-order input[name="payer_bank_bik"]').val(suggestion.data.bic);
            $('#checkout-order input[name="payer_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="payer_bank_bik"]').suggestions({ // банк получателя, поиск по БИК банка
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.bic);
            $('#checkout-order input[name="payer_bank_name"]').val(suggestion.value);
            $('#checkout-order input[name="payer_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="getter_company_name"]').suggestions({ // юр.лицо получателя, поиск по названию компании
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        triggerSelectOnBlur: false,
        mobileWidth: 240,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="getter_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="getter_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="getter_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="getter_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="getter_company_kpp"]').val(suggestion.data.kpp);
        }
    });
    $('#checkout-order input[name="getter_company_ceo"]').suggestions({ // юр.лицо, поиск по ФИО ген.директора
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
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниматель
                $(this).val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="getter_company_name"]').val(suggestion.value);
            $('#checkout-order input[name="getter_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="getter_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="getter_company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#checkout-order input[name="getter_company_inn"]').suggestions({ // юр.лицо получателя, поиск по ИНН компании
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.inn);
            $('#checkout-order input[name="getter_company_name"]').val(suggestion.value);
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="getter_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="getter_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="getter_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="getter_company_kpp"]').val(suggestion.data.kpp);

        }
    });
    $('#checkout-order input[name="getter_bank_name"]').suggestions({ // банк получателя, поиск по названию банка
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#checkout-order input[name="getter_bank_bik"]').val(suggestion.data.bic);
            $('#checkout-order input[name="getter_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="getter_bank_bik"]').suggestions({ // банк получателя, поиск по БИК банка
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.bic);
            $('#checkout-order input[name="getter_bank_name"]').val(suggestion.value);
            $('#checkout-order input[name="getter_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="shipping_address"]').suggestions({ // адрес доставки
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "ADDRESS",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#checkout-order input[name="shipping_city"]').val(suggestion.data.city);
            $('#checkout-order input[name="shipping_index"]').val(suggestion.data.postal_code);
        }
    });
});
