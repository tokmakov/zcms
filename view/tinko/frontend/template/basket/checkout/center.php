<?php
/**
 * Форма для оформления заказа, общедоступная часть сайта,
 * файл view/example/frontend/template/basket/checkout/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $profiles - массив профилей зарегистрированного и авторизованного пользователя
 * $offices - список офисов для самовывоза товара со склада
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены
 * ошибки, мы должны снова предъявить форму, заполненную уже введенными данными и
 * вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 *
 * $success - признак успешного размещения заказа (если $success=true,то будут доступны
 * только две переменные: $success и $breadcrumbs, потому как остальные просто не нужны)
 *
 * $offices = Array (
 *   [1] => Центральный офис
 *   [2] => Офис продаж «Сокол»
 *   [3] => Офис продаж «Мещанский»
 *   [4] => Офис продаж «Нагорный»
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/basket/checkout/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Оформление заказа</h1>

<?php if ($success): ?>
    <p>Ваш заказ успешно создан, наш менеджер свяжется с Вами в ближайшем будущем.</p>
    <?php return; ?>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $recipient_name             = ''; // имя контактного лица получателя
    $recipient_surname          = ''; // фамилия контактного лица получателя
    $recipient_email            = ''; // e-mail контактного лица получателя
    $recipient_phone            = ''; // телефон контактного лица получателя

    $own_shipping               = 1;  // самовывоз?
    $recipient_physical_address = ''; // адрес доставки получателя
    $recipient_city             = ''; // город
    $recipient_postal_index     = ''; // почтовый индекс

    $recipient_legal_person     = 0;  // получатель - юридическое лицо?
    $recipient_company          = ''; // название компании получателя
    $recipient_ceo_name         = ''; // генеральный директор компании получателя
    $recipient_legal_address    = ''; // юридический адрес компании получателя
    $recipient_bank_name        = ''; // название банка компании получателя
    $recipient_inn              = ''; // ИНН компании получателя
    $recipient_bik              = ''; // БИК компании получателя
    $recipient_settl_acc        = ''; // расчетный счет компании получателя
    $recipient_corr_acc         = ''; // корреспондентский счет компании получателя

    $recipient_payer_different  = 0;  // плательщик и получатель различаются?

    $payer_name                 = ''; // имя контактного лица плательщика
    $payer_surname              = ''; // фамилия контактного лица плательщика
    $payer_email                = ''; // e-mail контактного лица плательщика
    $payer_phone                = ''; // телефон контактного лица плательщика

    $payer_legal_person         = 0;  // плательщик - юридическое лицо?
    $payer_company              = ''; // название компании плательщика
    $payer_ceo_name             = ''; // генеральный директор компании плательщика
    $payer_legal_address        = ''; // юридический адрес компании плательщика
    $payer_bank_name            = ''; // название банка компании плательщика
    $payer_inn                  = ''; // ИНН компании плательщика
    $payer_bik                  = ''; // БИК компании плательщика
    $payer_settl_acc            = ''; // расчетный счет компании плательщика
    $payer_corr_acc             = ''; // корреспондентский счет компании плательщика

    $comment                    = ''; // комментарий к заказу

    if (isset($savedFormData)) {
        $recipient_name             = htmlspecialchars($savedFormData['recipient_name']);
        $recipient_surname          = htmlspecialchars($savedFormData['recipient_surname']);
        $recipient_email            = htmlspecialchars($savedFormData['recipient_email']);
        $recipient_phone            = htmlspecialchars($savedFormData['recipient_phone']);

        $own_shipping               = $savedFormData['own_shipping'];
        $recipient_physical_address = htmlspecialchars($savedFormData['recipient_physical_address']);
        $recipient_city             = htmlspecialchars($savedFormData['recipient_city']);
        $recipient_postal_index     = htmlspecialchars($savedFormData['recipient_postal_index']);

        $recipient_legal_person     = $savedFormData['recipient_legal_person'];
        $recipient_company          = htmlspecialchars($savedFormData['recipient_company']);
        $recipient_ceo_name         = htmlspecialchars($savedFormData['recipient_ceo_name']);
        $recipient_legal_address    = htmlspecialchars($savedFormData['recipient_legal_address']);
        $recipient_bank_name        = htmlspecialchars($savedFormData['recipient_bank_name']);
        $recipient_inn              = htmlspecialchars($savedFormData['recipient_inn']);
        $recipient_bik              = htmlspecialchars($savedFormData['recipient_bik']);
        $recipient_settl_acc        = htmlspecialchars($savedFormData['recipient_settl_acc']);
        $recipient_corr_acc         = htmlspecialchars($savedFormData['recipient_corr_acc']);

        $recipient_payer_different  = $savedFormData['recipient_payer_different'];

        $payer_name                 = htmlspecialchars($savedFormData['payer_name']);
        $payer_surname              = htmlspecialchars($savedFormData['payer_surname']);
        $payer_email                = htmlspecialchars($savedFormData['payer_email']);
        $payer_phone                = htmlspecialchars($savedFormData['payer_phone']);
        $payer_legal_person         = $savedFormData['payer_legal_person'];
        $payer_company              = htmlspecialchars($savedFormData['payer_company']);
        $payer_ceo_name             = htmlspecialchars($savedFormData['payer_ceo_name']);
        $payer_legal_address        = htmlspecialchars($savedFormData['payer_legal_address']);
        $payer_bank_name            = htmlspecialchars($savedFormData['payer_bank_name']);
        $payer_inn                  = htmlspecialchars($savedFormData['payer_inn']);
        $payer_bik                  = htmlspecialchars($savedFormData['payer_bik']);
        $payer_settl_acc            = htmlspecialchars($savedFormData['payer_settl_acc']);
        $payer_corr_acc             = htmlspecialchars($savedFormData['payer_corr_acc']);

        $comment                    = htmlspecialchars($savedFormData['comment']);
    }
?>

<form action="<?php echo $action; ?>" method="post" id="checkout-order">

    <div id="recipient-order">

        <h2>Получатель</h2>

        <?php if ($authUser && !empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
            <div id="recipient-profile">
                <div>
                    <div>Профиль получателя</div>
                    <div>
                        <select name="recipient_profile">
                            <option value="0">Выберите</option>
                            <?php foreach ($profiles as $profile): ?>
                                <option value="<?php echo $profile['id']; ?>"><?php echo $profile['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div>
            <label><input type="checkbox" name="recipient_legal_person"<?php if ($recipient_legal_person) echo ' checked="checked"'; ?> value="1" /> <span>Юридическое лицо</span></label> <span class="legal_person_help">?</span>
        </div>

        <div id="recipient-legal-person">
            <h3>Юридическое лицо</h3>
            <div>
                <div>Название компании <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_company" maxlength="64" value="<?php echo $recipient_company; ?>" /></div>
            </div>
            <div>
                <div>Генеральный директор <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_ceo_name" maxlength="64" value="<?php echo $recipient_ceo_name; ?>" /></div>
            </div>
            <div>
                <div>Юридический адрес <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_legal_address" maxlength="250" value="<?php echo $recipient_legal_address; ?>" /></div>
            </div>
            <div>
                <div>ИНН <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_inn" maxlength="32" value="<?php echo $recipient_inn; ?>" /></div>
            </div>
            <div>
                <div>Название банка <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_bank_name" maxlength="64" value="<?php echo $recipient_bank_name; ?>" /></div>
            </div>
            <div>
                <div>БИК <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_bik" maxlength="32" value="<?php echo $recipient_bik; ?>" /></div>
            </div>
            <div>
                <div>Расчетный счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_settl_acc" maxlength="32" value="<?php echo $recipient_settl_acc; ?>" /></div>
            </div>
            <div>
                <div>Корреспондентский счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_corr_acc" maxlength="32" value="<?php echo $recipient_corr_acc; ?>" /></div>
            </div>
        </div>

        <div id="recipient-physical-person">
            <h3>Контактное лицо</h3>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_surname" maxlength="32" value="<?php echo $recipient_surname; ?>" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_name" maxlength="32" value="<?php echo $recipient_name; ?>" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_email" maxlength="32" value="<?php echo $recipient_email; ?>" /></div>
            </div>
            <div>
                <div>Телефон</div>
                <div><input type="text" name="recipient_phone" maxlength="32" value="<?php echo $recipient_phone; ?>" /></div>
            </div>
        </div>

        <div id="recipient-own-shipping">
            <label><input type="checkbox" name="own_shipping"<?php if ($own_shipping) echo ' checked="checked"'; ?> value="1" /> <span>Самовывоз со склада</span></label>
            <?php if (!empty($offices)): ?>
                <select name="office">
                <?php foreach($offices as $key => $value): ?>
                    <option value="<?php echo $key; ?>"<?php if ($key == $own_shipping) echo ' selected="selected"'; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div id="recipient-physical-address">
            <h3>Адрес доставки</h3>
            <div>
                <div>Адрес <span class="form-field-required">*</span></div>
                <div><input type="text" name="recipient_physical_address" maxlength="250" value="<?php echo $recipient_physical_address; ?>" /></div>
            </div>
            <div>
                <div>Город</div>
                <div><input type="text" name="recipient_city" maxlength="32" value="<?php echo $recipient_city; ?>" /></div>
            </div>
            <div>
                <div>Почтовый индекс</div>
                <div><input type="text" name="recipient_postal_index" maxlength="32" value="<?php echo $recipient_postal_index; ?>" /></div>
            </div>
        </div>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_recipient_profile" checked="checked" value="1" />
                    <span>Создать профиль получателя</span>
                </label>
                <span class="make_profile_help">?</span>
            </div>
        <?php endif; ?>

    </div>

    <div>
        <label>
            <input type="checkbox" name="recipient_payer_different"<?php if ($recipient_payer_different) echo ' checked="checked"'; ?> value="1" />
            <span>Плательщик и получатель различаются</span>
        </label>
    </div>

    <div id="payer-order">

        <h2>Плательщик</h2>

        <?php if ($authUser && !empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
            <div id="payer-profile">
                <div>
                    <div>Профиль плательщика</div>
                    <div>
                        <select name="payer_profile">
                            <option value="0">Выберите</option>
                        <?php foreach ($profiles as $profile): ?>
                            <option value="<?php echo $profile['id']; ?>"><?php echo $profile['title']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div>
            <label>
                <input type="checkbox" name="payer_legal_person"<?php if ($payer_legal_person) echo ' checked="checked"'; ?> value="1" />
                <span>Юридическое лицо</span>
            </label>
            <span class="legal_person_help">?</span>
        </div>

        <div id="payer-legal-person">
            <h3>Юридическое лицо</h3>
            <div>
                <div>Название компании <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_company" maxlength="64" value="<?php echo $payer_company; ?>" /></div>
            </div>
            <div>
                <div>Генеральный директор <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_ceo_name" maxlength="64" value="<?php echo $payer_ceo_name; ?>" /></div>
            </div>
            <div>
                <div>Юридический адрес <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_legal_address" maxlength="250" value="<?php echo $payer_legal_address; ?>" /></div>
            </div>
            <div>
                <div>ИНН <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_inn" maxlength="32" value="<?php echo $payer_inn; ?>" /></div>
            </div>
            <div>
                <div>Название банка <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_bank_name" maxlength="64" value="<?php echo $payer_bank_name; ?>" /></div>
            </div>
            <div>
                <div>БИК <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_bik" maxlength="32" value="<?php echo $payer_bik; ?>" /></div>
            </div>
            <div>
                <div>Расчетный счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_settl_acc" maxlength="32" value="<?php echo $payer_settl_acc; ?>" /></div>
            </div>
            <div>
                <div>Корреспондентский счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_corr_acc" maxlength="32" value="<?php echo $payer_corr_acc; ?>" /></div>
            </div>
        </div>

        <div id="payer-physical-person">
            <h3>Контактное лицо</h3>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_surname" maxlength="32" value="<?php echo $payer_surname; ?>" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_name" maxlength="32" value="<?php echo $payer_name; ?>" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_email" maxlength="32" value="<?php echo $payer_email; ?>" /></div>
            </div>
            <div>
                <div>Телефон</div>
                <div><input type="text" name="payer_phone" maxlength="32" value="<?php echo $payer_phone; ?>" /></div>
            </div>
        </div>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_payer_profile" checked="checked" value="1" />
                    <span>Создать профиль плательщика</span>
                </label>
                <span class="make_profile_help">?</span>
            </div>
        <?php endif; ?>

    </div>

    <div id="comment-order">
        <div>
            <div>
                <div>Комментарий</div>
                <div><textarea name="comment" maxlength="250"><?php echo $comment; ?></textarea></div>
            </div>
        </div>
    </div>

    <div>
        <div>
            <div>
                <div></div>
                <div><input type="submit" name="submit" value="Отправить" /></div>
            </div>
        </div>
    </div>

</form>

<!-- Конец шаблона view/example/frontend/template/basket/checkout/center.php -->
