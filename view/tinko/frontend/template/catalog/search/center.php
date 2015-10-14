<?php
/**
 * Результаты поиска по каталогу, общедоступная часть сайта,
 * файл view/example/frontend/template/catalog/search/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $query - поисковый запрос
 * $thisPageUrl - URL этой страницы
 * $results - массив результатов поиска
 * $units - массив единиц измерения товара
 * $pager - постраничная навигация
 *
 * $results = Array (
 *   [0] => Array (
 *     [id] => 5014
 *     [code] => 005014
 *     [name] => ИП 212-43
 *     [title] => Извещатель пожарный дымовой оптико-электронный автономный
 *     [image] => 2/b/2bf3499a9915c614a2292e3686537a8b.jpg
 *     [price] => 1000.48
 *     [shortdescr] => Дымовой автономный, сирена встроенная 90 дБ, питание 4 батарейки ААА (в комплекте)
 *     [unit] => 1
 *     [relevance] => 1.30
 *     [ctg_id] => 2
 *     [ctg_name] => Извещатели пожарные
 *     [mkr_id] => 5
 *     [mkr_name] => Болид
 *     [url] => Array (
 *       [product] => /catalog/product/37
 *       [maker] => /catalog/maker/5
 *       [image] => /files/catalog/products/small/nophoto.jpg
 *     ))
 *   )
 *   [1] => Array (
 *     .....
 *   )
 * )
 *
 * $units = Array (
 *   0 => 'руб.',
 *   1 => 'руб./шт.',
 *   2 => 'руб./компл.',
 *   3 => 'руб./упак.',
 *   4 => 'руб./метр',
 *   5 => 'руб./пара',
 * )
 */

defined('ZCMS') or die('Access denied');

$units = Array (
    0 => 'руб',
    1 => 'руб/шт',
    2 => 'руб/компл',
    3 => 'руб/упак',
    4 => 'руб/метр',
    5 => 'руб/пара',
);
?>

<!-- Начало шаблона view/example/frontend/template/catalog/search/center.php -->

<?php if (!empty($breadcrumbs)): ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Поиск по каталогу</h1>

<div id="center-search-form">
    <form action="<?php echo $action; ?>" method="post">
        <input type="text" name="query" value="<?php echo !empty($query) ? htmlspecialchars($query) : ''; ?>" maxlength="64" />
        <input type="submit" name="submit" value="" />
    </form>

    <?php if (!empty($query)): ?>
        <?php if (!empty($results)): ?>
            <div class="products-list-line">
            <?php foreach($results as $product): ?>
                <div>
                    <div class="product-line-heading">
                        <h2><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h2>
                        <?php if (!empty($product['title'])): ?>
                            <h3><?php echo $product['title']; ?></h3>
                        <?php endif; ?>
                    </div>
                    <div class="product-line-image">
                        <a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
                    </div>
                    <div class="product-line-info">
                        <div>
                            <span>Цена:</span>
                            <span><strong><?php echo number_format($product['price'], 2, '.', ''); ?></strong> <?php echo $units[$product['unit']]; ?></span>
                        </div>
                        <div>
                            <span>Код:</span>
                            <span><strong><?php echo $product['code']; ?></strong></span>
                        </div>
                        <div>
                            <span>Производитель:</span>
                            <span><a href="<?php echo $product['url']['maker']; ?>"><?php echo $product['mkr_name']; ?></a></span>
                        </div>
                    </div>
                    <div class="product-line-basket">
                        <form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                            <input type="text" name="count" value="1" size="5" />
                            <input type="hidden" name="return" value="search" />
                            <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>" />
                            <?php if ($page > 1): ?>
                                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                            <?php endif; ?>
                            <input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
                        </form>
                        <form action="<?php echo $product['action']['wished']; ?>" method="post" class="add-wished-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                            <input type="hidden" name="return" value="search" />
                            <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>" />
                            <?php if ($page > 1): ?>
                                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                            <?php endif; ?>
                            <input type="submit" name="submit" value="Отложить" title="Добавить в отложенные" />
                        </form>
                        <form action="<?php echo $product['action']['compared']; ?>" method="post" class="add-compared-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                            <input type="hidden" name="return" value="search" />
                            <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>" />
                            <?php if ($page > 1): ?>
                                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                            <?php endif; ?>
                            <input type="submit" name="submit" value="К сравнению" title="Добавить к сравнению" />
                        </form>
                    </div>
                    <div class="product-line-descr"><?php echo $product['shortdescr']; ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>По Вашему запросу ничего не найдено.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>" class="first-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['prev'] != 1) ? '/page/'.$pager['prev'] : ''; ?>" class="prev-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left) : ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($left != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

        <li>
            <span><?php echo $pager['current']; // текущая страница ?></span>
        </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right) : ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>" class="next-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>" class="last-page"></a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/catalog/search/center.php -->


