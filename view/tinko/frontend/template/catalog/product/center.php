<?php
/**
 * Страница товара,
 * файл view/example/frontend/template/catalog/product/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $id - уникальный идентификатор товара
 * $breadcrumbs - хлебные крошки
 * $breadcrumbs2 - хлебные крошки
 * $thisPageUrl - URL этой страницы
 * $id - уникальный идентификатор товара
 * $name - заголовок h1 (торговое наименование товара)
 * $title - заголовок h2 (функциональное наименование товара)
 * $ctg_id - уникальный идентификатор родительской категории
 * $price - цена
 * $unit - единица измерения
 * $units - массив единиц измерения товара
 * $group - информация о функциональной группе
 * $maker - информация о производителе
 * $new - новый товар?
 * $hit - лидер продаж?
 * $shortdescr - краткое описание
 * $image - фото товара
 * $purpose - назначение изделия
 * $techdata - технические характеристики
 * $features - особенности изделия
 * $complect - комплектация
 * $equipment - доп. оборудование
 * $padding - доп. информация
 * $docs - файлы документации
 * $certs - файлы сертификатов
 * $action - атирибут action тега form формы для добавления товара
 * в корзину, в список отложенных, в список сравнения
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
 * $maker = Array (
 *   [id] => 22
 *   [name] => РЗМКП
 *   [url] => http://www.host.ru/catalog/maker/22
 * )
 *
 * $group = Array (
 *   [id] => 22
 *   [name] => Извещатель охранный
 *   [url] => http://www.host.ru/catalog/group/76
 * )
 *
 * $image = Array (
 *   [medium] => http://www.host.ru/files/catalog/imgs/medium/5/9/592730e9271a3c0f6e88573ed68695fb.jpg
 *   [big] => http://www.host.ru/files/catalog/imgs/big/5/9/592730e9271a3c0f6e88573ed68695fb.jpg
 * )
 *
 * $action = Array (
 *   [basket] => http://www.host.ru/basket/addprd
 *   [wished] => http://www.host.ru/wished/addprd
 *   [compare] => http://www.host.ru/compare/addprd
 * )
 *
 * $docs = Array (
 *   [0] => Array (
 *     [id] => 36
 *     [title] => Руководство по эксплуатации
 *     [type] => pdf
 *     [url] => http://www.host.ru/files/catalog/docs/3/9/396785fd3a56127cf43d2bf14105dd91.pdf
 *   )
 *   [1] => Array (
 *     [id] => 37
 *     [title] => Схема подключения
 *     [type] => pdf
 *     [url] => http://www.host.ru/files/catalog/docs/5/1/51e3f15b73f3263f6fe16c0162d47c7c.pdf
 *   )
 * )
 *
 * $certs = Array (
 *   [0] => Array (
 *     [title] => Декларация о соответствии
 *     [count] => 2
 *     [files] => Array (
 *       [0] => Array (
 *         [name] => 1/9/19a23671690e26cbaaacd536914a8ca4.jpg
 *         [url] => //www.host2.ru/files/catalog/cert/1/9/19a23671690e26cbaaacd536914a8ca4.jpg
 *       )
 *       [1] => Array (
 *         [name] => 1/9/19a23671690e26cbaaacd536914a8ca41.jpg
 *         [url] => //www.host2.ru/files/catalog/cert/1/9/19a23671690e26cbaaacd536914a8ca41.jpg
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [title] => Сертификат соответствия
 *     [count] => 1
 *     [files] => Array (
 *       [0] => Array (
 *         [name] => f/0/f0054da16a163660ec28dd48fad9fa98.jpg
 *         [url] => //www.host2.ru/files/catalog/cert/f/0/f0054da16a163660ec28dd48fad9fa98.jpg
 *       )
 *     )
 *   )
 *   [2] => Array (
 *     ..........
 *   )
 * )
 *
 * $recommendedProducts = Array (
 *   [0] => Array (
 *     [id] => 65498
 *     [code] => 065498
 *     [name] => MB-RIO4/16
 *     [title] => Модуль релейных выходов
 *     [price] => 5197.00
 *     [unit] => 1
 *     [shortdescr] => Модуль релейных выходов и оптоизолированных входов для PowerVN4 Pro...
 *     [image] => http://www.host.ru/files/catalog/imgs/small/2/3/233e1eb1bc716bf4bc33525cefaf67fc.jpg
 *     [ctg_id] => 598
 *     [ctg_name] => Компоненты и ПО
 *     [mkr_id] => 558
 *     [mkr_name] => VideoNet
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/65498
 *       [maker] => http://www.host.ru/catalog/maker/558
 *       [image] => http://www.host.ru/files/catalog/products/small/2/3/233e1eb1bc716bf4bc33525cefaf67fc.jpg
 *     )
 *     [action] => http://www.host.ru/basket/addprd
 *   )
 *   [1] => Array (
 *     ..........
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/product/center.php -->

<?php if (!empty($breadcrumbs)): ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
        <?php if (!empty($breadcrumbs2)): ?>
            <div>
                <?php foreach ($breadcrumbs2 as $item): ?>
                    <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>



<div id="product-item">

    <div class="product-item-heading">
        <h1><?php echo $name; ?></h1>
        <?php if (!empty($title)): ?>
            <h2><?php echo $title; ?></h2>
        <?php endif; ?>
    </div>

    <div class="product-item-main">
        <div class="product-item-image">
            <a href="<?php echo $image['big']; ?>" class="zoom" title="<?php echo htmlspecialchars($name); ?>">
                <?php if ($hit): ?><span class="hit-product">Лидер продаж</span><?php endif; ?>
                <?php if ($new): ?><span class="new-product">Новинка</span><?php endif; ?>
                <img src="<?php echo $image['medium']; ?>" alt="<?php echo htmlspecialchars($name); ?>" />
            </a>
        </div>
        <div class="product-item-info">
            <div>
                <span>Цена, <i class="fa fa-rub"></i>/<?php echo $units[$unit]; ?></span>
                <span>
                    <span>
                        <span><?php echo number_format($price, 2, '.', ' '); ?></span> <span>розничная</span>
                    </span>
                    <span>
                        <span><?php echo number_format($price2, 2, '.', ' '); ?></span> <span>мелкий опт</span>
                    </span>
                    <span>
                        <span><?php echo number_format($price3, 2, '.', ' '); ?></span> <span>оптовая</span>
                    </span>
                </span>
            </div>
            <div><span>Код</span> <span><?php echo $code; ?></span></div>
            <div><span>Производитель</span> <span><a href="<?php echo $maker['url']; ?>"><?php echo $maker['name']; ?></a></span></div>
            <div><span>Функционал</span> <span><a href="<?php echo $group['url']; ?>"><?php echo $group['name']; ?></a></span></div>
        </div>
        <div class="product-item-basket">
            <form action="<?php echo $action['basket']; ?>" method="post" class="add-basket-form">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>" />
                <input type="text" name="count" size="5" value="1" />
                <input type="hidden" name="return" value="product" />
                <input type="hidden" name="return_prd_id" value="<?php echo $id; ?>" />
                <input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
            </form>
            <form action="<?php echo $action['wished']; ?>" method="post" class="add-wished-form">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="return" value="product" />
                <input type="hidden" name="return_prd_id" value="<?php echo $id; ?>" />
                <input type="submit" name="submit" value="В избранное" title="Добавить в избранное" />
            </form>
            <form action="<?php echo $action['compare']; ?>" method="post" class="add-compare-form" data-group="<?php echo $group['id']; ?>">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="return" value="product" />
                <input type="hidden" name="return_prd_id" value="<?php echo $id; ?>" />
                <input type="submit" name="submit" value="К сравнению" title="Добавить к сравнению" />
            </form>
            <div></div>
        </div>
        <div class="product-item-descr"><?php echo $shortdescr; ?></div>
    </div>

    <div class="product-item-others">
        <?php if (!empty($purpose)): ?>
            <div class="center-block">
                <div><h3>Назначение</h3></div>
                <div><?php echo nl2br($purpose); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($techdata)): ?>
            <div class="center-block">
                <div><h3>Технические характеристики</h3></div>
                <div class="no-padding">
                    <table>
                    <?php foreach($techdata as $item): ?>
                        <tr>
                            <td><?php echo $item[0]; ?></td>
                            <td><?php echo $item[1]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($features)): ?>
            <div class="center-block">
                <div><h3>Особенности</h3></div>
                <div><?php echo nl2br($features); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($complect)): ?>
            <div class="center-block">
                <div><h3>Комплектация</h3></div>
                <div><?php echo nl2br($complect); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($equipment)): ?>
            <div class="center-block">
                <div><h3>Дополнительное оборудование</h3></div>
                <div><?php echo nl2br($equipment); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($padding)): ?>
            <div class="center-block">
                <div><h3>Дополнительная информация</h3></div>
                <div><?php echo nl2br($padding); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($docs)): ?>
            <div class="center-block">
                <div><h3>Документация</h3></div>
                <div>
                    <ul>
                    <?php foreach ($docs as $doc): ?>
                        <li><a href="<?php echo $doc['url']; ?>" target="_blank"><?php echo $doc['title']; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($certs)): ?>
            <div class="center-block">
                <div><h3>Сертификаты</h3></div>
                <div>
                    <?php foreach ($certs as $cert): ?>
                        <?php foreach ($cert['files'] as $file): ?>
                            <span>
                                <a href="<?php echo $file['url']; ?>" class="zoom" rel="certs">
                                    <img src="<?php echo $file['url']; ?>" alt="<?php echo str_replace('"', '', $cert['title']); ?>" />
                                </a>
                                <a href="/files/catalog/cert/download.php?file=<?php echo rawurldecode($file['name']); ?>">
                                    <i class="fa fa-download" aria-hidden="true"></i> сохранить
                                </a>
                            </span>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($recommendedProducts)): // рекомендованные товары ?>
            <div class="center-block">
                <div><h3>С этим товаром покупают</h3></div>
                <div class="no-padding">
                    <div class="product-list-upsell">
                        <?php foreach($recommendedProducts as $product): ?>
                            <div><div>
                                <div class="product-upsell-heading">
                                    <h4><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h4>
                                </div>
                                <div class="product-upsell-image">
                                    <a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
                                </div>
                                <div class="product-upsell-price">
                                    <span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?>
                                </div>
                                <div class="product-upsell-basket">
                                    <form action="<?php echo $product['action']; ?>" method="post" class="add-basket-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                        <input type="hidden" name="return" value="product" />
                                        <input type="hidden" name="return_prd_id" value="<?php echo $id; ?>" />
                                        <input type="submit" name="submit" value="В корзину" />
                                    </form>
                                </div>
                            </div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if (!empty($likedProducts)): // похожие товары ?>
            <div class="center-block">
                <div><h3>Похожие товары</h3></div>
                <div class="no-padding">
                    <div class="product-list-upsell">
                        <?php foreach($likedProducts as $product): ?>
                            <div><div>
                                <div class="product-upsell-heading">
                                    <h4><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h4>
                                </div>
                                <div class="product-upsell-image">
                                    <a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
                                </div>
                                <div class="product-upsell-price">
                                    <span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?>
                                </div>
                                <div class="product-upsell-basket">
                                    <form action="<?php echo $product['action']; ?>" method="post" class="add-basket-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                        <input type="hidden" name="return" value="product" />
                                        <input type="hidden" name="return_prd_id" value="<?php echo $id; ?>" />
                                        <input type="submit" name="submit" value="В корзину" />
                                    </form>
                                </div>
                            </div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>

</div>

<!-- Конец шаблона view/example/frontend/template/catalog/product/center.php -->
