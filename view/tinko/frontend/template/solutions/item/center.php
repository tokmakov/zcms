<?php
/**
 * Страница отдельного типового решения,
 * файл view/example/frontend/template/solutions/item/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $products - массив товаров типового решения
 * $units - единицы измерения
 *
 *
 * $units = Array (
 *   0 => 'руб',
 *   1 => 'руб/шт',
 *   2 => 'руб/компл',
 *   3 => 'руб/упак',
 *   4 => 'руб/метр',
 *   5 => 'руб/пара'
 * );
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/solutions/item/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<div id="item-solutions">

    <?php echo $content1; ?>

    <?php if (!empty($products)): ?>
        <table>
            <tr>
                <th>№</th>
                <th>Код</th>
                <th>Наименование</th>
                <th>Кол.</th>
                <th>Ед.изм.</th>
                <th>Цена</th>
                <th>Стоим.</th>
            </tr>
            <?php $totalCost = 0.0; ?>
            <?php foreach($products as $item) : ?>
                <?php if ( ! empty($item['heading'])): ?>
                    <?php if ($item['sortorder'] != 1): ?>
                        <tr>
                            <td colspan="7" style="text-align: right;">
                                <strong><?php echo number_format($totalCost, 2, '.', ''); ?></strong> руб.
                            </td>
                        </tr>
                        <?php $totalCost = 0.0; ?>
                    <?php endif; ?>
                    <tr>
                        <th colspan="7" class="heading"><?php echo $item['heading']; ?></th>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td><?php echo $item['sortorder']; ?></td>
                    <?php if ( ! $item['empty']) : ?>
                        <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                    <?php else: ?>
                        <td><?php echo $item['code']; ?></td>
                    <?php endif; ?>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['count']; ?><?php echo $item['note'] ? '*' : ''; ?></td>
                    <td><?php echo $units[$item['unit']]; ?></td>
                    <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                    <td><?php $cost = $item['count'] * $item['price']; echo number_format($cost, 2, '.', ''); ?></td>
                </tr>
                <?php $totalCost = $totalCost + $cost; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="3">
                    <form>
                        <input type="submit" name="submit" value="Добавить в корзину" />
                    </form>
                </td>
                <td colspan="4">
                    <strong><?php echo number_format($totalCost, 2, '.', ''); ?></strong> руб.
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <?php echo $content2; ?>

</div>

<!-- Конец шаблона view/example/frontend/template/solutions/item/center.php -->


