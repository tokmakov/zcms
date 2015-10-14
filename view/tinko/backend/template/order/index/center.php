<?php
/**
 * Список всех заказов в магазине,
 * файл view/example/backend/template/order/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $orders - массив заказов в магазине
 * $pager - постраничная навигация
 * $thisPageUrl - URL ссылки на эту страницу
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/order/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Заказы</h1>

<?php if (!empty($orders)): ?>
    <div id="all-orders">
        <ul>
        <?php foreach($orders as $order) : ?>
            <li>
                <div>
                    №&nbsp;<?php echo $order['order_id']; ?>&nbsp;&nbsp;
                    <?php if (!empty($order['user_name'])): ?>
                        <?php echo $order['user_name'] . ' ' . $order['user_surname']; ?>
                    <?php else: ?>
                        Незарегистрированный пользователь
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?php echo $order['url']; ?>">Подробнее</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
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
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['prev'] != 1) ? '/page/'.$pager['prev'] : ''; ?>">&lt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($left != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

    <li>
        <span><?php echo $pager['current']; // текущая страница ?></span>
    </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right): ?>
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

<!-- Конец шаблона view/example/backend/template/order/index/center.php -->
