<?php
/**
 * Личный кабинет зарегистрированного (и авторизованного) пользователя,
 * файл view/example/frontend/template/user/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $userEditUrl - URL страницы с формой для редактирования личных данных
 * $userProfilesUrl - URL страницы со списком профилей
 * $userOrdersUrl - URL страницы со списком всех заказов
 * $basketUrl - URL ссылки на страницу с корзиной
 * $userWishedUrl - URL страницы со списком отложенных товаров
 * $userViewedUrl - URL страницы со списком просмотренных товаров
 * $userLogoutUrl - ссылка для выхода из личного кабинета
 * $newUser - новый пользователь?
 * $errors - ошибки, которые были допущены при создании профилей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/index/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Личный кабинет</h1>

<div id="index-user-center">
    <ul>
        <li><a href="<?php echo $userEditUrl; ?>">Личные данные</a></li>
        <li><a href="<?php echo $userProfilesUrl; ?>">Ваши профили</a></li>
        <li><a href="<?php echo $userOrdersUrl; ?>">Ваши заявки</a></li>
        <li><a href="<?php echo $basketUrl; ?>">Ваша корзина</a></li>
        <li><a href="<?php echo $userWishedUrl; ?>">Избранное</a></li>
        <li><a href="<?php echo $userViewedUrl; ?>">Вы уже смотрели</a></li>
        <li><a href="<?php echo $userLogoutUrl; ?>">Выйти</a></li>
    </ul>

    <?php if ($newUser): ?>
        <h2>С чего начать?</h2>
        <p>Начните с <a href="<?php echo $userProfilesUrl; ?>">создания профиля</a>. Профиль упрощает процесс оформления заявки на оборудование. Один раз создав и заполнив профиль, Вы сможете много раз использовать его для оформления заявок. Можно создать несколько профилей, например, для доставки по разным адресам или для оплаты разными плательщиками.</p>
    <?php endif; ?>
</div>

<?php if ( ! empty($errors)): ?>
    <?php foreach($errors as $error): ?>
        <div class="attention">
            <div>ВНИМАНИЕ</div>
            <div>
                <p>Профиль «<?php echo $error['title']; ?>» содержит ошибк<?php echo count($error['messages']) > 1 ? 'и' : 'у'; ?>:</p>
                <ul>
                <?php foreach($error['messages'] as $message): ?>
                    <li><?php echo $message; ?></li>
                <?php endforeach; ?>
                </ul>
                <p>Вы не сможете использовать этот профиль для оформления заявок.</p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/user/index/center.php -->


