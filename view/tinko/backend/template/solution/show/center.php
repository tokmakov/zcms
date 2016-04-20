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
 * $units = Array (
 *   0 => '-',
 *   1 => 'шт',
 *   2 => 'компл',
 *   3 => 'упак',
 *   4 => 'метр',
 *   5 => 'пара'
 * );
 * 
 * $products = Array (
 *   [0] => Array (
 *     [id] => 3
 *     [name] => Объектовое оборудование
 *     [amount] => 11027.25
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 61
 *         [code] => 225676
 *         [name] => RS-200TP-RB
 *         [title] => Прибор объектовый со встроенным радиопередатчиком
 *         [price] => 5510.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 5510.00
 *         [note] => 0
 *         [sortorder] => 1
 *         [empty] => 0
 *         [url] => Array (
 *           [up] => http://www.host.ru/backend/solution/prdup/id/61
 *           [down] => http://www.host.ru/backend/solution/prddown/id/61
 *           [edit] => http://www.host.ru/backend/solution/editprd/id/61
 *           [remove] => http://www.host.ru/backend/solution/rmvprd/id/61
 *         )
 *       )
 *       [1] => Array (
 *         [id] => 62
 *         [code] => 224210
 *         [name] => Риф-КТМ-N
 *         [title] => Клавиатура кодовая
 *         [price] => 1240.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 1240.00
 *         [note] => 0
 *         [sortorder] => 2
 *         [empty] => 0
 *         [url] => Array (
 *           [up] => http://www.host.ru/backend/solution/prdup/id/62
 *           [down] => http://www.host.ru/backend/solution/prddown/id/62
 *           [edit] => http://www.host.ru/backend/solution/editprd/id/62
 *           [remove] => http://www.host.ru/backend/solution/rmvprd/id/62
 *         )
 *       )
 *       [2] => Array (
 *         ..........
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 4
 *     [name] => Пультовое оборудование
 *     [amount] => 21132.81
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 68
 *         [code] => 206633
 *         [name] => RS-200PN
 *         [title] => Пульт централизованного наблюдения
 *         [price] => 16000.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 16000.00
 *         [note] => 0
 *         [sortorder] => 1
 *         [empty] => 0
 *         [url] => Array (
 *           [up] => http://www.host.ru/backend/solution/prdup/id/68
 *           [down] => http://www.host.ru/backend/solution/prddown/id/68
 *           [edit] => http://www.host.ru/backend/solution/editprd/id/68
 *           [remove] => http://www.host.ru/backend/solution/rmvprd/id/68
 *         )
 *       )
 *       [1] => Array (
 *         [id] => 69
 *         [code] => 020124
 *         [name] => RS-200RD
 *         [title] => Устройство радиоприемное
 *         [price] => 2970.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 2970.00
 *         [note] => 0
 *         [sortorder] => 2
 *         [empty] => 0
 *         [url] => Array (
 *           [up] => http://www.host.ru/backend/solution/prdup/id/69
 *           [down] => http://www.host.ru/backend/solution/prddown/id/69
 *           [edit] => http://www.host.ru/backend/solution/editprd/id/69
 *           [remove] => http://www.host.ru/backend/solution/rmvprd/id/69
 *         )
 *       )
 *       [2] => Array (
 *         ..........
 *       )
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
        <?php $amount = 0.0; ?>
        <?php foreach($products as $value) : ?>
            <tr>
                <th colspan="10"><?php echo $value['name']; ?></th>
            </tr>
            <?php foreach ($value['products'] as $item): ?>
                <tr>
                    <td><?php echo $item['sortorder']; ?></td>
                    <td<?php echo $item['empty'] ? ' class="selected"' : ''; ?>><?php echo $item['code']; ?></td>
                    <td><?php echo $item['name']; ?></td>
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
