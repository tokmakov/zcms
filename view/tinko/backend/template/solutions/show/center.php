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
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 1
 *     [code] => 001001
 *     [name] => ИО 102-2 (СМК-1)
 *     [title] => Извещатель охранный точечный магнитоконтактный
 *     [count] => 1
 *     [price] => 36.8
 *     [unit] => 1
 *     [heading] =>
 *     [note] => 0
 *     [sortorder] => 1
 *     [empty] = 0
 *     [url] => Array (
 *       [edit] => http://www.host.ru/backend/solutions/editprd/id/1
 *       [remove] => http://www.host.ru/backend/solutions/rmvprd/id/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 2
 *     [code] => 001003
 *     [name] => ИО 102-4 (СМК-4)
 *     [title] => Извещатель охранный точечный магнитоконтактный
 *     [count] => 1
 *     [price] => 80.54
 *     [unit] => 1
 *     [heading] =>
 *     [note] => 0
 *     [sortorder] => 1
 *     [empty] = 1
 *     [url] => Array (
 *       [edit] => http://www.host.ru/backend/solutions/editprd/id/2
 *       [remove] => http://www.host.ru/backend/solutions/rmvprd/id/2
 *     )
 *   )
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
        <?php $totalCost = 0.0; ?>
        <?php foreach($products as $item) : ?>
            <?php if ( ! empty($item['heading'])): ?>
                <?php if ($item['sortorder'] != 1): ?>
                    <tr>
                        <td colspan="10" style="text-align: right;">Итого: <?php echo $totalCost; ?></td>
                    </tr>
                    <?php $totalCost = 0.0; ?>
                <?php endif; ?>
                <tr>
                    <th colspan="10"><?php echo $item['heading']; ?></th>
                </tr>
            <?php endif; ?>
            <tr>
                <td><?php echo $item['sortorder']; ?></td>
                <td<?php echo $item['empty'] ? ' class="selected"' : ''; ?>><?php echo $item['code']; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['count']; ?><?php echo $item['note'] ? '*' : ''; ?></td>
                <td><?php echo $item['price']; ?></td>
                <td><?php echo $units[$item['unit']]; ?></td>
                <td><a href="<?php echo $item['url']['up']; ?>" title="Вверх">Вверх</a></td>
                <td><a href="<?php echo $item['url']['down']; ?>" title="Вниз">Вниз</a></td>
                <td><a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a></td>
                <td><a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a></td>
            </tr>
            <?php $totalCost = $totalCost + $item['count'] * $item['price']; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="10" style="text-align: right;">Итого: <?php echo $totalCost; ?></td>
        </tr>
    </table>
<?php else: ?>
    <p>Нет товаров</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/show/center.php -->
