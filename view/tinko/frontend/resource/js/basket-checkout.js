$(document).ready(function() {

    /*
     * Форма для оформления заказа
     */

    // всплывающее окно с подсказкой для юридического лица
    $('#checkout-order .company-checkbox-help').click(function() {
        $('<div><p>Отметьте флажок, чтобы оформить заказ на юридическое лицо.</p><p>Укажите название компании, юр.адрес, ИНН, банк, БИК, номер счета.</p></div>')
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
        $('<div><p>Отметьте флажок, чтобы создать профиль на основе введенных данных.</p><p>Вы сможете много раз использовать этот профиль для оформления заказов.</p></div>')
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
    // скрываем часть формы, связанную с плательщиком
    if ( ! $('#checkout-order input[name="buyer_payer_different"]').prop('checked')) {
        $('#checkout-order #payer-order').hide();
    }
    // при изменении состояния checkbox «Плательщик и получатель различаются»,
    // скрываем/показываем часть формы, связанную с плательщиком
    $('#checkout-order input[name="buyer_payer_different"]').change(function() {
        $('#checkout-order #payer-order').slideToggle();
    });
    // если не отмечен checkbox «Юридическое лицо» для получателя,
    // скрываем часть формы, связанную с юр.лицом получателя
    if ( ! $('#checkout-order input[name="buyer_company"]').prop('checked')) {
        $('#checkout-order #buyer-company').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для получателя,
    // скрываем/показываем часть формы, связанную с юр.лицом получателя
    $('#checkout-order input[name="buyer_company"]').change(function() {
        $('#checkout-order #buyer-company').slideToggle();
    });

    // если отмечен checkbox «Самовывоз со склада» для получателя, скрываем
    // часть формы, связанную с адресом доставки
    if ($('#checkout-order input[name="shipping"]').prop('checked')) {
        $('#checkout-order #buyer-shipping-details').hide();
    } else {
        $('#checkout-order select[name="office"]').css('display','inline-block').hide(); // css() для MS IE
    }
    // при изменении состояния checkbox «Самовывоз со склада» для получателя,
    // скрываем/показываем часть формы, связанную с адресом доставки получателя
    $('#checkout-order input[name="shipping"]').change(function() {
        $('#checkout-order #buyer-shipping-details').slideToggle('normal', function() {
            $('#checkout-order select[name="office"]').toggle();
        });
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

    // если пользователь авторизован, подгружаем из профиля данные получателя
    // при выборе профиля получателя
    $('#checkout-order select[name="buyer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с получателем, в исходное состояние
        $('#checkout-order #buyer-order input[type="text"]').val('');
        $('#checkout-order select[name="office"] option:selected').prop('selected', false);
        if ($('#checkout-order input[name="buyer_company"]').prop('checked')) {
            $('#checkout-order input[name="buyer_company"]').prop('checked', false).change();
        }
        if ( ! $('#checkout-order input[name="shipping"]').prop('checked')) {
            $('#checkout-order input[name="shipping"]').prop('checked', true).change();
        }

        var buyerProfileId = $(this).val();
        if (buyerProfileId === '0') {
            return;
        }
        $.get('/user/profile/' + buyerProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            // фамилия контактного лица получателя
            $('#checkout-order input[name="buyer_surname"]').val(data.surname);
            // имя контактного лица получателя
            $('#checkout-order input[name="buyer_name"]').val(data.name);
            // отчество контактного лица получателя
            $('#checkout-order input[name="buyer_patronymic"]').val(data.patronymic);
            // e-mail контактного лица получателя
            $('#checkout-order input[name="buyer_email"]').val(data.email);
            // телефон контактного лица получателя
            $('#checkout-order input[name="buyer_phone"]').val(data.phone);
            if (data.company === '1') { // получатель - юридическое лицо?
                if ( ! $('#checkout-order input[name="buyer_company"]').prop('checked')) {
                    $('#checkout-order input[name="buyer_company"]').prop('checked', true).change();
                }
                $('#checkout-order input[name="buyer_company_name"]').val(data.company_name);
                $('#checkout-order input[name="buyer_company_ceo"]').val(data.company_ceo);
                $('#checkout-order input[name="buyer_company_address"]').val(data.company_address);
                $('#checkout-order input[name="buyer_company_inn"]').val(data.company_inn);
                $('#checkout-order input[name="buyer_company_kpp"]').val(data.company_kpp);
                $('#checkout-order input[name="buyer_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="buyer_bank_bik"]').val(data.bank_bik);
                $('#checkout-order input[name="buyer_settl_acc"]').val(data.settl_acc);
                $('#checkout-order input[name="buyer_corr_acc"]').val(data.corr_acc);
            }
            if (data.shipping === '0') { // доставка по адресу (не самовывоз)
                if ($('#checkout-order input[name="shipping"]').prop('checked')) {
                    $('#checkout-order input[name="shipping"]').prop('checked', false).change();
                }
                $('#checkout-order input[name="buyer_shipping_address"]').val(data.shipping_address);
                $('#checkout-order input[name="buyer_shipping_city"]').val(data.shipping_city);
                $('#checkout-order input[name="buyer_shipping_index"]').val(data.shipping_index);
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

    // если пользователь авторизован, подгружаем из профиля данные плательщика
    // при выборе профиля плательщика
    $('#checkout-order select[name="payer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с плательщиком, в исходное состояние
        $('#checkout-order #payer-order input[type="text"]').val('');
        if ($('#checkout-order input[name="payer_company"]').prop('checked')) {
            $('#checkout-order input[name="payer_company"]').prop('checked', false).change();
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
            if (data.company === '1') { // плательщик - юридическое лицо?
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
        }, 'json');
    });
    
    
    /*
     * подсказки для юр.лица, банка, адреса доставки
     */
    $('#checkout-order input[name="buyer_company_name"]').suggestions({ // юр.лицо получателя
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        triggerSelectOnBlur: false,
        mobileWidth: 240,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="buyer_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="buyer_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="buyer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="buyer_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="buyer_company_kpp"]').val(suggestion.data.kpp);
        }
    });
    $('#checkout-order input[name="buyer_company_ceo"]').suggestions({ // юр.лицо
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
            $('#checkout-order input[name="buyer_company_name"]').val(suggestion.value);
            $('#checkout-order input[name="buyer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="buyer_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="buyer_company_kpp"]').val(suggestion.data.kpp);
            
        }
    });
    $('#checkout-order input[name="buyer_company_inn"]').suggestions({ // юр.лицо получателя
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "PARTY",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.inn);
            $('#checkout-order input[name="buyer_company_name"]').val(suggestion.value);
            if (suggestion.data.type === 'LEGAL') { // юр.лицо
                $('#checkout-order input[name="buyer_company_ceo"]').val(suggestion.data.management.name);
            }
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="buyer_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="buyer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="buyer_company_kpp"]').val(suggestion.data.kpp);
            
        }
    });
    $('#checkout-order input[name="buyer_bank_name"]').suggestions({ // банк получателя
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#checkout-order input[name="buyer_bank_bik"]').val(suggestion.data.bic);
            $('#checkout-order input[name="buyer_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="buyer_bank_bik"]').suggestions({ // банк получателя
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "BANK",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.bic);
            $('#checkout-order input[name="buyer_bank_name"]').val(suggestion.value);
            $('#checkout-order input[name="buyer_corr_acc"]').val(suggestion.data.correspondent_account);
        }
    });
    $('#checkout-order input[name="buyer_shipping_address"]').suggestions({ // адрес доставки
        serviceUrl: "https://dadata.ru/api/v2",
        token: "14977cbf05ebd40c763abed4418ace516625be3e",
        type: "ADDRESS",
        count: 5,
        mobileWidth: 240,
        triggerSelectOnBlur: false,
        // вызывается, когда пользователь выбирает одну из подсказок
        onSelect: function(suggestion) {
            $('#checkout-order input[name="buyer_shipping_city"]').val(suggestion.data.city);
            $('#checkout-order input[name="buyer_shipping_index"]').val(suggestion.data.postal_code);
        }
    });
    $('#checkout-order input[name="payer_company_name"]').suggestions({ // юр.лицо плательщика
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
    $('#checkout-order input[name="payer_company_ceo"]').suggestions({ // юр.лицо
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
            $('#checkout-order input[name="payer_company_name"]').val(suggestion.value);
            $('#checkout-order input[name="payer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="payer_company_inn"]').val(suggestion.data.inn);
            $('#checkout-order input[name="payer_company_kpp"]').val(suggestion.data.kpp);
            
        }
    });
    $('#checkout-order input[name="payer_company_inn"]').suggestions({ // юр.лицо получателя
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
            if (suggestion.data.type === 'INDIVIDUAL') { // индивидуальный предриниметель
                $('#checkout-order input[name="payer_company_ceo"]').val(suggestion.data.name.full);
            }
            $('#checkout-order input[name="payer_company_address"]').val(suggestion.data.address.value);
            $('#checkout-order input[name="payer_company_kpp"]').val(suggestion.data.kpp);
            
        }
    });
    $('#checkout-order input[name="payer_bank_name"]').suggestions({ // банк получателя
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
    $('#checkout-order input[name="payer_bank_bik"]').suggestions({ // банк получателя
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
});