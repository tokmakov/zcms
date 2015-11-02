<?php
/**
 * Список товаров выбранного типового решения,
 * файл view/example/backend/template/solutions/show/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $products - массив товаров выбранного типового решения
 *
 * $products = Array (
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/show/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<?php if (!empty($products)): ?>
    <div id="all-solutions">
        <ul>
        <?php foreach($products as $item) : ?>
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
<?php else: ?>
    <p>Нет товаров</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/show/center.php -->
