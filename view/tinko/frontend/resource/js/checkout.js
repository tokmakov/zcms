$(document).ready(function() {

    /*
     * Форма для оформления заказа
     */

    // всплывающее окно с подсказкой для юридического лица
    $('#checkout-order .legal_person_help').click(function() {
        $('<div><p>Отметьте флажок, чтобы оформить заказ на юридическое лицо.</p><p>Укажите название компании, юр.адрес, ИНН, банк, номер счета.</p></div>')
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

    // если не отмечен checkbox «Плательщик и получатель различаются», скрываем часть формы, связанную с плательщиком
    if (!$('#checkout-order input[name="recipient_payer_different"]').prop('checked')) {
        $('#checkout-order #payer-order').hide();
    }
    // при изменении состояния checkbox «Плательщик и получатель различаются», скрываем/показываем часть формы, связанную с плательщиком
    $('#checkout-order input[name="recipient_payer_different"]').change(function() {
        $('#checkout-order #payer-order').slideToggle();
    });
    // если не отмечен checkbox «Юридическое лицо» для получателя, скрываем часть формы, связанную с юр.лицом получателя
    if (!$('#checkout-order input[name="recipient_legal_person"]').prop('checked')) {
        $('#checkout-order #recipient-legal-person').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для получателя, скрываем/показываем часть формы, связанную с юр.лицом получателя
    $('#checkout-order input[name="recipient_legal_person"]').change(function() {
        $('#checkout-order #recipient-legal-person').slideToggle();
    });

    // если отмечен checkbox «Самовывоз со склада» для получателя, скрываем часть формы, связанную с адресом доставки
    if ($('#checkout-order input[name="own_shipping"]').prop('checked')) {
        $('#checkout-order #recipient-physical-address').hide();
    }
    // при изменении состояния checkbox «Самовывоз со склада» для получателя, скрываем/показываем часть формы, связанную с адресом доставки получателя
    $('#checkout-order input[name="own_shipping"]').change(function() {
        $('#checkout-order #recipient-physical-address').slideToggle();
    });

    // если не отмечен checkbox «Юридическое лицо» для плательщика, скрываем часть формы, связанную с юр.лицом плательщика
    if (!$('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
        $('#checkout-order #payer-legal-person').hide();
    }
    // при изменении состояния checkbox «Юридическое лицо» для плательщика, скрываем/показываем часть формы, связанную с юр.лицом плательщика
    $('#checkout-order input[name="payer_legal_person"]').change(function() {
        $('#checkout-order #payer-legal-person').slideToggle();
    });

    // если пользователь авторизован, подгружаем из профиля данные получателя
    $('#checkout-order select[name="recipient_profile"]').change(function() {
        // возвращаем все поля формы, связанные с получателем, в исходное состояние
        $('#checkout-order #recipient-order input[type="text"]').val('');
        if ($('#checkout-order input[name="recipient_legal_person"]').prop('checked')) {
            // в Firefox это не работает
            // $('#checkout-order input[name="recipient_legal_person"]').prop('checked', false).change();
            // поэтому так
            $('#checkout-order input[name="recipient_legal_person"]').prop('checked', false);
            $('#checkout-order #recipient-order #recipient-legal-person').slideUp();
        }
        if (!$('#checkout-order input[name="own_shipping"]').prop('checked')) {
            // в Firefox это не работает
            // $('#checkout-order input[name="own_shipping"]').prop('checked', true).change();
            // поэтому так
            $('#checkout-order input[name="own_shipping"]').prop('checked', true);
            $('#checkout-order #recipient-physical-address').slideUp();
        }

        var recipientProfileId = $(this).val();
        if (recipientProfileId === '0') {
            return;
        }
        $.get('/user/ajax/profile/' + recipientProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            $('#checkout-order input[name="recipient_name"]').val(data.name); // имя контактного лица получателя
            $('#checkout-order input[name="recipient_surname"]').val(data.surname); // фамилия контактного лица получателя
            $('#checkout-order input[name="recipient_email"]').val(data.email); // e-mail контактного лица получателя
            $('#checkout-order input[name="recipient_phone"]').val(data.phone); // телефон контактного лица получателя
            if (data.legal_person === '1') { // получатель - юридическое лицо?
                if (!$('#checkout-order input[name="recipient_legal_person"]').prop('checked')) {
                    // в Firefox это не работает
                    // $('#checkout-order input[name="recipient_legal_person"]').prop('checked', true).change();
                    // поэтому так
                    $('#checkout-order input[name="recipient_legal_person"]').prop('checked', true);
                    $('#checkout-order #recipient-legal-person').slideDown();
                }
                $('#checkout-order input[name="recipient_company"]').val(data.company);
                $('#checkout-order input[name="recipient_ceo_name"]').val(data.ceo_name);
                $('#checkout-order input[name="recipient_legal_address"]').val(data.legal_address);
                $('#checkout-order input[name="recipient_inn"]').val(data.inn);
                $('#checkout-order input[name="recipient_bank_name"]').val(data.bank_name);
                $('#checkout-order input[name="recipient_bik"]').val(data.bik);
                $('#checkout-order input[name="recipient_settl_acc"]').val(data.settl_acc);
                $('#checkout-order input[name="recipient_corr_acc"]').val(data.corr_acc);
            }
            if (data.own_shipping === '0') { // доставка по адресу (не самовывоз)
                if ($('#checkout-order input[name="own_shipping"]').prop('checked')) {
                    // в Firefox это не работает
                    // $('#checkout-order input[name="own_shipping"]').prop('checked', false).change();
                    // поэтому так
                    $('#checkout-order input[name="own_shipping"]').prop('checked', false);
                    $('#checkout-order #recipient-physical-address').slideToggle();
                }
                $('#checkout-order input[name="recipient_physical_address"]').val(data.physical_address);
                $('#checkout-order input[name="recipient_city"]').val(data.city);
                $('#checkout-order input[name="recipient_postal_index"]').val(data.postal_index);
            }
        }, 'json');
    });

    // если пользователь авторизован, подгружаем из профиля данные плательщика
    $('#checkout-order select[name="payer_profile"]').change(function() {
        // возвращаем все поля формы, связанные с плательщиком, в исходное состояние
        $('#checkout-order #payer-order input[type="text"]').val('');
        if ($('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
            // в Firefox это не работает
            // $('#checkout-order input[name="payer_legal_person"]').prop('checked', false).change();
            // поэтому так
            $('#checkout-order input[name="payer_legal_person"]').prop('checked', false);
            $('#checkout-order #payer-legal-person').slideUp();
        }

        var payerProfileId = $(this).val();
        if (payerProfileId === '0') {
            return;
        }
        $.get('/user/ajax/profile/' + payerProfileId, function(data) { // получаем профиль с сервера
            if (data.title === undefined) {
                return;
            }
            $('#checkout-order input[name="payer_name"]').val(data.name); // имя контактного лица плательщика
            $('#checkout-order input[name="payer_surname"]').val(data.surname); // фамилия контактного лица плательщика
            $('#checkout-order input[name="payer_email"]').val(data.email); // e-mail контактного лица плательщика
            $('#checkout-order input[name="payer_phone"]').val(data.phone); // телефон контактного лица плательщика
            if (data.legal_person === '1') { // плательщика - юридическое лицо?
                if (!$('#checkout-order input[name="payer_legal_person"]').prop('checked')) {
                    // в Firefox это не работает
                    // $('#checkout-order input[name="payer_legal_person"]').prop('checked', true).change();
                    // поэтому так
                    $('#checkout-order input[name="payer_legal_person"]').prop('checked', true);
                    $('#checkout-order #payer-legal-person').slideDown();
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