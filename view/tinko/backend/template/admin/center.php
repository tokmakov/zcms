<?php
/**
 * Форма для авторизации администратора сайта
 * файл view/example/backend/template/admin/login/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $action - атрибут action тега form
 */

defined('ZCMS') or die('Access denied');
?>

<!-- view/example/backend/template/admin/login/center.php -->

<h1 style="text-align: center;">Войти</h1>

<form action="<?php echo $action; ?>" method="post">
<div id="login-admin">
    <div>
        <div>Имя</div>
        <div><input type="text" name="name" maxlength="32" value="" /></div>
    </div>
    <div>
        <div>Пароль</div>
        <div><input type="password" name="password" maxlength="32" value="" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Войти" /></div>
    </div>
</div>
</form>