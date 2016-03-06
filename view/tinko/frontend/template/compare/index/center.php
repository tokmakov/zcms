<?php
/**
 * Список товаров, отложенных для сравнения посетителем сайта,
 * файл view/example/frontend/template/compare/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $thisPageUrl - URL этой страницы
 * $tablePageUrl - URL ссылки на таблицу сравнения
 * $name - наимнование функциональной группы
 * $compareProducts - массив отложенных для сравнения товаров
 * $units - массив единиц измерения товара
 *
 * $compareProducts = Array (
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
        <a href="<?php echo $tablePageUrl; ?>"><i class="fa fa-table"></i>&nbsp; <span>Таблица сравнения</span></a>
        <h1>Сравнение товаров</h1>
        <?php if (!empty($name)): ?>
            <h2>Функционал: <?php echo $name; ?></h2>
        <?php endif; ?>
    </div>

    <div class="product-list-line">
    <?php if (!empty($compareProducts)): // отложенные для сравнения товары ?>
        <?php foreach($compareProducts as $product): ?>
            <div>
                <div class="product-list-added">
                    <?php echo $product['date']; ?>
                    <?php echo $product['time']; ?>
                </div>
                <div class="product-list-heading">
                    <h2><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h2>
                    <?php if (!empty($product['title'])): ?>
                        <h3><?php echo $product['title']; ?></h3>
                    <?php endif; ?>
                </div>
                <div class="product-list-image">
                    <a href="<?php echo $product['url']['product']; ?>">
                        <?php if ($product['hit']): ?><span class="hit-product">Лидер продаж</span><?php endif; ?>
                        <?php if ($product['new']): ?><span class="new-product">Новинка</span><?php endif; ?>
                        <img src="<?php echo $product['url']['image']; ?>" alt="" />
                    </a>
                </div>
                <div class="product-list-info">
                    <div>
                        <span>Цена, <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?></span>
                        <span>
                            <span><strong><?php echo number_format($product['price'], 2, '.', ' '); ?></strong><span>розничная</span></span>
                            <span><strong><?php echo number_format($product['price2'], 2, '.', ' '); ?></strong><span>мелкий опт</span></span>
                            <span><strong><?php echo number_format($product['price3'], 2, '.', ' '); ?></strong><span>оптовая</span></span>
                        </span>
                    </div>
                    <div>
                        <span>Код</span>
                        <span><?php echo $product['code']; ?></span>
                    </div>
                    <div>
                        <span>Производитель</span>
                        <span><a href="<?php echo $product['url']['maker']; ?>"><?php echo $product['mkr_name']; ?></a></span>
                    </div>
                </div>
                <div class="product-list-basket">
                    <form action="<?php echo $product['action']['basket']; /* добавить в корзину */ ?>" method="post" class="add-basket-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="text" name="count" value="1" size="5" />
                        <input type="hidden" name="return" value="compare" />
                        <input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
                    </form>
                    <form action="<?php echo $product['action']['wished']; /* добавить в избранное */ ?>" method="post" class="add-wished-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="hidden" name="return" value="compare" />
                        <input type="submit" name="submit" value="В избранное" title="Добавить в избранное" />
                    </form>
                    <form action="<?php echo $product['action']['compare']; /* удалить из сравнения */ ?>" method="post" class="remove-compare-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="submit" name="submit" value="Удалить"  title="Удалить из сравнения"  class="selected" />
                    </form>
                </div>
                <?php if (!empty($product['techdata'])): ?>
                    <div class="product-list-techdata">
                        <div>
                            <span>Технические характеристики</span>
                            <span><span>показать</span></span>
                        </div>
                        <div>
                            <table>
                                <?php foreach($product['techdata'] as $item): ?>
                                    <tr>
                                        <td><?php echo $item[0]; ?></td>
                                        <td><?php echo $item[1]; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="product-list-descr"><?php echo $product['shortdescr']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Нет товаров для сравнения</p>
    <?php endif; ?>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/compare/center.php -->
