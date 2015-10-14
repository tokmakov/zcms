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
        <div class="orders-list-item">
            <p><?php echo $order['date']; ?> <?php echo $order['time']; ?></p>
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
            <p><a href="<?php echo $order['url']; ?>">Подробнее</a></p>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
        <?php if (isset($pager['first'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>">&lt;&lt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['prev'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['prev'] : ''; ?>">&lt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['left'])): ?>
            <?php foreach ($pager['left'] as $left) : ?>
                <li>
                    <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <li>
            <span><?php echo $pager['current']; // текущая страница ?></span>
        </li>

        <?php if (isset($pager['right'])): ?>
            <?php foreach ($pager['right'] as $right) : ?>
                <li>
                    <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($pager['next'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>">&gt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['last'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>">&gt;&gt;</a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/user/show/center.php -->
