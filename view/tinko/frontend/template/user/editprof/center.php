<?php
/**
 * Форма для редактирования профиля пользователя,
 * файл view/example/backend/template/catalog/editprof/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 *
 * $id - уникальный идентификатор профиля
 * $title - название профиля
 * $name - имя контактного лица
 * $surname - фамилия контактного лица
 * $email - e-mail контактного лица
 * $phone - телефон контактного лица
 * $shipping - самовывоз со склада?
 * $offices - массив офисов для самовывоза
 * $shipping_address - адрес доставки
 * $shipping_city - город  доставки
 * $shipping_index - почтовый индекс
 * $company - юридическое лицо?
 * $company_name - название компании
 * $company_ceo - генеральный директор
 * $company_address - юридический адрес
 * $company_inn - ИНН
 * $bank_name - название банка
 * $bank_bik - БИК банка
 * $settl_acc - расчетный счет
 * $corr_acc - корреспондентский счет
 *
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
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

<!-- Начало шаблона view/example/backend/template/catalog/editprof/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование профиля</h1>

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
    $title            = htmlspecialchars($title);            // название профиля
    $name             = htmlspecialchars($name);             // имя контактного лица
    $surname          = htmlspecialchars($surname);          // фамилия контактного лица
    $email            = htmlspecialchars($email);            // e-mail контактного лица
    $phone            = htmlspecialchars($phone);            // телефон контактного лица
    $shipping_address = htmlspecialchars($shipping_address); // адрес доставки
    $shipping_index   = htmlspecialchars($shipping_index );  // почтовый индекс
    $company_name     = htmlspecialchars($company_name);     // название компании
    $company_ceo      = htmlspecialchars($company_ceo);      // генеральный директор
    $company_address  = htmlspecialchars($company_address);  // юридический адрес
    $company_inn      = htmlspecialchars($company_inn);      // ИНН
    $bank_name        = htmlspecialchars($bank_name);        // название банка
    $bank_bik         = htmlspecialchars($bank_bik);         // БИК банка
    $settl_acc        = htmlspecialchars($settl_acc);        // расчетный счет
    $corr_acc         = htmlspecialchars($corr_acc);         // корреспондентский счет

    if (isset($savedFormData)) {
        $title            = htmlspecialchars($savedFormData['title']);
        $name             = htmlspecialchars($savedFormData['name']);
        $surname          = htmlspecialchars($savedFormData['surname']);
        $email            = htmlspecialchars($savedFormData['email']);
        $phone            = htmlspecialchars($savedFormData['phone']);
        $shipping         = $savedFormData['shipping'];
        $shipping_address = htmlspecialchars($savedFormData['shipping_address']);
        $shipping_index   = htmlspecialchars($savedFormData['shipping_index']);
        $company          = $savedFormData['company'];
        $company_name     = htmlspecialchars($savedFormData['company_name']);
        $companyceo       = htmlspecialchars($savedFormData['company_ceo']);
        $company_address  = htmlspecialchars($savedFormData['company_address']);
        $company_inn      = htmlspecialchars($savedFormData['company_inn']);
        $bank_name        = htmlspecialchars($savedFormData['bank_name']);
        $bank_bik         = htmlspecialchars($savedFormData['bank_bik']);
        $settl_acc        = htmlspecialchars($savedFormData['settl_acc']);
        $corr_acc         = htmlspecialchars($savedFormData['corr_acc']);
    }
?>

<form action="<?php echo $action; ?>" method="post" id="add-edit-profile">
    <div>
        <div>
            <div>Название профиля <span class="form-field-required">*</span></div>
            <div><input type="text" name="title" maxlength="32" value="<?php echo $title; ?>" /> <span id="profile-title-help">?</span></div>
        </div>
    </div>

    <div>
        <label>
            <input type="checkbox" name="company" value="1"<?php echo $company ? ' checked="checked"' : ''; ?> />
            <span>Юридическое лицо</span>
        </label>
        <span id="company-checkbox-help">?</span>
    </div>

    <div id="company">
        <h2>Юридическое лицо</h2>
        <div>
            <div>Название компании <span class="form-field-required">*</span></div>
            <div><input type="text" name="company_name" maxlength="64" value="<?php echo $company_name; ?>" /></div>
        </div>
        <div>
            <div>Генеральный директор <span class="form-field-required">*</span></div>
            <div><input type="text" name="company_ceo" maxlength="64" value="<?php echo $company_ceo; ?>" /></div>
        </div>
        <div>
            <div>Юридический адрес <span class="form-field-required">*</span></div>
            <div><input type="text" name="company_address" maxlength="250" value="<?php echo $company_address; ?>" /></div>
        </div>
        <div>
            <div>ИНН <span class="form-field-required">*</span>, КПП</div>
            <div>
                <input type="text" name="company_inn" maxlength="12" value="<?php echo $company_inn; ?>" placeholder="ИНН" />
                <input type="text" name="company_kpp" maxlength="9" value="<?php echo $company_kpp; ?>" placeholder="КПП" />
            </div>
        </div>
        <div>
            <div>Название банка <span class="form-field-required">*</span></div>
            <div><input type="text" name="bank_name" maxlength="64" value="<?php echo $bank_name; ?>" /></div>
        </div>
        <div>
            <div>БИК банка <span class="form-field-required">*</span></div>
            <div><input type="text" name="bank_bik" maxlength="9" value="<?php echo $bank_bik; ?>" /></div>
        </div>
        <div>
            <div>Расчетный счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="settl_acc" maxlength="20" value="<?php echo $settl_acc; ?>" /></div>
        </div>
        <div>
            <div>Корреспондентский счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="corr_acc" maxlength="20" value="<?php echo $corr_acc; ?>" /></div>
        </div>
    </div>

    <div>
        <h2>Контактное лицо</h2>
        <div>
            <div>Фамилия <span class="form-field-required">*</span></div>
            <div><input type="text" name="surname" maxlength="32" value="<?php echo $surname; ?>" /></div>
        </div>
        <div>
            <div>Имя <span class="form-field-required">*</span></div>
            <div>
                <input type="text" name="name" maxlength="16" value="<?php echo $name; ?>" placeholder="имя" />
                <input type="text" name="patronymic" maxlength="16" value="<?php echo $patronymic; ?>" placeholder="отчество" />
            </div>
        </div>
        <div>
            <div>E-mail <span class="form-field-required">*</span></div>
            <div><input type="text" name="email" maxlength="32" value="<?php echo $email; ?>" /></div>
        </div>
        <div>
            <div>Телефон</div>
            <div><input type="text" name="phone" maxlength="32" value="<?php echo $phone; ?>" placeholder="+7 (495) 123-45-67" /></div>
        </div>
    </div>

    <div>
        <label><input type="checkbox" name="shipping" value="1"<?php echo $shipping ? ' checked="checked"' : ''; ?> /> <span>Самовывоз со склада</span></label>
        <?php if (!empty($offices)): ?>
            <select name="office">
                <?php foreach($offices as $key => $value): ?>
                    <option value="<?php echo $key; ?>"<?php if ($key == $shipping) echo ' selected="selected"'; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <div id="shipping-address">
        <h2>Адрес доставки</h2>
        <div>
            <div>Адрес доставки <span class="form-field-required">*</span></div>
            <div><input type="text" name="shipping_address" maxlength="250" value="<?php echo $shipping_address; ?>" /></div>
        </div>
        <div>
            <div>Город, почтовый индекс</div>
            <div>
                <input type="text" name="shipping_city" maxlength="32" value="<?php echo $shipping_city; ?>" placeholder="город" />
                <input type="text" name="shipping_index" maxlength="32" value="<?php echo $shipping_index; ?>" placeholder="индекс" />
            </div>
        </div>
    </div>

    <div>
        <div>
            <div></div>
            <div><input type="submit" name="submit" value="Сохранить" /></div>
        </div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/editprof/center.php -->
