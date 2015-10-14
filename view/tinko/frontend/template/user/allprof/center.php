<?php
/**
 * Список профилей зарегистрированного (и авторизованного) пользователя,
 * файл view/example/frontend/template/user/allprof/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $profiles - массив профилей пользователя
 * $addProfileUrl - URL ссылки для добавления нового профиля
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/allprof/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Ваши профили</h1>

<?php if (!empty($profiles)): ?>
    <div id="all-profiles">
        <ul>
        <?php foreach($profiles as $profile): ?>
            <li>
                <div><?php echo $profile['title']; ?></div>
                <div>
                    <a href="<?php echo $profile['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $profile['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<p id="add-new-prof"><a href="<?php echo $addProfileUrl; ?>">Добавить профиль</a></p>

<p>Профиль упрощает процесс оформления заказа. Один раз создав и заполнив профиль, Вы сможете много раз использовать его для оформления заказов. Можно создать несколько профилей, например, для доставки по разным адресам или для оплаты разными плательщиками.</p>

<!-- Конец шаблона view/example/frontend/template/user/allprof/center.php -->

