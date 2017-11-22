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
        <span><a href="/for-buyer">В помощь покупателю</a></span>
        <span><a href="/delivery">Доставка и оплата</a></span>
        <span><a href="/credit">Товарный кредит</a></span>
        <span><a href="/support">Техническая поддержка</a></span>
        <span><a href="/rating">Рейтинг продаж</a></span>
        <span><a href="/sale">Распродажа</a></span>
        <span><a href="/repair">Гарантийный ремонт</a></span>
    </li>
    <li>
        <span><a href="/solutions">Типовые решения</a></span>
        <span><a href="/solutions/category/1">Охранно-пожарная сигнализация</a></span>
        <span><a href="/solutions/category/2">Охранное телевидение</a></span>
        <span><a href="/solutions/category/3">Контроль и управление доступом</a></span>
        <span><a href="/solutions/category/4">Домофоны</a></span>
        <span><a href="/solutions/category/5">Оповещение и музыкальная трансляция</a></span>
        <span><a href="/solutions/category/6">Системы пожаротушения</a></span>
    </li>
    <li>
        <span><a href="/about">О компании</a></span>
        <span><a href="/trading">ТД ТИНКО  — участник госзакупок</a></span>
        <span><a href="/journal">Журнал «Грани Безопасности»</a></span>
        <span><a href="/library">Библиотека технического специалиста</a></span>
        <span><a href="/partners">Партнерские сертификаты</a></span>
        <span><a href="/catalog/all-brands">Бренды</a></span>
        <span><a href="/vacancies">Вакансии</a></span>
    </li>
    <li>
        <span><a href="/blog">Новости</a></span>
        <span><a href="/blog/category/1">Новости компании</a></span>
        <span><a href="/blog/category/2">События отрасли</a></span>
        <span><a href="/contacts">Контакты</a></span>
        <span><a href="tel:+74957084213">+7 (495) 708-42-13</a></span>
        <span><a href="tel:+78002008465">+7 (800) 200-84-65</a></span>
    </li>
</ul>
<div>
    <span>© 2001—<?php echo date('Y'); ?> ООО «ТД ТИНКО»</span>
    <span>
        <a href="#" class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-twitter fa-stack-1x" aria-hidden="true"></i>
        </a>
        <a href="#" class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-facebook fa-stack-1x" aria-hidden="true"></i>
        </a>
        <a href="#" class="fa-stack">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-youtube fa-stack-1x" aria-hidden="true"></i>
        </a>
    </span>
    <span><a href="<?php echo $siteMapUrl; ?>">Карта сайта</a></span>
</div>

<!-- Конец шаблона view/example/frontend/template/footer.php -->
