<?php
/**
 * Форма для регистрации на сайте нового пользователя,
 * файл view/example/frontend/template/user/reg/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $action - атрибут action тега form
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/reg/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Регистрация</h1>

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
    
    $surname    = '';
    $name       = '';
    $patronymic = '';
    $email      = '';
    $password   = '';
    $confirm    = '';

    if (isset($savedFormData)) {
        $surname    = htmlspecialchars($savedFormData['surname']);
        $name       = htmlspecialchars($savedFormData['name']);
        $patronymic = htmlspecialchars($savedFormData['patronymic']);
        $email      = htmlspecialchars($savedFormData['email']);
    }
?>

<form action="<?php echo $action; ?>" method="post" id="reg-user">
    <div>
        <div>Фамилия <span class="form-field-required">*</span></div>
        <div><input type="text" name="surname" maxlength="32" value="<?php echo $surname; ?>" placeholder="фамилия" /></div>
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
        <div><input type="text" name="email" maxlength="32" value="<?php echo $email; ?>" placeholder="e-mail" /></div>
    </div>
    <div>
        <div>Пароль <span class="form-field-required">*</span></div>
        <div><input type="text" name="password" maxlength="32" value="<?php echo $password; ?>" placeholder="пароль" /></div>
    </div>
    <div>
        <div>Пароль еще раз <span class="form-field-required">*</span></div>
        <div><input type="text" name="confirm" maxlength="32" value="<?php echo $confirm; ?>" placeholder="пароль" /></div>
    </div>
    <div>
        <div>Вопрос <span class="form-field-required">*</span></div>
        <div>
            <strong><?php echo $numbers[0]; ?> + <?php echo $numbers[1]; ?> =</strong>
            <select name="answer">
                <option value="0">Выберите</option>
                <option value="1">Один</option>
                <option value="2">Два</option>
                <option value="3">Три</option>
                <option value="4">Четыре</option>
                <option value="5">Пять</option>
                <option value="6">Шесть</option>
                <option value="7">Семь</option>
                <option value="8">Восемь</option>
                <option value="9">Девять</option>
            </select>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/frontend/template/user/reg/center.php -->

