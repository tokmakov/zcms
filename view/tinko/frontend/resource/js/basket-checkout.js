$(document).ready(function() {

    /*
     * Форма для оформления заказа
     */

    // всплывающее окно с подсказкой для юридического лица
    $('#checkout-order .legal_person_help').click(function() {
        $('<div><p>Отметьте флажок, чтобы оформить заказ на юридическое лицо.</p><p>Укажите название компании, юридический адрес, ИНН, банк, номер счета.</p></div>')
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

    // всплывающее окно с подсказкой о создании профиля
    $('#checkout-order .make_profile_help').click(function() {
        $('<div><p>Отметьте флажок, чтобы создать профиль на основе введенных данных.</p><p>Создав профиль, Вы сможете много раз использовать эти данные для оформления заказов.</p></div>')
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

    // если не отмечен checkbox «Плательщик и получатель различаются»,
    // скрываем часть формы, связанную с плательщиком
    if (!$('#checkout-order input[name="buyer_payer_different"]').prop('checked')) {
        $('#checkout-order #payer-order').hide();
    }
    // при изменении состояния checkbox «Плательщик и получатель различаются»,
    // скрываем/показываем часть формы, связанную с плательщиком
    $('#checkout-order input[name="buyer_payer_different"]').change(function() {
        $('#checkout-order #payer-order').slideToggle();
    });
    // если не отмечен checkbox «Юридическое лицо» для получателя,
    // скрываем часть формы, связанную с юр.лицом получателя
    if (!$('#checkout-order input[name="buyer_legal_person"]').prop('checked')) {
        $('#checkout-order #buyer-legal-person').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для получателя,
    // скрываем/показываем часть формы, связанную с юр.лицом получателя
    $('#checkout-order input[name="buyer_legal_person"]').change(function() {
        $('#checkout-order #buyer-legal-person').slideToggle();
    });

    // если отмечен checkbox «Самовывоз со склада» для получателя, скрываем
    // часть формы, связанную с адресом доставки
    if ($('#checkout-order input[name="shipping"]').prop('checked')) {
        $('#checkout-order #buyer-shipping-details').hide();
    } else {
        $('#checkout-order select[name="office"]').css('display','inline-block').hide(); // css()для MS IE
    }
    // при изменении состояния checkbox «Самовывоз со склада» для получателя,
    // скрываем/показываем часть формы, связанную с адресом доставки получателя
    $('#checkout-order input[name="shipping"]').change(function() {
        $('#checkout-order #buyer-shipping-details').slideToggle();
        $('#checkout-order select[name="office"]').toggle();
    });

    // если не отмечен checkbox «Юридическое лицо» для плательщика,
    // скрываем часть формы, связанную с юр.лицом плательщика
    if (!$('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
        $('#checkout-order #payer-legal-person').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для плательщика,
    // скрываем/показываем часть формы, связанную с юр.лицом плательщика
    $('#checkout-order input[name="payer_legal_person"]').change(function() {
        $('#checkout-order #payer-legal-person').slideToggle();
    });

    // если пользователь авторизован, подгружаем из профиля данные получателя
    $('#checkout-order select[name="buyer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с получателем, в исходное состояние
        $('#checkout-order #buyer-order input[type="text"]').val('');
        $('#checkout-order select[name="office"] option:selected').prop('selected', false);
        if ($('#checkout-order input[name="buyer_legal_person"]').prop('checked')) {
            $('#checkout-order input[name="buyer_legal_person"]').prop('checked', false).change();
        }
        if (!$('#checkout-order input[name="shipping"]').prop('checked')) {
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
            // имя контактного лица получателя
            $('#checkout-order input[name="buyer_name"]').val(data.name);
            // фамилия контактного лица получателя
            $('#checkout-order input[name="buyer_surname"]').val(data.surname);
            // e-mail контактного лица получателя
            $('#checkout-order input[name="buyer_email"]').val(data.email);
            // телефон контактного лица получателя
            $('#checkout-order input[name="buyer_phone"]').val(data.phone);
            if (data.legal_person === '1') { // получатель - юридическое лицо?
                if (!$('#checkout-order input[name="buyer_legal_person"]').prop('checked')) {
                    $('#checkout-order input[name="buyer_legal_person"]').prop('checked', true).change();
                }
                $('#checkout-order input[name="buyer_company"]').val(data.company);
                $('#checkout-order input[name="buyer_ceo_name"]').val(data.ceo_name);
                $('#checkout-order input[name="buyer_legal_address"]').val(data.legal_address);
                $('#checkout-order input[name="buyer_inn"]').val(data.inn);
                $('#checkout-order input[name="buyer_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="buyer_bik"]').val(data.bik);
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
    $('#checkout-order select[name="payer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с плательщиком, в исходное состояние
        $('#checkout-order #payer-order input[type="text"]').val('');
        if ($('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
            $('#checkout-order input[name="payer_legal_person"]').prop('checked', false).change();
        }

        var payerProfileId = $(this).val();
        if (payerProfileId === '0') {
            return;
        }
        $.get('/user/profile/' + payerProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            // имя контактного лица плательщика
            $('#checkout-order input[name="payer_name"]').val(data.name);
            // фамилия контактного лица плательщика
            $('#checkout-order input[name="payer_surname"]').val(data.surname);
            // e-mail контактного лица плательщика
            $('#checkout-order input[name="payer_email"]').val(data.email);
            // телефон контактного лица плательщика
            $('#checkout-order input[name="payer_phone"]').val(data.phone);
            if (data.legal_person === '1') { // плательщик - юридическое лицо?
                if (!$('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
                    $('#checkout-order input[name="payer_legal_person"]').prop('checked', true).change();
                }
                $('#checkout-order input[name="payer_company"]').val(data.company);
                $('#checkout-order input[name="payer_ceo_name"]').val(data.ceo_name);
                $('#checkout-order input[name="payer_legal_address"]').val(data.legal_address);
                $('#checkout-order input[name="payer_inn"]').val(data.inn);
                $('#checkout-order input[name="payer_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="payer_bik"]').val(data.bik);
                $('#checkout-order input[name="payer_settl_acc"]').val(data.settl_acc);
                $('#checkout-order input[name="payer_corr_acc"]').val(data.corr_acc);
            }
        }, 'json');
    });
});