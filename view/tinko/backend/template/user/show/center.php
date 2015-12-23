<?php
/**
 * Подробная информация о пользователе,
 * файл view/example/backend/template/user/show/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $user - информация о пользователе
 * $orders - массив заказов пользователя
 * $thisPageUrl - URL ссылки на эту страницу
 * $pager - постраничная навигация
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/user/show/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Пользователь</h1>

<ul>
    <li>Имя: <?php echo $user['name']; ?></li>
    <li>Фамилия: <?php echo $user['surname']; ?></li>
    <li>E-mail: <?php echo $user['email']; ?></li>
</ul>

<?php if (!empty($orders)): ?>
    <h2>Заказы</h2>
    <div id="user-orders-list">
    <?php foreach($orders as $order): ?>
        <div>
            <p><?php echo $order['date']; ?> <?php echo $order['time']; ?></p>
            <table class="data-table">
                <tr>
                    <th>Код</th>
                    <th>Наименование</th>
                    <th>Функциональное наименование</th>
                    <th>Кол.</th>
                    <th>Цена</th>
                    <th>Стоим.</th>
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
            <p><a href="<?php echo $order['url']; ?>">Подробнее</a></p>
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Нет заказов</p>
<?php endif; ?>

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

<!-- Конец шаблона view/example/backend/template/user/show/center.php -->
