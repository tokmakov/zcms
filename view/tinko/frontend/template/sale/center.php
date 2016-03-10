<?php
/**
 * Товары по сниженным ценам,
 * файл view/example/frontend/template/sale/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $sale - массив всех товаров и категорий
 * $units - массив единиц измерения товара
 *
 * $sale = Array (
 *   [0] => Array (
 *     [category] => Охранное телевидение
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [code] => 238407
 *         [name] => STC-3523/3 ULTIMATE
 *         [title] => Видеокамера купольная
 *         [image] => http://www.host.ru/files/sale/1.jpg
 *         [count] => 10
 *         [price1] => 7499.00
 *         [price2] => 6499.00
 *         [unit] => 1
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [code] => 042419
 *         [name] => ED-100/C-3W
 *         [title] => Видеокамера купольная
 *         [image] => http://www.host.ru/files/sale/2.jpg
 *         [count] => 5
 *         [price1] => 3368.000
 *         [price2] => 2200.00
 *         [unit] => 1
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [category] => Охранно-пожарная сигнализация
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [code] => 236557
 *         [name] => FG-1608
 *         [title] => Извещатель охранный
 *         [image] =>
 *         [count] => 48
 *         [price1] => 1452.00
 *         [price2] => 900.00
 *         [unit] => 1
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [code] => 231530
 *         [name] => ВС-СМК ВЕКТОР
 *         [title] => Извещатель магнитоконтактный
 *         [image] =>
 *         [count] => 17
 *         [price1] => 1244.00000
 *         [price2] => 800.00000
 *         [unit] => 1
 *       )
 *     )
 *   )
 * )
 *
 * $units = Array (
 *   [0] => руб
 *   [1] => руб/шт
 *   [2] => руб/компл
 *   [3] => руб/упак
 *   [4] => руб/метр
 *   [5] => руб/пара
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/sale/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Распродажа</h1>

<p>
Уважаемые партнеры! Ниже представлен периодически обновляемый список изделий, на которые действует особая цена. И вам не стоит сомневаться в качестве — на все оборудование, предлагаемое в данном разделе, распространяются стандартные условия гарантии.
</p>

<?php if ( ! empty($sale)): ?>
    <div id="sale-products">
    <?php foreach($sale as $item): ?>
        <h2><?php echo $item['category']; ?></h2>
        <?php if ( ! empty($item['products'])): ?>
            <table>
            <tr>
                <th>№</th>
                <th>Код</th>
                <th>Наименование</th>
                <th>Кол.</th>
                <th>Цена</th>
                <th>Цена</th>
                <th>Ед.изм.</th>
            </tr>
            <?php foreach($item['products'] as $product): ?>
                <tr>
                    <td><?php echo $product['number']; ?></td>
                    <td><?php echo $product['code']; ?></td>
                    <td>
                        <span><?php echo $product['name']; ?></span>
                        <div>
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="" />
                            <?php endif; ?>
                            <div>
                                <span><?php echo $product['title']; ?></span>
                                <span><?php echo nl2br($product['description']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo $product['count']; ?></td>
                    <td><?php echo number_format($product['price1'], 2, '.', ''); ?></td>
                    <td><?php echo number_format($product['price2'], 2, '.', ''); ?></td>
                    <td><i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?></td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/sale/center.php -->

