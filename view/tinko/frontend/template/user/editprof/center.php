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
 * $legal_person - юридическое лицо?
 * $company - название компании
 * $ceo_name - генеральный директор
 * $legal_address - юридический адрес
 * $inn - ИНН
 * $bank_name - название банка
 * $bik - БИК
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
$shipping_city    = htmlspecialchars($shipping_city);    // город доставки
$shipping_index   = htmlspecialchars($shipping_index );  // почтовый индекс
$company          = htmlspecialchars($company);          // название компании
$ceo_name         = htmlspecialchars($ceo_name);         // генеральный директор
$legal_address    = htmlspecialchars($legal_address);    // юридический адрес
$inn              = htmlspecialchars($inn);              // ИНН
$bank_name        = htmlspecialchars($bank_name);        // название банка
$bik              = htmlspecialchars($bik);              // БИК
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
    $shipping_city    = htmlspecialchars($savedFormData['shipping_city']);
    $shipping_index   = htmlspecialchars($savedFormData['shipping_index']);
    $legal_person     = $savedFormData['legal_person'];
    $company          = htmlspecialchars($savedFormData['company']);
    $ceo_name         = htmlspecialchars($savedFormData['ceo_name']);
    $legal_address    = htmlspecialchars($savedFormData['legal_address']);
    $inn              = htmlspecialchars($savedFormData['inn']);
    $bank_name        = htmlspecialchars($savedFormData['bank_name']);
    $bik              = htmlspecialchars($savedFormData['bik']);
    $settl_acc        = htmlspecialchars($savedFormData['settl_acc']);
    $corr_acc         = htmlspecialchars($savedFormData['corr_acc']);
}
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-profile">
    <div>
        <div>
            <div>Название профиля <span class="form-field-required">*</span></div>
            <div><input type="text" name="title" maxlength="32" value="<?php echo $title; ?>" /> <span id="profile-title-help">?</span></div>
        </div>
    </div>

    <div>
        <label><input type="checkbox" name="legal_person" value="1"<?php echo $legal_person ? ' checked="checked"' : ''; ?> /> <span>Юридическое лицо</span></label> <span id="legal-person-help">?</span>
    </div>

    <div id="legal-person">
        <h2>Юридическое лицо</h2>
        <div>
            <div>Название компании <span class="form-field-required">*</span></div>
            <div><input type="text" name="company" maxlength="64" value="<?php echo $company; ?>" /></div>
        </div>
        <div>
            <div>Генеральный директор <span class="form-field-required">*</span></div>
            <div><input type="text" name="ceo_name" maxlength="64" value="<?php echo $ceo_name; ?>" /></div>
        </div>
        <div>
            <div>Юридический адрес <span class="form-field-required">*</span></div>
            <div><input type="text" name="legal_address" maxlength="250" value="<?php echo $legal_address; ?>" /></div>
        </div>
        <div>
            <div>ИНН <span class="form-field-required">*</span></div>
            <div><input type="text" name="inn" maxlength="32" value="<?php echo $inn; ?>" /></div>
        </div>
        <div>
            <div>Название банка <span class="form-field-required">*</span></div>
            <div><input type="text" name="bank_name" maxlength="64" value="<?php echo $bank_name; ?>" /></div>
        </div>
        <div>
            <div>БИК <span class="form-field-required">*</span></div>
            <div><input type="text" name="bik" maxlength="32" value="<?php echo $bik; ?>" /></div>
        </div>
        <div>
            <div>Расчетный счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="settl_acc" maxlength="32" value="<?php echo $settl_acc; ?>" /></div>
        </div>
        <div>
            <div>Корреспондентский счет <span class="form-field-required">*</span></div>
            <div><input type="text" name="corr_acc" maxlength="32" value="<?php echo $corr_acc; ?>" /></div>
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
            <div><input type="text" name="name" maxlength="32" value="<?php echo $name; ?>" /></div>
        </div>
        <div>
            <div>E-mail <span class="form-field-required">*</span></div>
            <div><input type="text" name="email" maxlength="32" value="<?php echo $email; ?>" /></div>
        </div>
        <div>
            <div>Телефон</div>
            <div><input type="text" name="phone" maxlength="32" value="<?php echo $phone; ?>" /></div>
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

    <div id="shipping-address-city-index">
        <h2>Адрес доставки</h2>
        <div>
            <div>Адрес <span class="form-field-required">*</span></div>
            <div><input type="text" name="shipping_address" maxlength="250" value="<?php echo $shipping_address; ?>" /></div>
        </div>
        <div>
            <div>Город</div>
            <div><input type="text" name="shipping_city" maxlength="32" value="<?php echo $shipping_city; ?>" /></div>
        </div>
        <div>
            <div>Почтовый индекс</div>
            <div><input type="text" name="shipping_index" maxlength="32" value="<?php echo $shipping_index; ?>" /></div>
        </div>
    </div>

    <div>
        <div>
            <div></div>
            <div><input type="submit" name="submit" value="Сохранить" /></div>
        </div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/editprof/center.php -->

