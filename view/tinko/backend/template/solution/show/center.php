<?php
/**
 * Список товаров выбранного типового решения,
 * файл view/example/backend/template/solutions/show/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $products - массив товаров выбранного типового решения
 * $units - единицы измерения
 * $addPrdUrl - URL страницы с формой для добавления товара
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

<p><a href="<?php echo $addPrdUrl; ?>">Добавить товар</a></p>

<?php if (!empty($products)): ?>
    <table class="data-table">
        <tr>
            <th>№</th>
            <th>Код</th>
            <th width="50%">Наименование</th>
            <th>Кол.</th>
            <th>Цена</th>
            <th>Ед.изм.</th>
            <th>Вверх</th>
            <th>Вниз</th>
            <th>Ред.</th>
            <th>Удл.</th>
        </tr>
        <?php $amount = 0.0; ?>
        <?php foreach($products as $value) : ?>
            <tr>
                <th colspan="10"><?php echo $value['name']; ?></th>
            </tr>
            <?php foreach ($value['products'] as $item): ?>
                <tr>
                    <td><?php echo $item['sortorder']; ?></td>
                    <td<?php echo $item['empty'] ? ' class="selected"' : ''; ?>><?php echo $item['code']; ?></td>
                    <td><?php echo $item['name']; ?> <span class="selected"><?php echo $item['heading']; ?></span></td>
                    <td><?php echo $item['count']; ?><?php echo $item['note'] ? '*' : ''; ?></td>
                    <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                    <td><?php echo $units[$item['unit']]; ?></td>
                    <td><a href="<?php echo $item['url']['up']; ?>" title="Вверх">Вверх</a></td>
                    <td><a href="<?php echo $item['url']['down']; ?>" title="Вниз">Вниз</a></td>
                    <td><a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a></td>
                    <td><a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="10" style="text-align: right;">Итого: <?php echo number_format($value['amount'], 2, '.', ''); ?></td>
            </tr>
            <?php $amount = $amount + $value['amount']; ?>
        <?php endforeach; ?>
    </table>
    <?php if (isset($products[1])): ?>
        <p>Итого за весь комплект: <strong><?php echo number_format($amount, 2, '.', ''); ?></strong> руб.</p>
    <?php endif; ?>
<?php else: ?>
    <p>Нет товаров</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/show/center.php -->
