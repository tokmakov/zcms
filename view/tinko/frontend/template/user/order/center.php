<?php
/**
 * Подробная информация о заказе зарегистрированного пользователя,
 * файл view/example/frontend/template/user/order/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $order - подробная информация о заказе
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/order/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Заказ № <?php echo $order['order_id']; ?></h1>

<div id="user-order">
    <p>
        <span><?php echo $order['date']; ?> <?php echo $order['time']; ?></span>
        <span>Итого: <strong><?php echo number_format($order['amount'], 2, '.', ''); ?></strong> руб.</span>
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
               <td><?php echo $product['code']; ?></td>
               <td><?php echo $product['name']; ?></td>
               <td><?php echo $product['quantity']; ?></td>
               <td><?php echo number_format($product['price'], 2, '.', ''); ?></td>
               <td><?php echo number_format($product['cost'], 2, '.', ''); ?></td>
           </tr>
        <?php endforeach; ?>
        <?php if (($order['amount'] - $order['user_amount']) > 0.01): ?>
            <tr><td colspan="5" class="note-user-price">Цены и стоимость заказа указаны с учетом скидки</td></tr>
        <?php endif; ?>
    </table>

    <h2>Получатель</h2>
    <ul>
        <li>Имя: <?php echo $order['buyer_name']; ?></li>
        <li>Фамилия: <?php echo $order['buyer_surname']; ?></li>
        <li>E-mail: <?php echo $order['buyer_email']; ?></li>
        <li>Телефон: <?php echo $order['buyer_phone']; ?></li>
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
    <?php if ($order['buyer_legal_person']): // получатель - юридические лицо? ?>
        <ul>
            <li>Название компании: <?php echo $order['buyer_company']; ?></li>
            <li>Генеральный директор: <?php echo $order['buyer_ceo_name']; ?></li>
            <li>Юридический адрес: <?php echo $order['buyer_legal_address']; ?></li>
            <li>ИНН: <?php echo $order['buyer_inn']; ?></li>
            <li>Название банка: <?php echo $order['buyer_bank_name']; ?></li>
            <li>БИК: <?php echo $order['buyer_bik']; ?></li>
            <li>Расчетный счет: <?php echo $order['buyer_settl_acc']; ?></li>
            <li>Корреспондентский счет: <?php echo $order['buyer_corr_acc']; ?></li>
        </ul>
    <?php endif; ?>


    <?php if ($order['buyer_payer_different']): ?>
        <h2>Плательщик</h2>
        <ul>
            <li>Имя: <?php echo $order['payer_name']; ?></li>
            <li>Фамилия: <?php echo $order['payer_surname']; ?></li>
            <li>E-mail: <?php echo $order['payer_email']; ?></li>
            <li>Телефон: <?php echo $order['payer_phone']; ?></li>
        </ul>
        <?php if ($order['payer_legal_person']): // плательщик - юридическое лицо? ?>
            <ul>
                <li>Название компании: <?php echo $order['payer_company']; ?></li>
                <li>Генеральный директор: <?php echo $order['payer_ceo_name']; ?></li>
                <li>Юридический адрес: <?php echo $order['payer_legal_address']; ?></li>
                <li>ИНН: <?php echo $order['payer_inn']; ?></li>
                <li>Название банка: <?php echo $order['payer_bank_name']; ?></li>
                <li>БИК: <?php echo $order['payer_bik']; ?></li>
                <li>Расчетный счет: <?php echo $order['payer_settl_acc']; ?></li>
                <li>Корреспондентский счет: <?php echo $order['payer_corr_acc']; ?></li>
            </ul>
        <?php endif; ?>

    <?php endif; ?>

</div>

<!-- Конец шаблона view/example/frontend/template/user/order/center.php -->

