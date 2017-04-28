<?php
/**
 * Список всех заказов зарегистрированного пользователя,
 * файл view/example/frontend/template/user/allorders/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $orders - массив заказов пользователя
 * $pager - постраничная навигация
 * $page - текущая страница
 *
 * $orders = Array (
 *   [0] => Array (
 *     [order_id] => 38
 *     [amount] => 42296.00
 *     [user_amount] => 40147.00
 *     [date] => 04.12.2014
 *     [time] => 16:44:13
 *     [status] => 0
 *     [url] => /user/order/38
 *     [repeat] => true
 *     [action] => /user/repeat/38
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 5010
 *         [code] => 005010
 *         [name] => ИП 103-5/1-А3
 *         [title] => Извещатель пожарный тепловой максимальный
 *         [price] => 300.00
 *         [user_price] => 280.00
 *         [quantity] => 2
 *         [cost] => 600.00
 *         [exists] => 1
 *         [url] => /catalog/product/5010
 *       )
 *       [1] => Array (
 *         [id] => 5170
 *         [code] => 005170
 *         [name] => ИП 103-5/2-А1 (н.з.)
 *         [title] => Извещатель пожарный тепловой максимальный
 *         [price] => 330.00
 *         [user_price] => 330.00
 *         [quantity] => 2
 *         [cost] => 660.00
 *         [exists] => 1
 *         [url] => /catalog/product/5170
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [order_id] => 34
 *     [amount] => 144153.00
 *     [user_amount] => 134279.00
 *     [date] => 03.12.2014
 *     [time] => 16:25:12
 *     [status] => 0
 *     [url] => /user/order/34
 *     [repeat] => true
 *     [action] => /user/repeat/34
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 1001
 *         [code] => 001001
 *         [name] => ИО 102-2 (СМК-1)
 *         [title] => Извещатель охранный точечный магнитоконтактный
 *         [price] => 32.90
 *         [user_price] => 30.00
 *         [quantity] => 9
 *         [cost] => 296.10
 *         [exists] => 1
 *         [url] => /catalog/product/1001
 *       )
 *       [1] => Array (
 *         [id] => 1002
 *         [code] => 001002
 *         [name] => ИО 102-11М (СМК-3)
 *         [title] => Извещатель охранный точечный магнитоконтактный
 *         [price] => 34.00
 *         [user_price] => 31.00
 *         [quantity] => 1
 *         [cost] => 34.00
 *         [exists] => 1
 *         [url] => /catalog/product/1002
 *       )
 *     )
 *   )
 * )
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/user/allorders/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Ваши заявки</h1>
<?php if (empty($orders)): ?>
    <p>У Вас еще нет заявок.</p>
    <?php return; ?>
<?php endif; ?>

<div id="user-orders-list">
    <?php foreach($orders as $order): ?>
        <div>
            <p>
                <span>№ <strong><?php echo $order['order_id']; ?></strong> от <?php echo $order['date']; ?> <?php echo $order['time']; ?></span>
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
            <?php if (($order['amount'] - $order['user_amount']) > 1): ?>
                <tr><td colspan="5" class="note-user-price">Цены и стоимость указаны с учетом скидки</td></tr>
            <?php endif; ?>
            </table>
            <div>
                <div>
                    <?php if ($order['repeat']): ?>
                        <form action="<?php echo $order['action']; ?>" method="post">
                            <input type="hidden" name="page" value="<?php echo $page; ?>" />
                            <input type="submit" name="submit" value="Повторить заявку" />
                        </form>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?php echo $order['url']; ?>">Подробнее</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $pager['first']['url']; ?>" class="first-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $pager['prev']['url']; ?>" class="prev-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left) : ?>
            <li>
                <a href="<?php echo $left['url']; ?>"><?php echo $left['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

        <li>
            <span><?php echo $pager['current']['num']; // текущая страница ?></span>
        </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right) : ?>
            <li>
                <a href="<?php echo $right['url']; ?>"><?php echo $right['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $pager['next']['url']; ?>" class="next-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $pager['last']['url']; ?>" class="last-page"></a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/user/allorders/center.php -->

