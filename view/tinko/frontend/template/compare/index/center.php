<?php
/**
 * Список товаров, отложенных для сравнения посетителем сайта,
 * файл view/example/frontend/template/compare/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $thisPageUrl - URL этой страницы
 * $clearCompareURL - URL ссылки для удаления всех товаров из сравнения
 * $name - наимнование функциональной группы
 * $products - массив отложенных для сравнения товаров
 * $units - массив единиц измерения товара
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 239725
 *     [code] => 239725
 *     [name] => КОП-24ПС
 *     [title] => Оповещатель охранно-пожарный комбинированный
 *     [shortdescr] => Табло с сиреной 92дБ, Uпит.24В, Iпотр.20мА, IP41, tраб.-10...+55°С, пластиковый корпус
 *     [price] => 570.00
 *     [price2] => 533.00
 *     [price3] => 496.00
 *     [unit] => 1
 *     [hit] => 0
 *     [new] => 0
 *     [ctg_id] => 56
 *     [ctg_name] => Табло
 *     [mkr_id] => 122
 *     [mkr_name] => Системсервис
 *     [grp_id] => 63
 *     [grp_name] => Табло со встроенной сиреной
 *     [date] => 22.03.2016
 *     [time] => 10:42:06
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/239725
 *       [maker] => http://www.host.ru/catalog/maker/122
 *       [image] => http://www.host.ru/files/catalog/imgs/small/d/d/dd028fb37fafc172d99773689b661b5d.jpg
 *     )
 *     [action] => Array (
 *       [basket] => http://www.host.ru/basket/addprd
 *       [wished] => http://www.host.ru/wished/addprd
 *       [compare] => http://www.host.ru/compare/rmvprd
 *     )
 *   )
 *   [1] = Array (
 *     ..........
 *   )
 *   [2] = Array (
 *     ..........
 *   )
 * )
 *
 * $params = Array (
 *   [0] => Array (
 *     [0] => Функциональное наименование
 *     [1] => Оповещатель охранно-пожарный комбинированный
 *     [2] => Оповещатель охранно-пожарный комбинированный
 *     [3] => Оповещатель охранно-пожарный комбинированный
 *     [4] => Оповещатель охранно-пожарный комбинированный
 *     [5] => Оповещатель охранно-пожарный комбинированный
 *   )
 *   [1] => Array (
 *     [0] => Код
 *     [1] => 235497
 *     [2] => 019056
 *     [3] => 019053
 *     [4] => 019305
 *     [5] => 019128
 *   )
 *   [2] => Array (
 *     [0] => Производитель
 *     [1] => Этра-спецавтоматика
 *     [2] => Ирсэт
 *     [3] => Ирсэт
 *     [4] => Системсервис
 *     [5] => Системсервис
 *   )
 *   [3] => Array (
 *     [0] => Технические характеристики
 *     [1] => http://www.host.ru/catalog/product/235497
 *     [2] => http://www.host.ru/catalog/product/19056
 *     [3] => http://www.host.ru/catalog/product/19053
 *     [4] => http://www.host.ru/catalog/product/19305
 *     [5] => http://www.host.ru/catalog/product/19128
 *   )
 *   [4] => Array (
 *     [0] => Краткое описание
 *     [1] => Uпит. 9-28В, 400mA, 95дБ, Tокр. среды -55°…+85°С, IP68
 *     [2] => ..........
 *     [3] => ..........
 *     [4] => ..........
 *     [5] => ..........
 *   )
 *   [5] => Array (
 *     [0] => Категория взрывозащиты
 *     [1] => нет
 *     [2] => да
 *     [3] => да
 *     [4] =>
 *     [5] =>
 *   )
 *   [6] => Array (
 *     [0] => Напряжение питания, В
 *     [1] => постоянное 12
 *     [2] => постоянное 24
 *     [3] => постоянное 12
 *     [4] => постоянное 24
 *     [5] => постоянное 12
 *   )
 * )
 *
 * $units = Array (
 *     0 => '-',
 *     1 => 'шт',
 *     2 => 'компл',
 *     3 => 'упак',
 *     4 => 'метр',
 *     5 => 'пара',
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
        <h1>Сравнение товаров</h1>
        <?php if (!empty($name)): ?>
            <h2>Функционал: <?php echo $name; ?></h2>
        <?php endif; ?>
    </div>

    <?php if (!empty($products)): // отложенные для сравнения товары ?>
        <a href="<?php echo $clearCompareURL; ?>">
            <i class="fa fa-trash-o"></i>&nbsp; <span>Очистить список сравнения</span>
        </a>
        <div class="table-responsive">
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
                        <div class="product-table-remove">
                            <form action="<?php echo $product['action']['compare']; ?>" method="post" class="remove-compare-form">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <input type="hidden" name="return" value="compare" />
                                <input type="submit" name="submit" value="Удалить из сравнения" title="Удалить из сравнения" class="selected" />
                            </form>
                        </div>
                    </div>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php foreach($params as $i => $row): ?>
                <tr>
                <?php foreach($row as $j => $cell): ?>
                        <td>
                            <?php
                                if ($i == 3 && $j > 0) { // технические характеристики
                                    if (!empty($cell)) {
                                        echo '<a href="' . $cell . '" class="zoom fancybox.ajax" rel="techdata">смотреть</a>';
                                    }
                                    continue;
                                }
                                if ($i == 4 && $j > 0) { // краткое описание
                                    echo '<span>показать</span>';
                                    echo '<span>' . $cell . '</span>';
                                    continue;
                                }
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
        </div>
    <?php else: ?>
        <p>Нет товаров для сравнения</p>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/compare/center.php -->
