<?php
/**
 * Подробная информация о заказе зарегистрированного пользователя,
 * файл view/example/frontend/template/user/order/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $order - подробная информация о заказе
 * $offices - список офисов для самовывоза
 *
 * $order = Array (
 *   [order_id] => 47
 *   [amount] => 62592.40000
 *   [user_amount] => 44984.95000
 *   [date] => 25.02.2016
 *   [time] => 11:23:17
 *   [status] => 0
 *   [payer_name] => Алексей
 *   [payer_surname] => Миронов
 *   [payer_patronymic] => Михайлович
 *   [payer_email] => mironov.a@mail.ru
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
 *   [payer_getter_different] => 1
 *   [getter_surname] => Токмаков
 *   [getter_name] => Евгений
 *   [getter_patronymic] => Юрьевич
 *   [getter_email] => tokmakov.e@mail.ru
 *   [getter_phone] =>
 *   [getter_company] => 1
 *   [getter_company_name] => ООО "ТД ТИНКО"
 *   [getter_company_ceo] => Клещенок Геннадий Степанович
 *   [getter_company_address] => г Москва, ул Щепкина, д 47 стр 1
 *   [getter_company_inn] => 7702680818
 *   [getter_company_kpp] => 770201001
 *   [getter_bank_name] => БАНК ВТБ (ПАО)
 *   [getter_bank_bik] => 044525187
 *   [getter_settl_acc] => 40702810500260000795
 *   [getter_corr_acc] => 30101810700000000187
 *   [shipping] => 0
 *   [shipping_address] => г. Москва, проезд Перова Поля 3-й, д 8
 *   [shipping_city] => Москва
 *   [shipping_index] => 111141
 *   [comment] => Комментарий к заказу
 *   [products] => Array (
 *     [0] => Array(
 *       [id] => 2043
 *       [code] => 002043
 *       [name] => Стекло-3 (ИО 329-4)
 *       [title] => Извещатель охранный поверхностный звуковой
 *       [price] => 615.37000
 *       [user_price] => 615.37000
 *       [unit] => 1
 *       [quantity] => 2
 *       [cost] => 1230.74000
 *       [user_cost] => 1230.74000
 *       [exists] => 1
 *       [url] => //www.server.com/catalog/product/2043
 *     )
 *     [1] => Array (
 *       ..........
 *     )
 *   )
 *   [repeat] => true
 * )
 *
 * $offices = Array (
 *   [1] => Центральный офис
 *   [2] => Офис продаж «Сокол»
 *   [3] => Офис продаж «Мещанский»
 *   [4] => Офис продаж «Нагорный»
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/order/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Заявка № <?php echo $order['order_id']; ?></h1>

<div id="user-order">
    <p>
        <span><?php echo $order['date']; ?> <?php echo $order['time']; ?></span>
        <span>Итого: <strong><?php echo $order['user_amount'] > 1000000 ? round(($order['user_amount']/1000000),1).' млн.' : number_format($order['user_amount'], 2, '.', ' '); ?></strong> руб.</span>
    </p>
    <table>
        <tr>
            <th>Код</th>
            <th>Наименование</th>
            <th>Кол.</th>
            <th>Цена</th>
            <th>Стоим.</th>
        </tr>
        <?php foreach ($order['products'] as $product): ?>
           <tr>
                <?php if ($product['exists']): ?>
                    <td><a href="<?php echo $product['url']; ?>"><?php echo $product['code']; ?></a></td>
                <?php else: ?>
                    <td><?php echo $product['code']; ?></td>
                <?php endif; ?>
               <td><?php echo $product['name']; ?></td>
               <td><?php echo $product['quantity']; ?></td>
               <td><?php echo number_format($product['user_price'], 2, '.', ''); ?></td>
               <td><?php echo $product['user_cost'] > 1000000 ? round(($product['user_cost']/1000000),1).' млн.' : number_format($product['user_cost'], 2, '.', ''); ?></td>
           </tr>
        <?php endforeach; ?>
        <?php if (($order['amount'] - $order['user_amount']) > 0.01): ?>
            <tr><td colspan="5" class="note-user-price">Цены и стоимость указаны с учетом скидки</td></tr>
        <?php endif; ?>
    </table>

    <h2>Плательщик</h2>
    <ul>
        <li>КОНТАКТНОЕ ЛИЦО</li>
        <li>Фамилия: <?php echo $order['payer_surname']; ?></li>
        <li>Имя: <?php echo $order['payer_name']; ?></li>
        <?php if ( ! empty($order['payer_patronymic'])): ?>
            <li>Отчество: <?php echo $order['payer_patronymic']; ?></li>
        <?php endif; ?>
        <li>E-mail: <?php echo $order['payer_email']; ?></li>
        <li>Телефон: <?php echo $order['payer_phone']; ?></li>
    </ul>
    <?php if ($order['payer_company']): // плательщик - юридическое лицо? ?>
        <ul>
            <li>ЮРИДИЧЕСКОЕ ЛИЦО</li>
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

    <?php if ($order['payer_getter_different']): ?>
        <h2>Получатель</h2>
        <ul>
            <li>КОНТАКТНОЕ ЛИЦО</li>
            <li>Фамилия: <?php echo $order['getter_surname']; ?></li>
            <li>Имя: <?php echo $order['getter_name']; ?></li>
            <?php if ( ! empty($order['getter_patronymic'])): ?>
                <li>Отчество: <?php echo $order['getter_patronymic']; ?></li>
            <?php endif; ?>
            <li>E-mail: <?php echo $order['getter_email']; ?></li>
            <li>Телефон: <?php echo $order['getter_phone']; ?></li>
        </ul>
        <?php if ($order['getter_company']): // получатель - юридические лицо? ?>
            <ul>
                <li>ЮРИДИЧЕСКОЕ ЛИЦО</li>
                <li>Название компании: <?php echo $order['getter_company_name']; ?></li>
                <li>Генеральный директор: <?php echo $order['getter_company_ceo']; ?></li>
                <li>Юридический адрес: <?php echo $order['getter_company_address']; ?></li>
                <li>ИНН: <?php echo $order['getter_company_inn']; ?></li>
                <li>КПП: <?php echo $order['getter_company_kpp']; ?></li>
                <li>Название банка: <?php echo $order['getter_bank_name']; ?></li>
                <li>БИК: <?php echo $order['getter_bank_bik']; ?></li>
                <li>Расчетный счет: <?php echo $order['getter_settl_acc']; ?></li>
                <li>Корреспондентский счет: <?php echo $order['getter_corr_acc']; ?></li>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Доставка</h2>
    <ul>
    <?php if ( ! $order['shipping']): ?>
        <li>ДОСТАВКА ПО АДРЕСУ</li>
        <li>Адрес доставки: <?php echo $order['getter_shipping_address']; ?></li>
        <li>Город: <?php echo $order['getter_shipping_city']; ?></li>
        <li>Почтовый индекс: <?php echo $order['getter_shipping_index']; ?></li>
    <?php else: ?>
        <li>САМОВЫВОЗ СО СКЛАДА</li>
        <li><?php echo $offices[$order['shipping']]; ?></li>
    <?php endif; ?>
    </ul>

</div>

<!-- Конец шаблона view/example/frontend/template/user/order/center.php -->
