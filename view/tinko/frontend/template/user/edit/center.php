<?php
/**
 * Форма для редактирования личных данных пользователя,
 * файл view/example/frontend/template/user/edit/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $action - атрибут action тега form
 * $name - имя пользователя
 * $surname - фамилия пользователя
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже введенными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/edit/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Личные данные</h1>

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
    $change   = false;
    $password = '';
    $confirm  = '';

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $surname = htmlspecialchars($savedFormData['surname']);
        $change  = $savedFormData['change'];
    }
?>

<form action="<?php echo $action; ?>" method="post" id="edit-user">
    <div>
        <div>Фамилия</div>
        <div><input type="text" name="surname" maxlength="32" value="<?php echo $surname; ?>" /></div>
    </div>
    <div>
        <div>Имя</div>
        <div><input type="text" name="name" maxlength="32" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div></div>
        <div>
            <label><input type="checkbox" name="change" value="1"<?php echo ($change) ? ' checked="checked"' : ''; ?> /> <span>изменить пароль</span></label>
        </div>
    </div>
    <div class="password">
        <div>Новый пароль</div>
        <div><input type="text" name="password" maxlength="32" value="<?php echo $password; ?>" /></div>
    </div>
    <div class="password">
        <div>Подтвердите пароль</div>
        <div><input type="text" name="confirm" maxlength="32" value="<?php echo $confirm; ?>" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/frontend/template/user/edit/center.php -->