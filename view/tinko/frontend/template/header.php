<?php
/**
 * Шапка сайта, файл view/example/frontend/template/header.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $indexUrl - URL ссылки на главную страницу сайта
 * $searchUrl - URL ссылки на страницу поиска по каталогу товаров
 * $basketUrl - URL ссылки на страницу с корзиной
 * $userUrl - URL ссылки на страницу личного кабинета
 * $wishedUrl - URL ссылки на страницу отложенных товаров
 * $comparedUrl - URL ссылки на страницу сравнения товаров
 * $viewedUrl - URL ссылки на страницу просмотренных товаров
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/header.php -->

<div id="top-logo">
	<a href="<?php echo $indexUrl; ?>"></a>
	<div><span>Торговый Дом</span><span><span>Т</span>ИНКО</span></div>
</div>

<div id="top-phone">
	<div></div>
	<div><span>+7 (495)</span> 708-42-13<br/><span>+7 (800)</span> 200-84-65</div>
</div>

<div id="top-search">
	<form action="<?php echo $searchUrl; ?>" method="post">
		<input type="text" name="query" value="" placeholder="Поиск по каталогу" maxlength="64" />
		<input type="submit" name="submit" value="" />
	</form>
	<div></div>
</div>

<div id="top-menu">
	<a href="<?php echo $basketUrl; ?>" title="Ваша корзина"><span>Ваша корзина</span><span>Корзина</span></a>
	<a href="<?php echo $userUrl; ?>" title="Личный кабинет"><span>Личный кабинет</span><span>Кабинет</span></a>
	<a href="<?php echo $wishedUrl; ?>" title="Отложенные товары"><span>Отложенные товары</span><span>Избранное</span></a>
	<a href="<?php echo $comparedUrl; ?>" title="Сравнение товаров"><span>Сравнение товаров</span><span>Сравнение</span></a>
	<a href="<?php echo $viewedUrl; ?>" title="Вы уже смотрели"><span>Вы уже смотрели</span><span>История</span></a>
</div>

<!-- Конец шаблона view/example/frontend/template/header.php -->
