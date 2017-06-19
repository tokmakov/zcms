<?php
/**
 * Форма для оформления заказа, общедоступная часть сайта,
 * файл view/example/frontend/template/basket/checkout/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * authUser - пользователь авторизован?
 * customer - не зарегистрированный пользователь уже делал заказы ранее?
 * buyer_name - фамилия контактного лица получателя
 * buyer_surname - имя контактного лица получателя
 * buyer_patronymic - отчество контактного лица получателя
 * buyer_email - e-mail контактного лица получателя
 * $profiles - массив профилей зарегистрированного и авторизованного пользователя
 * $offices - список офисов для самовывоза товара со склада
 * $message - сообщение об успешном размещении заказа
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены
 * ошибки, мы должны снова предъявить форму, заполненную уже введенными данными и
 * вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 *
 * $success - признак успешного размещения заказа (если $success=true,то будут доступны
 * только три переменные: $success, $breadcrumbs и $message, потому как остальные просто
 * не нужны)
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

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Оформление заявки</h1>

<?php if ($success): ?>
    <p><?php echo $message; ?></p>
    <?php return; ?>
<?php endif; ?>

<?php if ( ! empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $buyer_phone            = ''; // телефон контактного лица получателя

    $shipping               = 1;  // самовывоз? (0 - нет; 1,2,3,4 - да)
    $buyer_shipping_address = ''; // адрес доставки получателя
    $buyer_shipping_city    = ''; // город доставки получателя
    $buyer_shipping_index   = ''; // почтовый индекс получателя

    $buyer_company          = 0;  // получатель - юридическое лицо?
    $buyer_company_name     = ''; // название компании получателя
    $buyer_company_ceo      = ''; // генеральный директор компании получателя
    $buyer_company_address  = ''; // юридический адрес компании получателя
    $buyer_company_inn      = ''; // ИНН компании получателя
    $buyer_company_kpp      = ''; // КПП компании получателя
    $buyer_bank_name        = ''; // название банка компании получателя
    $buyer_bank_bik         = ''; // БИК банка компании получателя
    $buyer_settl_acc        = ''; // расчетный счет компании получателя
    $buyer_corr_acc         = ''; // корр. счет банка компании получателя

    // если пользователь авторизован, но у него еще нет профилей — предлагаем
    // создать профили получателя и плательщика на основе введенных данных
    $make_buyer_profile     = 1;  // создать профиль получателя на основе введенных данных?
    $make_payer_profile     = 1;  // создать профиль плательщика на основе введенных данных?

    $buyer_payer_different  = 0;  // плательщик и получатель различаются?

    $payer_surname          = ''; // фамилия контактного лица плательщика
    $payer_name             = ''; // имя контактного лица плательщика
    $payer_patronymic       = ''; // отчество контактного лица плательщика
    $payer_email            = ''; // e-mail контактного лица плательщика
    $payer_phone            = ''; // телефон контактного лица плательщика

    $payer_company          = 0;  // плательщик - юридическое лицо?
    $payer_company_name     = ''; // название компании плательщика
    $payer_company_ceo      = ''; // генеральный директор компании плательщика
    $payer_company_address  = ''; // юридический адрес компании плательщика
    $payer_company_inn      = ''; // ИНН компании плательщика
    $payer_company_kpp      = ''; // КПП компании плательщика
    $payer_bank_name        = ''; // название банка компании плательщика
    $payer_bank_bik         = ''; // БИК банка компании плательщика
    $payer_settl_acc        = ''; // расчетный счет компании плательщика
    $payer_corr_acc         = ''; // корр. счет банка компании плательщика

    $comment                = ''; // комментарий к заказу

    if (isset($savedFormData)) {
        $buyer_surname          = htmlspecialchars($savedFormData['buyer_surname']);
        $buyer_name             = htmlspecialchars($savedFormData['buyer_name']);
        $buyer_patronymic       = htmlspecialchars($savedFormData['buyer_patronymic']);
        $buyer_email            = htmlspecialchars($savedFormData['buyer_email']);
        $buyer_phone            = htmlspecialchars($savedFormData['buyer_phone']);

        $shipping               = $savedFormData['shipping'];
        $buyer_shipping_address = htmlspecialchars($savedFormData['buyer_shipping_address']);
        $buyer_shipping_city    = htmlspecialchars($savedFormData['buyer_shipping_city']);
        $buyer_shipping_index   = htmlspecialchars($savedFormData['buyer_shipping_index']);

        $buyer_company          = $savedFormData['buyer_company'];
        $buyer_company_name     = htmlspecialchars($savedFormData['buyer_company_name']);
        $buyer_company_ceo      = htmlspecialchars($savedFormData['buyer_company_ceo']);
        $buyer_company_address  = htmlspecialchars($savedFormData['buyer_company_address']);
        $buyer_company_inn      = htmlspecialchars($savedFormData['buyer_company_inn']);
        $buyer_company_kpp      = htmlspecialchars($savedFormData['buyer_company_kpp']);
        $buyer_bank_name        = htmlspecialchars($savedFormData['buyer_bank_name']);
        $buyer_bank_bik         = htmlspecialchars($savedFormData['buyer_bank_bik']);
        $buyer_settl_acc        = htmlspecialchars($savedFormData['buyer_settl_acc']);
        $buyer_corr_acc         = htmlspecialchars($savedFormData['buyer_corr_acc']);

        $make_buyer_profile     = $savedFormData['make_buyer_profile'];

        $buyer_payer_different  = $savedFormData['buyer_payer_different'];

        $payer_surname          = htmlspecialchars($savedFormData['payer_surname']);
        $payer_name             = htmlspecialchars($savedFormData['payer_name']);
        $payer_patronymic       = htmlspecialchars($savedFormData['payer_patronymic']);
        $payer_email            = htmlspecialchars($savedFormData['payer_email']);
        $payer_phone            = htmlspecialchars($savedFormData['payer_phone']);
        $payer_company          = $savedFormData['payer_company'];
        $payer_company_name     = htmlspecialchars($savedFormData['payer_company_name']);
        $payer_company_ceo      = htmlspecialchars($savedFormData['payer_company_ceo']);
        $payer_company_address  = htmlspecialchars($savedFormData['payer_company_address']);
        $payer_company_inn      = htmlspecialchars($savedFormData['payer_company_inn']);
        $payer_company_kpp      = htmlspecialchars($savedFormData['payer_company_kpp']);
        $payer_bank_name        = htmlspecialchars($savedFormData['payer_bank_name']);
        $payer_bank_bik         = htmlspecialchars($savedFormData['payer_bank_bik']);
        $payer_settl_acc        = htmlspecialchars($savedFormData['payer_settl_acc']);
        $payer_corr_acc         = htmlspecialchars($savedFormData['payer_corr_acc']);

        $make_payer_profile     = $savedFormData['make_payer_profile'];

        $comment                = htmlspecialchars($savedFormData['comment']);
    }
?>

<form action="<?php echo $action; ?>" method="post" id="checkout-order">

    <?php if ($customer): ?>
        <div id="customer">
            <label>
                <input type="checkbox" name="customer" value="1" />
                <span>Копировать из последней заявки</span>
            </label>
            <span class="customer-checkbox-help">?</span>
        </div>
    <?php endif; ?>

    <div id="buyer-order">

        <h2>Получатель</h2>

        <?php if ( ! empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
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
            <label>
                <input type="checkbox" name="buyer_company"<?php if ($buyer_company) echo ' checked="checked"'; ?> value="1" />
                <span>Юридическое лицо</span>
            </label>
            <span class="company-checkbox-help">?</span>
        </div>

        <fieldset id="buyer-company">
            <legend>Юридическое лицо</legend>
            <div>
                <div>ИНН <span class="form-field-required">*</span>, КПП</div>
                <div>
                    <input type="text" name="buyer_company_inn" maxlength="12" value="<?php echo $buyer_company_inn; ?>" placeholder="ИНН" />
                    <input type="text" name="buyer_company_kpp" maxlength="9" value="<?php echo $buyer_company_kpp; ?>" placeholder="КПП" />
                </div>
            </div>
            <div>
                <div>Название, ген.директор</div>
                <div>
                    <input type="text" name="buyer_company_name" maxlength="64" value="<?php echo $buyer_company_name; ?>" placeholder="название компании" />
                    <input type="text" name="buyer_company_ceo" maxlength="64" value="<?php echo $buyer_company_ceo; ?>" placeholder="ФИО ген.директора" />
                </div>
            </div>
            <div>
                <div>Юридический адрес</div>
                <div><input type="text" name="buyer_company_address" maxlength="250" value="<?php echo $buyer_company_address; ?>" /></div>
            </div>
            <div>
                <div>Название банка, БИК</div>
                <div>
                    <input type="text" name="buyer_bank_name" maxlength="64" value="<?php echo $buyer_bank_name; ?>" placeholder="название банка" />
                    <input type="text" name="buyer_bank_bik" maxlength="9" value="<?php echo $buyer_bank_bik; ?>" placeholder="БИК банка" />
                </div>
            </div>
            <div>
                <div>Расч.счет, корр.счет</div>
                <div>
                    <input type="text" name="buyer_settl_acc" maxlength="20" value="<?php echo $buyer_settl_acc; ?>" placeholder="расчетный счет" />
                    <input type="text" name="buyer_corr_acc" maxlength="20" value="<?php echo $buyer_corr_acc; ?>" placeholder="корреспондентский счет" />
                </div>
            </div>
        </fieldset>

        <fieldset id="buyer-physical-person">
            <legend>Контактное лицо</legend>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_surname" maxlength="32" value="<?php echo $buyer_surname; ?>" placeholder="фамилия" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div>
                    <input type="text" name="buyer_name" maxlength="32" value="<?php echo $buyer_name; ?>" placeholder="имя" />
                    <input type="text" name="buyer_patronymic" maxlength="32" value="<?php echo $buyer_patronymic; ?>" placeholder="отчество" />
                </div>
            </div>
            <div>
                <div>Телефон <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_phone" maxlength="64" value="<?php echo $buyer_phone; ?>" placeholder="+7 (495) 123-45-67" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_email" maxlength="64" value="<?php echo $buyer_email; ?>" placeholder="e-mail" /></div>
            </div>
        </fieldset>

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

        <fieldset id="buyer-shipping-details">
            <legend>Адрес доставки</legend>
            <div>
                <div>Адрес доставки <span class="form-field-required">*</span></div>
                <div><input type="text" name="buyer_shipping_address" maxlength="250" value="<?php echo $buyer_shipping_address; ?>" /></div>
            </div>
            <div>
                <div>Город, почтовый индекс</div>
                <div>
                    <input type="text" name="buyer_shipping_city" maxlength="32" value="<?php echo $buyer_shipping_city; ?>" placeholder="город" />
                    <input type="text" name="buyer_shipping_index" maxlength="6" value="<?php echo $buyer_shipping_index; ?>" placeholder="индекс" />
                </div>
            </div>
        </fieldset>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_buyer_profile"<?php if ($make_buyer_profile) echo ' checked="checked"'; ?> value="1" />
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

        <?php if ( ! empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
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
                <input type="checkbox" name="payer_company"<?php if ($payer_company) echo ' checked="checked"'; ?> value="1" />
                <span>Юридическое лицо</span>
            </label>
            <span class="company-checkbox-help">?</span>
        </div>

        <fieldset id="payer-company">
            <legend>Юридическое лицо</legend>
            <div>
                <div>ИНН <span class="form-field-required">*</span>, КПП</div>
                <div>
                    <input type="text" name="payer_company_inn" maxlength="12" value="<?php echo $payer_company_inn; ?>" placeholder="ИНН" />
                    <input type="text" name="payer_company_kpp" maxlength="9" value="<?php echo $payer_company_kpp; ?>" placeholder="КПП" />
                </div>
            </div>
            <div>
                <div>Название, ген.директор</div>
                <div>
                    <input type="text" name="payer_company_name" maxlength="64" value="<?php echo $payer_company_name; ?>" placeholder="название компании" />
                    <input type="text" name="payer_company_ceo" maxlength="64" value="<?php echo $payer_company_ceo; ?>" placeholder="ФИО ген.директора" />
                </div>
            </div>
            <div>
                <div>Юридический адрес</div>
                <div><input type="text" name="payer_company_address" maxlength="250" value="<?php echo $payer_company_address; ?>" /></div>
            </div>
            <div>
                <div>Название банка, БИК</div>
                <div>
                    <input type="text" name="payer_bank_name" maxlength="64" value="<?php echo $payer_bank_name; ?>" placeholder="название банка" />
                    <input type="text" name="payer_bank_bik" maxlength="9" value="<?php echo $payer_bank_bik; ?>" placeholder="БИК банка" />
                </div>
            </div>
            <div>
                <div>Расч.счет, корр.счет</div>
                <div>
                    <input type="text" name="payer_settl_acc" maxlength="20" value="<?php echo $payer_settl_acc; ?>" placeholder="расчетный счет" />
                    <input type="text" name="payer_corr_acc" maxlength="20" value="<?php echo $payer_corr_acc; ?>" placeholder="корреспондентский счет" />
                </div>
            </div>
        </fieldset>

        <fieldset id="payer-physical-person">
            <legend>Контактное лицо</legend>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_surname" maxlength="32" value="<?php echo $payer_surname; ?>" placeholder="фамилия" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div>
                    <input type="text" name="payer_name" maxlength="32" value="<?php echo $payer_name; ?>" placeholder="имя" />
                    <input type="text" name="payer_patronymic" maxlength="32" value="<?php echo $payer_patronymic; ?>" placeholder="отчество" />
                </div>
            </div>
            <div>
                <div>Телефон <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_phone" maxlength="64" value="<?php echo $payer_phone; ?>" placeholder="+7 (495) 123-45-67" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="payer_email" maxlength="64" value="<?php echo $payer_email; ?>" placeholder="e-mail" /></div>
            </div>
        </fieldset>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_payer_profile"<?php if ($make_payer_profile) echo ' checked="checked"'; ?> value="1" />
                    <span>Создать профиль плательщика</span>
                </label>
                <span class="make_profile_help">?</span>
            </div>
        <?php endif; ?>

    </div>

    <div>
        <div>Комментарий</div>
        <div><textarea name="comment" maxlength="250"><?php echo $comment; ?></textarea></div>
    </div>

    <div>
        <input type="submit" name="submit" value="Отправить" />
    </div>

</form>

<!-- Конец шаблона view/example/frontend/template/basket/checkout/center.php -->
