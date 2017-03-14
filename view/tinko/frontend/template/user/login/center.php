<?php
/**
 * Форма для авторизации пользователя
 * файл view/example/frontend/template/user/login/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $regUserUrl - URL страницы регистрации
 * $forgotPasswordUrl - URL страницы восстановления пароля
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/login/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Войти в личный кабинет</h1>

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
    $email    = '';
    $password = '';

    if (isset($savedFormData)) {
        $email = htmlspecialchars($savedFormData['email']);
    }
?>

<div id="login-user-center">
    <form action="<?php echo $action; ?>" method="post">
        <div>
            <div>E-mail</div>
            <div><input type="text" name="email" maxlength="64" value="<?php echo $email; ?>" /></div>
        </div>
        <div>
            <div>Пароль</div>
            <div><input type="text" name="password" maxlength="32" value="<?php echo $password; ?>" /></div>
        </div>
        <div>
            <div></div>
            <div><label><input type="checkbox" name="remember" value="1" /> <span>запомнить меня</span></label></div>
        </div>
        <div>
            <div></div>
            <div><input type="submit" name="submit" value="Войти" /></div>
        </div>
    </form>

    <ul>
        <li><a href="<?php echo $regUserUrl; ?>">Регистрация</a></li>
        <li><a href="<?php echo $forgotPasswordUrl; ?>">Забыли пароль?</a></li>
    </ul>
</div>

<!-- Конец шаблона view/example/frontend/template/user/login/center.php -->
