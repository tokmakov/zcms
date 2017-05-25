<?php
/**
 * Шапка сайта, файл view/example/frontend/template/header.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $indexUrl - URL ссылки на главную страницу сайта
 * $searchUrl - URL ссылки на страницу поиска по каталогу товаров
 * $userUrl - URL ссылки на страницу личного кабинета
 * $authUser - пользователь авторизован?
 * $basketUrl - URL ссылки на страницу с корзиной
 * $emptyBasket - корзина пуста?
 * $wishedUrl - URL ссылки на страницу отложенных товаров
 * $emptyWished - список отложенных товаров пустой?
 * $compareUrl - URL ссылки на страницу сравнения товаров
 * $emptyCompare - список товаров для сравнения пустой?
 * $viewedUrl - URL ссылки на страницу просмотренных товаров
 * $emptyViewed - список просмотренных товаров пустой?
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/header.php -->

<div id="top-logo">
    <a href="<?php echo $indexUrl; ?>"></a>
    <div>
        <span>Торговый Дом</span>
        <strong><span>Т</span>ИНКО</strong>
    </div>
</div>

<div id="top-phone">
    <div>Заказ по телефонам</div>
    <div>+7 (495) <span>708-42-13</span></div>
    <div>+7 (800) <span>200-84-65</span></div>
</div>

<div id="top-search">
    <form action="<?php echo $searchUrl; ?>" method="post">
        <input type="text" name="query" value="" placeholder="Поиск по каталогу" maxlength="64" />
        <input type="submit" name="submit" value="" />
    </form>
    <div></div>
</div>

<div id="top-menu">
    <a href="<?php echo $basketUrl; ?>" title="Ваша корзина">
        <i class="fa fa-shopping-basket<?php if ( ! $emptyBasket) echo ' selected'; ?>"></i>&nbsp;
        <span>Ваша корзина</span><span>Корзина</span>
    </a>
    <a href="<?php echo $userUrl; ?>" title="Личный кабинет">
        <i class="fa fa-user<?php if ($authUser) echo ' selected'; ?>"></i>&nbsp;
        <span>Личный кабинет</span><span>Кабинет</span>
    </a>
    <a href="<?php echo $wishedUrl; ?>" title="Отложенные товары">
        <i class="fa fa-star<?php if ( ! $emptyWished) echo ' selected'; ?>"></i>&nbsp;
        <span>Избранное</span><span>Избранное</span>
    </a>
    <a href="<?php echo $compareUrl; ?>" title="Сравнение товаров">
        <i class="fa fa-balance-scale<?php if ( ! $emptyCompare) echo ' selected'; ?>"></i>&nbsp;
        <span>Сравнение товаров</span><span>Сравнение</span>
    </a>
    <a href="<?php echo $viewedUrl; ?>" title="Вы уже смотрели">
        <i class="fa fa-eye<?php if ( ! $emptyViewed) echo ' selected'; ?>"></i>&nbsp;
        <span>Вы уже смотрели</span><span>История</span>
    </a>
</div>

<!-- Конец шаблона view/example/frontend/template/header.php -->
