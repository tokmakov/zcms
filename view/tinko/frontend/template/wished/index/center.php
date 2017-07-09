<?php
/**
 * Список товаров, отложенных посетителем сайта,
 * файл view/example/frontend/template/wished/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $view - представление списка товаров
 * $thisPageUrl - URL этой страницы
 * $wishedProducts - массив отложенных товаров
 * $units - массив единиц измерения товара
 * $pager - постраничная навигация
 * $page - текущая страница
 *
 * $wishedProducts = Array (
 *   [0] => Array (
 *     [id] => 37
 *     [code] => 001007
 *     [name] => ИП 212
 *     [title] => Извещатель пожарный дымовой
 *     [price] => 123.45
 *     [shortdescr] =>
 *     [ctg_id] => 2
 *     [ctg_name] => Извещатели пожарные
 *     [mkr_id] => 5
 *     [mkr_name] => Болид
 *     [grp_id] => 5
 *     [date] => 28.11.2014
 *     [time] => 11:50:36
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/37
 *       [maker] => http://www.host.ru/catalog/maker/5
 *       [image] => http://www.host.ru/files/catalog/imgs/small/8/7/8710c4a3ed9f660b5549092b5378c42c.jpg
 *     )
 *     [action] => Array (
 *       [basket] => http://www.host.ru/basket/addprd
 *       [compare] => http://www.host.ru/compare/addprd
 *       [wished] => http://www.host.ru/wished/rmvprd
 *       [comment] => http://www.host.ru/wished/comment
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
 *     0 => '-',
 *     1 => 'шт',
 *     2 => 'компл',
 *     3 => 'упак',
 *     4 => 'метр',
 *     5 => 'пара',
 * )
 *
 * $pager = Array (
 *   [first] => Array (
 *     [num] => 1
 *     [url] => http://www.host.ru/wished
 *   )
 *   [prev] => Array (
 *     [num] => 2
 *     [url] => http://www.host.ru/wished/page/2
 *   )
 *   [current] => Array (
 *     [num] => 3
 *     [url] => http://www.host.ru/wished/page/3
 *   )
 *   [last] => Array (
 *     [num] => 32
 *     [url] => http://www.host.ru/wished/page/12
 *   )
 *   [next] => Array (
 *     [num] => 4
 *     [url] => http://www.host.ru/wished/page/4
 *   )
 *   [left] => Array (
 *     [0] => Array (
 *       [num] => 1
 *       [url] => http://www.host.ru/wished
 *     )
 *     [1] => Array (
 *       [num] => 2
 *       [url] => http://www.host.ru/wished/page/2
 *     )
 *   )
 *   [right] => Array (
 *     [0] => Array (
 *       [num] => 4
 *       [url] => http://www.host.ru/wished/page/4
 *     )
 *     [1] => Array (
 *       [num] => 5
 *       [url] => http://www.host.ru/wished/page/5
 *     )
 *   )
 * )
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/wished/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<span id="switch-line-grid">
    <i class="fa fa-bars<?php echo ($view == 'line') ? ' selected' : ''; ?>"></i>
    <i class="fa fa-th-large<?php echo ($view == 'grid') ? ' selected' : ''; ?>"></i>
</span>
<h1>Избранное</h1>

<?php if (!empty($wishedProducts)): // отложенные товары ?>
    <div class="product-list-<?php echo $view; ?>">
    <?php foreach($wishedProducts as $product): ?>
        <div>
            <div class="product-list-added">
                <?php echo $product['date']; ?>
                <?php echo $product['time']; ?>
            </div>
            <div class="product-list-heading">
                <h2><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h2>
                <?php if ( ! empty($product['title'])): ?>
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
                <form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                    <input type="text" name="count" value="1" size="5" />
                    <input type="hidden" name="return" value="wished" />
                    <?php if ($page > 1): ?>
                        <input type="hidden" name="page" value="<?php echo $page; ?>" />
                    <?php endif; ?>
                    <input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
                </form>
                <form action="<?php echo $product['action']['wished']; ?>" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                    <input type="hidden" name="return" value="wished" />
                    <?php if ($page > 1): ?>
                        <input type="hidden" name="page" value="<?php echo $page; ?>" />
                    <?php endif; ?>
                    <input type="submit" name="submit" value="Удалить" title="Удалить из избранного" class="selected" />
                </form>
                <form action="<?php echo $product['action']['compare']; ?>" method="post" class="add-compare-form" data-group="<?php echo $product['grp_id']; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                    <input type="hidden" name="return" value="wished" />
                    <?php if ($page > 1): ?>
                        <input type="hidden" name="page" value="<?php echo $page; ?>" />
                    <?php endif; ?>
                    <input type="submit" name="submit" value="К сравнению" title="Добавить к сравнению" />
                </form>
            </div>
            <div class="product-list-descr"><?php echo $product['shortdescr']; ?></div>
            <div class="product-list-comment">
                <form action="<?php echo $product['action']['comment']; ?>" method="post">
                    <div>
                        <span>Комментарий</span>
                        <input type="submit" name="submit" value="Сохранить" title="Сохранить комментарий" />
                    </div>
                    <div>
                        <textarea name="comment" maxlength="250" placeholder="Ваш комментарий, чтобы не забыть..."><?php echo htmlspecialchars($product['comment']); ?></textarea>
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <?php if ($page > 1): ?>
                            <input type="hidden" name="page" value="<?php echo $page; ?>" />
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Нет отложенных товаров</p>
<?php endif; ?>

<?php if ( ! empty($pager)): /* постраничная навигация */ ?>
    <ul class="pager"> <!-- постраничная навигация -->
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $pager['first']['url']; /* первая страница */ ?>" class="first-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $pager['prev']['url']; /* предыдущая страница */ ?>" class="prev-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left) : ?>
            <li>
                <a href="<?php echo $left['url']; ?>"><?php echo $left['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

        <li>
            <span><?php echo $pager['current']['num']; /* текущая страница */ ?></span>
        </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right) : ?>
            <li>
                <a href="<?php echo $right['url']; ?>"><?php echo $right['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $pager['next']['url']; /* следующая страница */ ?>" class="next-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $pager['last']['url']; /* последняя страница */ ?>" class="last-page"></a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/wished/center.php -->
