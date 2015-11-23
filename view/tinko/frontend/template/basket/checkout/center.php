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
    $buyer_name             = ''; // имя контактного лица получателя
    $buyer_surname          = ''; // фамилия контактного лица получателя
    $buyer_email            = ''; // e-mail контактного лица получателя
    $buyer_phone            = ''; // телефон контактного лица получателя

    $shipping               = 1;  // самовывоз?
    $buyer_shipping_address = ''; // адрес доставки получателя
    $buyer_shipping_city    = ''; // город доставки получателя
    $buyer_shipping_index   = ''; // почтовый индекс получателя

    $buyer_legal_person     = 0;  // получатель - юридическое лицо?
    $buyer_company          = ''; // название компании получателя
    $buyer_ceo_name         = ''; // генеральный директор компании получателя
    $buyer_legal_address    = ''; // юридический адрес компании получателя
    $buyer_bank_name        = ''; // название банка компании получателя
    $buyer_inn              = ''; // ИНН компании получателя
    $buyer_bik              = ''; // БИК компании получателя
    $buyer_settl_acc        = ''; // расчетный счет компании получателя
    $buyer_corr_acc         = ''; // корреспондентский счет компании получателя

    $buyer_payer_different  = 0;  // плательщик и получатель различаются?

    $payer_name             = ''; // имя контактного лица плательщика
    $payer_surname          = ''; // фамилия контактного лица плательщика
    $payer_email            = ''; // e-mail контактного лица плательщика
    $payer_phone            = ''; // телефон контактного лица плательщика

    $payer_legal_person     = 0;  // плательщик - юридическое лицо?
    $payer_company          = ''; // название компании плательщика
    $payer_ceo_name         = ''; // генеральный директор компании плательщика
    $payer_legal_address    = ''; // юридический адрес компании плательщика
    $payer_bank_name        = ''; // название банка компании плательщика
    $payer_inn              = ''; // ИНН компании плательщика
    $payer_bik              = ''; // БИК компании плательщика
    $payer_settl_acc        = ''; // расчетный счет компании плательщика
    $payer_corr_acc         = ''; // корреспондентский счет компании плательщика

    $comment                = ''; // комментарий к заказу

    if (isset($savedFormData)) {
        $buyer_name             = htmlspecialchars($savedFormData['buyer_name']);
        $buyer_surname          = htmlspecialchars($savedFormData['buyer_surname']);
        $buyer_email            = htmlspecialchars($savedFormData['buyer_email']);
        $buyer_phone            = htmlspecialchars($savedFormData['buyer_phone']);

        $shipping               = $savedFormData['shipping'];
        $buyer_shipping_address = htmlspecialchars($savedFormData['buyer_shipping_address']);
        $buyer_shipping_city    = htmlspecialchars($savedFormData['buyer_shipping_city']);
        $buyer_shipping_index   = htmlspecialchars($savedFormData['buyer_shipping_index']);

        $buyer_legal_person     = $savedFormData['buyer_legal_person'];
        $buyer_company          = htmlspecialchars($savedFormData['buyer_company']);
        $buyer_ceo_name         = htmlspecialchars($savedFormData['buyer_ceo_name']);
        $buyer_legal_address    = htmlspecialchars($savedFormData['buyer_legal_address']);
        $buyer_bank_name        = htmlspecialchars($savedFormData['buyer_bank_name']);
        $buyer_inn              = htmlspecialchars($savedFormData['buyer_inn']);
        $buyer_bik              = htmlspecialchars($savedFormData['buyer_bik']);
        $buyer_settl_acc        = htmlspecialchars($savedFormData['buyer_settl_acc']);
        $buyer_corr_acc         = htmlspecialchars($savedFormData['buyer_corr_acc']);

        $buyer_payer_different  = $savedFormData['buyer_payer_different'];

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

    <div id="buyer-order">

        <h2>Получатель</h2>

        <?php if ($authUser && !empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
            <div id="buyer-profile">
                <div>
                    <div>Профиль получателя</div>
                    <div>
                        <select name="buyer_profile">
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
            <label><input type="checkbox" name="buyer_legal_person"<?php if ($buyer_legal_person) echo ' checked="checked"'; ?> value="1" /> <span>Юридическое лицо</span></label> <span class="legal_person_help">?</span>
        </div>

        <div id="buyer-legal-person">
            <h3>Юридическое лицо</h3>
            <div>
                <div>Название компании <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_company" maxlength="64" value="<?php echo $buyer_company; ?>" /></div>
            </div>
            <div>
                <div>Генеральный директор <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_ceo_name" maxlength="64" value="<?php echo $buyer_ceo_name; ?>" /></div>
            </div>
            <div>
                <div>Юридический адрес <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_legal_address" maxlength="250" value="<?php echo $buyer_legal_address; ?>" /></div>
            </div>
            <div>
                <div>ИНН <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_inn" maxlength="32" value="<?php echo $buyer_inn; ?>" /></div>
            </div>
            <div>
                <div>Название банка <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_bank_name" maxlength="64" value="<?php echo $buyer_bank_name; ?>" /></div>
            </div>
            <div>
                <div>БИК <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_bik" maxlength="32" value="<?php echo $buyer_bik; ?>" /></div>
            </div>
            <div>
                <div>Расчетный счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_settl_acc" maxlength="32" value="<?php echo $buyer_settl_acc; ?>" /></div>
            </div>
            <div>
                <div>Корреспондентский счет <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_corr_acc" maxlength="32" value="<?php echo $buyer_corr_acc; ?>" /></div>
            </div>
        </div>

        <div id="buyer-physical-person">
            <h3>Контактное лицо</h3>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_surname" maxlength="32" value="<?php echo $buyer_surname; ?>" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_name" maxlength="32" value="<?php echo $buyer_name; ?>" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_email" maxlength="32" value="<?php echo $buyer_email; ?>" /></div>
            </div>
            <div>
                <div>Телефон</div>
                <div><input type="text" name="buyer_phone" maxlength="32" value="<?php echo $buyer_phone; ?>" /></div>
            </div>
        </div>

        <div id="buyer-shipping">
            <label><input type="checkbox" name="shipping"<?php if ($shipping) echo ' checked="checked"'; ?> value="1" /> <span>Самовывоз со склада</span></label>
            <?php if (!empty($offices)): ?>
                <select name="office">
                <?php foreach ($offices as $key => $value): ?>
                    <option value="<?php echo $key; ?>"<?php if ($key == $shipping) echo ' selected="selected"'; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div id="buyer-shipping-details">
            <h3>Адрес доставки</h3>
            <div>
                <div>Адрес <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_shipping_address" maxlength="250" value="<?php echo $buyer_shipping_address; ?>" /></div>
            </div>
            <div>
                <div>Город</div>
                <div><input type="text" name="buyer_shipping_city" maxlength="32" value="<?php echo $buyer_shipping_city; ?>" /></div>
            </div>
            <div>
                <div>Почтовый индекс</div>
                <div><input type="text" name="buyer_shipping_index" maxlength="32" value="<?php echo $buyer_shipping_index; ?>" /></div>
            </div>
        </div>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_buyer_profile" checked="checked" value="1" />
                    <span>Создать профиль получателя</span>
                </label>
                <span class="make_profile_help">?</span>
            </div>
        <?php endif; ?>

    </div>

    <div>
        <label>
            <input type="checkbox" name="buyer_payer_different"<?php if ($buyer_payer_different) echo ' checked="checked"'; ?> value="1" />
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
