<?php
/**
 * Главная старница административной части сайта
 * файл view/example/backend/template/admin/index/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $lastOrders - массив последних заказов в магазине
 * $lastNews - массив последних новостей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- view/example/backend/template/admin/index/center.php -->

<h1>Администрирование</h1>

<h2>Последние заказы</h2>
<div id="all-orders">
    <ul>
        <?php foreach($lastOrders as $item) : ?>
            <li>
                <div>
                    №&nbsp;<?php echo $item['order_id']; ?>&nbsp;&nbsp;
                    <?php if (!empty($item['user_name'])): ?>
                        <?php echo $item['user_name'] . ' ' . $item['user_surname']; ?>
                    <?php else: ?>
                        Незарегистрированный пользователь
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?php echo $item['url']; ?>">Подробнее</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<h2>Последние новости</h2>
<div id="all-blog-posts">
    <ul>
        <?php foreach($lastNews as $item) : ?>
            <li>
                <div><?php echo $item['name']; ?></div>
                <div>
                    <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
