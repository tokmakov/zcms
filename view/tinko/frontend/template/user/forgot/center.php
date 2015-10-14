<?php
/**
 * Страница с формой для восстановления пароля,
 * файл view/example/frontend/template/user/password/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $action - атрибут action тега form
 * $success - признак успешного восстановления пароля
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/password/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Восстановление пароля</h1>

<?php if ($success): ?>
    <p>На ваш адрес электронной почты был выслан новый пароль.</p>
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
    $email    = '';

    if (isset($savedFormData)) {
        $email   = htmlspecialchars($savedFormData['email']);
    }
?>

<form action="<?php echo $action; ?>" method="post" id="new-password">
    <div>
        <div>Ваш e-mail</div>
        <div><input type="text" name="email" maxlength="32" value="<?php echo $email; ?>" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Отправить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/frontend/template/user/password/center.php -->


