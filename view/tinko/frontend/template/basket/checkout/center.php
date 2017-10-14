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
 * payer_name - фамилия контактного лица плательщика
 * payer_surname - имя контактного лица плательщика
 * payer_patronymic - отчество контактного лица плательщика
 * payer_email - e-mail контактного лица плательщика
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

    $payer_phone             = ''; // телефон контактного лица плательщика

    $payer_company           = 0;  // плательщик - юридическое лицо?
    $payer_company_name      = ''; // название компании плательщика
    $payer_company_ceo       = ''; // генеральный директор компании плательщика
    $payer_company_address   = ''; // юридический адрес компании плательщика
    $payer_company_inn       = ''; // ИНН компании плательщика
    $payer_company_kpp       = ''; // КПП компании плательщика
    $payer_bank_name         = ''; // название банка компании плательщика
    $payer_bank_bik          = ''; // БИК банка компании плательщика
    $payer_settl_acc         = ''; // расчетный счет компании плательщика
    $payer_corr_acc          = ''; // корр. счет банка компании плательщика

    $payer_getter_different  = 0;  // плательщик и получатель различаются?

    $getter_surname          = ''; // фамилия контактного лица получателя
    $getter_name             = ''; // имя контактного лица получателя
    $getter_patronymic       = ''; // отчество контактного лица получателя
    $getter_email            = ''; // e-mail контактного лица получателя
    $getter_phone            = ''; // телефон контактного лица получателя
    $getter_phone            = ''; // телефон контактного лица получателя

    $getter_company          = 0;  // получатель - юридическое лицо?
    $getter_company_name     = ''; // название компании получателя
    $getter_company_ceo      = ''; // генеральный директор компании получателя
    $getter_company_address  = ''; // юридический адрес компании получателя
    $getter_company_inn      = ''; // ИНН компании получателя
    $getter_company_kpp      = ''; // КПП компании получателя
    $getter_bank_name        = ''; // название банка компании получателя
    $getter_bank_bik         = ''; // БИК банка компании получателя
    $getter_settl_acc        = ''; // расчетный счет компании получателя
    $getter_corr_acc         = ''; // корр. счет банка компании получателя

    $shipping                = 1;  // самовывоз? (0 - нет; 1,2,3,4 - да)
    $shipping_address        = ''; // адрес доставки
    $shipping_city           = ''; // город доставки
    $shipping_index          = ''; // почтовый индекс

    // если пользователь авторизован, но у него еще нет профилей — предлагаем
    // создать профили получателя и плательщика на основе введенных данных
    $make_getter_profile     = 1;  // создать профиль получателя на основе введенных данных?
    $make_payer_profile      = 1;  // создать профиль плательщика на основе введенных данных?

    $comment                 = ''; // комментарий к заказу

    if (isset($savedFormData)) {
        /*
         * плательщик
         */
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

        // плательщик и получатель различаются?
        $payer_getter_different = $savedFormData['payer_getter_different'];

        /*
         * получатель
         */
        $getter_surname         = htmlspecialchars($savedFormData['getter_surname']);
        $getter_name            = htmlspecialchars($savedFormData['getter_name']);
        $getter_patronymic      = htmlspecialchars($savedFormData['getter_patronymic']);
        $getter_email           = htmlspecialchars($savedFormData['getter_email']);
        $getter_phone           = htmlspecialchars($savedFormData['getter_phone']);

        $getter_company         = $savedFormData['getter_company'];
        $getter_company_name    = htmlspecialchars($savedFormData['getter_company_name']);
        $getter_company_ceo     = htmlspecialchars($savedFormData['getter_company_ceo']);
        $getter_company_address = htmlspecialchars($savedFormData['getter_company_address']);
        $getter_company_inn     = htmlspecialchars($savedFormData['getter_company_inn']);
        $getter_company_kpp     = htmlspecialchars($savedFormData['getter_company_kpp']);
        $getter_bank_name       = htmlspecialchars($savedFormData['getter_bank_name']);
        $getter_bank_bik        = htmlspecialchars($savedFormData['getter_bank_bik']);
        $getter_settl_acc       = htmlspecialchars($savedFormData['getter_settl_acc']);
        $getter_corr_acc        = htmlspecialchars($savedFormData['getter_corr_acc']);

        /*
         * доставка
         */
        $shipping               = $savedFormData['shipping'];
        $shipping_address       = htmlspecialchars($savedFormData['shipping_address']);
        $shipping_city          = htmlspecialchars($savedFormData['shipping_city']);
        $shipping_index         = htmlspecialchars($savedFormData['shipping_index']);

        // создать профиль плательщика на основе введенных данных?
        $make_payer_profile     = $savedFormData['make_payer_profile'];
        // создать профиль получателя на основе введенных данных?
        $make_getter_profile    = $savedFormData['make_getter_profile'];

        // комментарий к заказу
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

        <fieldset id="payer-person">
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
        <label>
            <input type="checkbox" name="payer_getter_different"<?php if ($payer_getter_different) echo ' checked="checked"'; ?> value="1" />
            <span>Плательщик и получатель различаются</span>
        </label>
    </div>

    <div id="getter-order">

        <h2>Получатель</h2>

        <?php if ( ! empty($profiles)): /* пользователь авторизован и у него есть профили */ ?>
            <div id="getter-profile">
                <div>
                    <div>Профиль получателя</div>
                    <div>
                        <select name="getter_profile">
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
                <input type="checkbox" name="getter_company"<?php if ($getter_company) echo ' checked="checked"'; ?> value="1" />
                <span>Юридическое лицо</span>
            </label>
            <span class="company-checkbox-help">?</span>
        </div>

        <fieldset id="getter-company">
            <legend>Юридическое лицо</legend>
            <div>
                <div>ИНН <span class="form-field-required">*</span>, КПП</div>
                <div>
                    <input type="text" name="getter_company_inn" maxlength="12" value="<?php echo $getter_company_inn; ?>" placeholder="ИНН" />
                    <input type="text" name="getter_company_kpp" maxlength="9" value="<?php echo $getter_company_kpp; ?>" placeholder="КПП" />
                </div>
            </div>
            <div>
                <div>Название, ген.директор</div>
                <div>
                    <input type="text" name="getter_company_name" maxlength="64" value="<?php echo $getter_company_name; ?>" placeholder="название компании" />
                    <input type="text" name="getter_company_ceo" maxlength="64" value="<?php echo $getter_company_ceo; ?>" placeholder="ФИО ген.директора" />
                </div>
            </div>
            <div>
                <div>Юридический адрес</div>
                <div><input type="text" name="getter_company_address" maxlength="250" value="<?php echo $getter_company_address; ?>" /></div>
            </div>
            <div>
                <div>Название банка, БИК</div>
                <div>
                    <input type="text" name="getter_bank_name" maxlength="64" value="<?php echo $getter_bank_name; ?>" placeholder="название банка" />
                    <input type="text" name="getter_bank_bik" maxlength="9" value="<?php echo $getter_bank_bik; ?>" placeholder="БИК банка" />
                </div>
            </div>
            <div>
                <div>Расч.счет, корр.счет</div>
                <div>
                    <input type="text" name="getter_settl_acc" maxlength="20" value="<?php echo $getter_settl_acc; ?>" placeholder="расчетный счет" />
                    <input type="text" name="getter_corr_acc" maxlength="20" value="<?php echo $getter_corr_acc; ?>" placeholder="корреспондентский счет" />
                </div>
            </div>
        </fieldset>

        <fieldset id="getter-person">
            <legend>Контактное лицо</legend>
            <div>
                <div>Фамилия <span class="form-field-required">*</span></div>
                <div><input type="text" name="getter_surname" maxlength="32" value="<?php echo $getter_surname; ?>" placeholder="фамилия" /></div>
            </div>
            <div>
                <div>Имя <span class="form-field-required">*</span></div>
                <div>
                    <input type="text" name="getter_name" maxlength="32" value="<?php echo $getter_name; ?>" placeholder="имя" />
                    <input type="text" name="getter_patronymic" maxlength="32" value="<?php echo $getter_patronymic; ?>" placeholder="отчество" />
                </div>
            </div>
            <div>
                <div>Телефон <span class="form-field-required">*</span></div>
                <div><input type="text" name="getter_phone" maxlength="64" value="<?php echo $getter_phone; ?>" placeholder="+7 (495) 123-45-67" /></div>
            </div>
            <div>
                <div>E-mail <span class="form-field-required">*</span></div>
                <div><input type="text" name="getter_email" maxlength="64" value="<?php echo $getter_email; ?>" placeholder="e-mail" /></div>
            </div>
        </fieldset>

        <?php if ($authUser && empty($profiles)): /* пользователь авторизован, но у него нет профилей */ ?>
            <div class="make-profile">
                <label>
                    <input type="checkbox" name="make_getter_profile"<?php if ($make_getter_profile) echo ' checked="checked"'; ?> value="1" />
                    <span>Создать профиль получателя</span>
                </label>
                <span class="make_profile_help">?</span>
            </div>
        <?php endif; ?>

    </div>

    <div id="delivery">
        <h2>Доставка</h2>
        <div>
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
        <fieldset>
            <legend>Адрес доставки</legend>
            <div>
                <div>Адрес доставки <span class="form-field-required">*</span></div>
                <div><input type="text" name="shipping_address" maxlength="250" value="<?php echo $shipping_address; ?>" /></div>
            </div>
            <div>
                <div>Город, почтовый индекс</div>
                <div>
                    <input type="text" name="shipping_city" maxlength="32" value="<?php echo $shipping_city; ?>" placeholder="город" />
                    <input type="text" name="shipping_index" maxlength="6" value="<?php echo $shipping_index; ?>" placeholder="индекс" />
                </div>
            </div>
        </fieldset>
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
