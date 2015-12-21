<?php
/**
 * Форма для редактирования личных данных пользователя,
 * файл view/example/backend/template/user/edit/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $action - атрибут action тега form
 * $name - имя пользователя
 * $surname - фамилия пользователя
 * $type - тип пользователя
 * $types - типы пользователей, для возможности выбора
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже введенными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/user/edit/center.php -->

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h1>Личные данные</h1>

<?php if (!empty($errorMessage)): ?>
    <ul>
    <?php foreach($errorMessage as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
    $change   = false;
    $password = '';
    $confirm  = '';

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $surname = htmlspecialchars($savedFormData['surname']);
        $change  = $savedFormData['change'];
        $type    = $savedFormData['type'];
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-user">
    <div>
        <div>Имя</div>
        <div><input type="text" name="name" maxlength="32" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Фамилия</div>
        <div><input type="text" name="surname" maxlength="32" value="<?php echo $surname; ?>" /></div>
    </div>
    <div>
        <div>Тип пользователя</div>
        <div>
            <select name="type">
            <option value="0">Выберите</option>
            <?php if (!empty($types)): ?>
                <?php foreach ($types as $key => $value): ?>
                    <option value="<?php echo $key; ?>"<?php if ($key == $type) echo 'selected="selected"'; ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div></div>
        <div>
            <input type="checkbox" name="change" value="1"<?php echo ($change) ? ' checked="checked"' : ''; ?> />
            изменить пароль
        </div>
    </div>
    <div class="password">
        <div>Пароль</div>
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
</div>
</form>

<!-- Конец шаблона view/example/backend/template/user/edit/center.php -->
