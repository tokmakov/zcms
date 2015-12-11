<?php
/**
 * Подвал страницы, файл view/example/frontend/template/footer.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $siteMapUrl - URL ссылки на карту сайта
 */
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/footer.php -->

<div><span>2000—<?php echo date('Y'); ?> Торговый Дом ТИНКО<br/>Москва, 3-й проезд Перова поля, дом&nbsp;8</span></div>
<div><span><a href="<?php echo $siteMapUrl; ?>">Карта сайта</a></span></div>

<!-- Конец шаблона view/example/frontend/template/footer.php -->
