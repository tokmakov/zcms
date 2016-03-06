<?php
/**
 * Список товаров, отложенных для сравнения посетителем сайта,
 * файл view/example/frontend/template/compare/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $thisPageUrl - URL этой страницы
 * $gridPageUrl - URL ссылки на список сравнения
 * $name - наимнование функциональной группы
 * $products - массив отложенных для сравнения товаров
 * $units - массив единиц измерения товара
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 37
 *     [code] => 001001
 *     [name] => ИО-102
 *     [title] => Извещатель охранный магнитоконтактный
 *     [shortdescr] =>
 *     [price] => 200.00
 *     [image] => 8710c4a3ed9f660b5549092b5378c42c.jpg
 *     [techdata] => Array (
 *       [0] => Array (
 *         [0] => Маркировка по взрывозащите
 *         [1] => нет
 *       )
 *       [1] => Array (
 *         [0] => Тип контактов
 *         [1] => НЗ
 *       )
 *       [2] => Array (
 *         [0] => Расстояние между магнитом и герконом, мм:
 *         [1] => 10
 *       )
 *     )
 *     [ctg_id] => 2
 *     [ctg_name] => Извещатели охранные
 *     [mkr_id] => 7
 *     [mkr_name] => Болид
 *     [date] => 28.11.2014
 *     [time] => 11:52:47
 *     [url] => Array (
 *       [product] => /catalog/product/37
 *       [maker] => /catalog/maker/7
 *       [image] => /files/catalog/products/small/8710c4a3ed9f660b5549092b5378c42c.jpg
 *     )
 *     [action] => Array (
 *       [basket] => /basket/addprd/37
 *       [wished] => /wished/addprd/37
 *       [compared] => /compare/rmvprd/37
 *     )
 *   )
 *   [1] => Array (
 *     .....
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $units = Array (
 *     0 => 'руб',
 *     1 => 'руб/шт',
 *     2 => 'руб/компл',
 *     3 => 'руб/упак',
 *     4 => 'руб/метр',
 *     5 => 'руб/пара',
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/compare/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="compare-products">
    <div>
        <a href="<?php echo $gridPageUrl; ?>"><i class="fa fa-th-large"></i>&nbsp; <span>Список сравнения</span></a>
        <h1>Таблица сравнения</h1>
        <?php if (!empty($name)): ?>
            <h2>Функционал: <?php echo $name; ?></h2>
        <?php endif; ?>
    </div>

    <?php if (!empty($products)): // отложенные для сравнения товары ?>
        <table>
        <tr>
            <td></td>
            <?php foreach ($products as $product): ?>
            <td>
                <div class="product-table-item">
                    <div class="product-table-added">
                        <?php echo $product['date']; ?>
                        <?php echo $product['time']; ?>
                    </div>
                    <div class="product-table-heading">
                        <h3><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h3>
                    </div>
                    <div class="product-table-image">
                        <a href="<?php echo $product['url']['product']; ?>">
                            <?php if ($product['hit']): ?><span class="hit-product">Лидер продаж</span><?php endif; ?>
                            <?php if ($product['new']): ?><span class="new-product">Новинка</span><?php endif; ?>
                            <img src="<?php echo $product['url']['image']; ?>" alt="" />
                        </a>
                    </div>
                    <div class="product-table-price">
                        <span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?>
                    </div>
                    <div class="product-table-basket">
                        <form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                            <input type="hidden" name="return" value="compare" />
                            <input type="submit" name="submit" value="В корзину" />
                        </form>
                    </div>
                </div>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php foreach($params as $row): ?>
            <tr>
            <?php foreach($row as $cell): ?>
                    <td>
                        <?php
                            if (is_array($cell)) {
                                echo implode('<br/>', $cell);
                            } else {
                                echo $cell;
                            }
                        ?>
                    </td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Нет товаров для сравнения</p>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/compare/center.php -->
