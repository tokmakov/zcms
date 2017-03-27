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

<ul>
    <li>
        <a href="/for-buyer">В помощь покупателю</a>
        <a href="/delivery">Доставка и оплата</a>
        <a href="/credit">Товарный кредит</a>
        <a href="/support">Техническая поддержка</a>
        <a href="/rating">Рейтинг продаж</a>
        <a href="/sale">Распродажа</a>
        <a href="/repair">Гарантийный ремонт</a>
    </li>
    <li>
        <a href="/solutions">Типовые решения</a>
        <a href="/solutions/category/1">Охранно-пожарная сигнализация</a>
        <a href="/solutions/category/2">Охранное телевидение</a>
        <a href="/solutions/category/3">Контроль и управление доступом</a>
        <a href="/solutions/category/4">Домофоны</a>
        <a href="/solutions/category/5">Оповещение и музыкальная трансляция</a>
        <a href="/solutions/category/6">Системы пожаротушения</a>
    </li>
    <li>
        <a href="/about">О компании</a>
        <a href="/trading">ТД ТИНКО  — участник госзакупок</a>
        <a href="/journal">Журнал «Грани Безопасности»</a>
        <a href="/library">Библиотека технического специалиста</a>
        <a href="/partners">Партнерские сертификаты</a>
        <a href="/brands">Бренды</a>
        <a href="/vacancies">Вакансии</a>
    </li>
    <li>
        <a href="/blog">Новости</a>
        <a href="/blog/category/1">Новости компании</a>
        <a href="/blog/category/2">События отрасли</a>
        <a href="/contacts">Контакты</a>
        <a href="tel:+74957084213">+7 (495) 708-42-13</a>
        <a href="tel:+78002008465">+7 (800) 200-84-65</a>

    </li>
</ul>
<div>
    <div><span>2000—<?php echo date('Y'); ?> Торговый Дом ТИНКО<br/>Москва, 3-й проезд Перова поля, дом&nbsp;8</span></div>
    <div><span><a href="<?php echo $siteMapUrl; ?>">Карта сайта</a></span></div>
</div>

<!-- Конец шаблона view/example/frontend/template/footer.php -->
