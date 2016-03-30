<?php
/**
 * Подробная информация об отдельном заказе в магазине,
 * файл view/example/frontend/template/order/show/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $order - подробная информация о заказе
 *
 * $order = Array (
 *   [order_id] => 47
 *   [amount] => 62592.40000
 *   [user_amount] => 44984.95000
 *   [date] => 25.02.2016
 *   [time] => 11:23:17
 *   [status] => 0
 *   [buyer_surname] => Токмаков
 *   [buyer_name] => Евгений
 *   [buyer_patronymic] => Юрьевич
 *   [buyer_email] => tokmakov.e@mail.ru
 *   [buyer_phone] =>
 *   [shipping] => 0
 *   [buyer_shipping_address] => г. Москва, проезд Перова Поля 3-й, д 8
 *   [buyer_shipping_city] => Москва
 *   [buyer_shipping_index] => 111141
 *   [buyer_company] => 1
 *   [buyer_company_name] => ООО "ТД ТИНКО"
 *   [buyer_company_ceo] => Клещенок Геннадий Степанович
 *   [buyer_company_address] => г Москва, ул Щепкина, д 47 стр 1
 *   [buyer_company_inn] => 7702680818
 *   [buyer_company_kpp] => 770201001
 *   [buyer_bank_name] => БАНК ВТБ (ПАО)
 *   [buyer_bank_bik] => 044525187
 *   [buyer_settl_acc] => 40702810500260000795
 *   [buyer_corr_acc] => 30101810700000000187
 *   [buyer_payer_different] => 1
 *   [payer_name] => Алексей
 *   [payer_surname] => Миронов
 *   [payer_patronymic] => Михайлович
 *   [payer_email] => mironov.m@mail.ru
 *   [payer_phone] =>
 *   [payer_company] => 1
 *   [payer_company_name] => ЗАО "ГОРИЗОНТ"
 *   [payer_company_ceo] => Егельский Сергей Михайлович
 *   [payer_company_address] => г Москва, ул Нелидовская, д 16
 *   [payer_company_inn] => 7733511746
 *   [payer_company_kpp] => 773301001
 *   [payer_bank_name] => ПАО СБЕРБАНК
 *   [payer_bank_bik] => 044525225
 *   [payer_settl_acc] => 40702810500260000795
 *   [payer_corr_acc] => 30101810400000000225
 *   [comment] => Комментарий к заказу
 *   [products] => Array (
 *     [0] => Array (
 *       [product_id] => 205151
 *       [code] => 205151
 *       [name] => ИПР 513-10
 *       [title] => Извещатель пожарный ручной
 *       [price] => 206.60000
 *       [user_price] => 178.64000
 *       [quantity] => 1
 *       [cost] => 206.60000
 *       [user_cost] => 178.64000
 *     )
 *     [1] => Array (
 *       ..........
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/order/show/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Заказ № <?php echo $order['order_id']; ?></h1>

<?php if ( ! empty($order['user_email'])): ?>
    <ul>
        <li>Фамилия: <?php echo $order['user_surname']; ?></li>
        <li>Имя: <?php echo $order['user_name']; ?></li>
        <?php if ( ! empty($order['user_patronymic'])): ?>
            <li>Отчество: <?php echo $order['user_patronymic']; ?></li>
        <?php endif; ?>
        <li>E-mail: <?php echo $order['user_email']; ?></li>
    </ul>
<?php else: ?>
    <p>Незарегистрированный пользователь</p>
<?php endif; ?>

<table class="data-table">
    <tr>
        <th width="10%">Код</th>
        <th width="20%">Наименование</th>
        <th width="40%">Функциональное наименование</th>
        <th width="10%">Кол.</th>
        <th width="10%">Цена</th>
        <th width="10%">Стоим.</th>
    </tr>
    <?php foreach ($order['products'] as $product): ?>
       <tr>
           <td><?php echo $product['code']; ?></td>
           <td><?php echo $product['name']; ?></td>
           <td><?php echo $product['title']; ?></td>
           <td><?php echo $product['quantity']; ?></td>
           <td><?php echo number_format($product['price'], 2, '.', ''); ?></td>
           <td><?php echo number_format($product['cost'], 2, '.', ''); ?></td>
       </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="5" align="right">Итого:</td>
        <td><?php echo number_format($order['amount'], 2, '.', ''); ?></td>
    </tr>
</table>

<?php if (empty($order['buyer_surname'])) return; /* Этот код потом удалить, только для заказов из Magento */ ?>

<h2>Получатель</h2>
<ul>
    <li>Фамилия: <?php echo $order['buyer_surname']; ?></li>
    <li>Имя: <?php echo $order['buyer_name']; ?></li>
    <?php if ( ! empty($order['buyer_patronymic'])): ?>
        <li>Отчество: <?php echo $order['buyer_patronymic']; ?></li>
    <?php endif; ?>
    <li>Телефон: <?php echo $order['buyer_phone']; ?></li>
    <li>E-mail: <?php echo $order['buyer_email']; ?></li>

</ul>
<ul>
<?php if ( ! $order['shipping']): ?>
    <li>Адрес доставки: <?php echo $order['buyer_shipping_address']; ?></li>
    <li>Город: <?php echo $order['buyer_shipping_city']; ?></li>
    <li>Почтовый индекс: <?php echo $order['buyer_shipping_index']; ?></li>
<?php else: ?>
    <li>Самовывоз со склада</li>
<?php endif; ?>
</ul>
<?php if ($order['buyer_company']): // получатель - юридические лицо? ?>
    <ul>
        <li>Название компании: <?php echo $order['buyer_company_name']; ?></li>
        <li>Генеральный директор: <?php echo $order['buyer_company_ceo']; ?></li>
        <li>Юридический адрес: <?php echo $order['buyer_company_address']; ?></li>
        <li>ИНН: <?php echo $order['buyer_company_inn']; ?></li>
        <li>КПП: <?php echo $order['buyer_company_kpp']; ?></li>
        <li>Название банка: <?php echo $order['buyer_bank_name']; ?></li>
        <li>БИК: <?php echo $order['buyer_bank_bik']; ?></li>
        <li>Расчетный счет: <?php echo $order['buyer_settl_acc']; ?></li>
        <li>Корреспондентский счет: <?php echo $order['buyer_corr_acc']; ?></li>
    </ul>
<?php endif; ?>

<?php if ($order['buyer_payer_different']): ?>
    <h2>Плательщик</h2>
    <ul>
        <li>Фамилия: <?php echo $order['payer_surname']; ?></li>
        <li>Имя: <?php echo $order['payer_name']; ?></li>
        <?php if ( ! empty($order['payer_patronymic'])): ?>
            <li>Отчество: <?php echo $order['payer_patronymic']; ?></li>
        <?php endif; ?>
        <li>Телефон: <?php echo $order['payer_phone']; ?></li>
        <li>E-mail: <?php echo $order['payer_email']; ?></li>
    </ul>
    <?php if ($order['payer_company']): // плательщик - юридическое лицо? ?>
        <ul>
            <li>Название компании: <?php echo $order['payer_company_name']; ?></li>
            <li>Генеральный директор: <?php echo $order['payer_company_ceo']; ?></li>
            <li>Юридический адрес: <?php echo $order['payer_company_address']; ?></li>
            <li>ИНН: <?php echo $order['payer_company_inn']; ?></li>
            <li>КПП: <?php echo $order['payer_company_kpp']; ?></li>
            <li>Название банка: <?php echo $order['payer_bank_name']; ?></li>
            <li>БИК: <?php echo $order['payer_bank_bik']; ?></li>
            <li>Расчетный счет: <?php echo $order['payer_settl_acc']; ?></li>
            <li>Корреспондентский счет: <?php echo $order['payer_corr_acc']; ?></li>
        </ul>
    <?php endif; ?>

<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/order/show/center.php -->
