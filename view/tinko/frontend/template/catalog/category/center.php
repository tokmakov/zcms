<?php
/**
 * Категория каталога, список дочерних категорий + список товаров категории,
 * общедоступная часть сайта, файл view/example/frontend/template/catalog/category/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории
 * $thisPageUrl - URL этой страницы
 * $childCategories - массив дочерних категорий
 * $action - атрибут action тега форм
 * $group - id выбранной функциональной группы или ноль
 * $maker - id выбранного производителя или ноль
 * $hit - показывать только лидеров продаж?
 * $countHit - количество лидеров продаж
 * $new - показывать только новинки?
 * $countNew - количество новинок
 * $param - массив выбранных параметров подбора
 * $groups - массив функциональных групп
 * $makers - массив производителей
 * $params - массив всех параметров подбора
 * $sort - выбранная сортировка
 * $sortorders - массив всех вариантов сортировки
 * $units - массив единиц измерения товара
 * $products - массив товаров категории
 * $clearFilterURL - URL ссылки для сборса фильтра
 * $pager - постраничная навигация
 * $page - текущая страница
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 37
 *     [code] => 001007
 *     [name] => ИП 212
 *     [title] => Извещатель пожарный дымовой
 *     [price] => 123.45
 *     [shortdescr] =>
 *     [hit] = 1
 *     [new] = 0
 *     [ctg_id] => 2
 *     [ctg_name] => Извещатели пожарные
 *     [mkr_id] => 5
 *     [mkr_name] => Болид
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/37
 *       [maker] => http://www.host.ru/catalog/maker/5
 *       [image] => http://www.host.ru/files/catalog/products/small/nophoto.jpg
 *     )
 *     [action] => Array (
 *       [basket] => http://www.host.ru/basket/addprd/37
 *       [wished] => http://www.host.ru/wished/addprd/37
 *       [compare] => http://www.host.ru/compare/addprd/37
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
 * $groups = Array (
 *   [0] => Array (
 *     [id] => 11
 *     [name] => Видеокамеры корпусные
 *     [count] => 12
 *   )
 *   [1] => Array (
 *     [id] => 23
 *     [name] => Видеокамеры купольные
 *     [count] => 14
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $params = Array (
 *   [0] => Array (
 *     [id] => 5
 *     [name] => Напряжение питания
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 7
 *         [name] => 12 Вольт
 *         [count] => 3
 *       )
 *       [1] => Array (
 *         [id] => 9
 *         [name] => 24 Вольт
 *         [count] => 5
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 8
 *     [name] => Встроенная ИК подсветка
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 11
 *         [name] => есть
 *         [count] => 4
 *       )
 *       [1] => Array (
 *         [id] => 14
 *         [name] => нет
 *         [count] => 6
 *       )
 *     )
 *   )
 * )
 *
 * ключ элемента массива - id параметра (например, «Напряжение питания»)
 * значение элемента массива - id значения (например, «12 Вольт»)
 * $param = Array (
 *   [5] => 7
 *   [8] => 11
 * )
 *
 * $makers = Array (
 *   [0] => Array (
 *     [id] => 380
 *     [name] => EverFocus
 *     [count] => 12
 *   )
 *   [1] => Array (
 *     [id] => 384
 *     [name] => MicroDigital
 *     [count] => 14
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $sortorders = Array (
 *   [0] => Array (
 *     [url] => http://www.host.ru/catalog/category/1
 *     [name] => без сортировки
 *   )
 *   [1] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/1
 *     [name] => цена, возр.
 *   )
 *   [2] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/2
 *     [name] => цена, убыв.
 *   )
 *   [3] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/3
 *     [name] => название, возр.
 *   )
 *   [4] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/4
 *     [name] => название, убыв.
 *   )
 *   [5] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/5
 *     [name] => код, возр.
 *   )
 *   [6] => Array (
 *     [url] => http://www.host.ru/catalog/category/1/sort/6
 *     [name] => код, убыв.
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
 *
 * $pager = Array (
 *   [first] => Array (
 *     [num] => 1
 *     [url] => http://www.host.ru/catalog/category/185
 *   )
 *   [prev] => Array (
 *     [num] => 2
 *     [url] => http://www.host.ru/catalog/category/185/page/2
 *   )
 *   [current] => Array (
 *     [num] => 3
 *     [url] => http://www.host.ru/catalog/category/185/page/3
 *   )
 *   [last] => Array (
 *     [num] => 32
 *     [url] => http://www.host.ru/catalog/category/185/page/32
 *   )
 *   [next] => Array (
 *     [num] => 4
 *     [url] => http://www.host.ru/catalog/category/185/page/4
 *   )
 *   [left] => Array (
 *     [0] => Array (
 *       [num] => 1
 *       [url] => http://www.host.ru/catalog/category/185
 *     )
 *     [1] => Array (
 *       [num] => 2
 *       [url] => http://www.host.ru/catalog/category/185/page/2
 *     )
 *   )
 *   [right] => Array (
 *     [0] => Array (
 *       [num] => 4
 *       [url] => http://www.host.ru/catalog/category/185/page/4
 *     )
 *     [1] => Array (
 *       [num] => 5
 *       [url] => http://www.host.ru/catalog/category/185/page/5
 *     )
 *   )
 * )
 *
 */

defined('ZCMS') or die('Access denied');

/*
 * Варианты сортировки:
 * 0 - по умолчанию,
 * 1 - по цене, по возрастанию
 * 2 - по цене, по убыванию
 * 3 - по наименованию, по возрастанию
 * 4 - по наименованию, по убыванию
 * 5 - по коду, по возрастанию
 * 6 - по коду, по убыванию
 * Можно переопределить текст по умолчанию:
 */
for ($i = 0; $i <= 6; $i++) {
    switch ($i) {
        case 0: $text = 'прайс-лист'; $class = '';               break;
        case 1: $text = 'цена';       $class = 'sort-asc-blue';  break;
        case 2: $text = 'цена';       $class = 'sort-desc-blue'; break;
        case 3: $text = 'название';   $class = 'sort-asc-blue';  break;
        case 4: $text = 'название';   $class = 'sort-desc-blue'; break;
        case 5: $text = 'код';        $class = 'sort-asc-blue';  break;
        case 6: $text = 'код';        $class = 'sort-desc-blue'; break;
    }
    if ($sort && $i == $sort) {
        $class = str_replace('blue', 'orange', $class);
    }
    $sortorders[$i]['name'] = $text;
    $sortorders[$i]['class'] = $class;
}

?>

<!-- Начало шаблона view/example/frontend/template/catalog/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<?php if (!empty($childCategories)): // дочерние категории ?>
    <div id="category-childs">
        <div>
            <span>Категории</span>
            <span><span>скрыть</span></span>
        </div>
        <div>
            <ul>
            <?php
                $border = 0;
                $divide = 0;
                $count = count($childCategories);
                if ($count > 3) {
                    $divide = ceil($count/2);
                    $border = $count%2;
                }
            ?>
            <?php foreach ($childCategories as $key => $item): ?>
                <li<?php if (($key == $count-1) && $border) echo ' class="category-last-child-border"'; ?>>
                    <?php if ($item['count']): // есть товары в категории? ?>
                        <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                    <?php else: ?>
                        <span><span><?php echo $item['name']; ?></span> <span>0</span></span>
                    <?php endif; ?>
                </li>
                <?php if ($divide && $divide == ($key+1)): ?>
                    </ul>
                    <ul>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($products) && empty($group) && empty($maker) && empty($hit) && empty($new)): ?>
    <p>Нет товаров в этой категории.</p>
    <?php return; ?>
<?php endif; ?>

<div id="category-filters">
    <div>
        <span>
            Фильтр
            <a href="<?php echo $clearFilterURL; ?>"<?php if ($group || $maker || $hit || $new) echo ' class="show-clear-filter"'; ?>>
                сбросить
            </a>
        </span>
        <span>
            <span>скрыть</span>
        </span>
    </div>
    <div>
        <form action="<?php echo $action; ?>" method="post">
            <div>
                <div>
                    <div>
                        <span>Функциональное назначение</span>
                    </div>
                    <div>
                        <span>
                        <select name="group">
                            <option value="0">Выберите</option>
                            <?php foreach ($groups as $item): ?>
                                <option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $group) ? ' selected="selected"' : ''; ?><?php echo (!$item['count']) ? ' class="empty-option"' : ''; ?>><?php echo $item['name']; ?> [<?php echo $item['count']; ?>]</option>
                            <?php endforeach; ?>
                        </select>
                        </span>
                    </div>
                </div>
                <div>
                    <div>
                        <span>Производитель</span>
                    </div>
                    <div>
                        <span>
                        <select name="maker">
                            <option value="0">Выберите</option>
                            <?php foreach ($makers as $item): ?>
                                <option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $maker) ? ' selected="selected"' : ''; ?><?php echo (!$item['count']) ? ' class="empty-option"' : ''; ?>><?php echo $item['name']; ?> [<?php echo $item['count']; ?>]</option>
                            <?php endforeach; ?>
                        </select>
                        </span>
                    </div>
                </div>
                <?php if (!empty($params)): ?>
                    <?php foreach ($params as $item): ?>
                        <?php $selected = false; ?>
                        <div>
                            <div>
                                <span><?php echo $item['name']; ?></span>
                            </div>
                            <div>
                                <span>
                                <select name="param[<?php echo $item['id']; ?>]">
                                    <option value="0">Выберите</option>
                                    <?php foreach ($item['values'] as $value): ?>
                                        <?php $selected = isset($param[$item['id']]) && $param[$item['id']] == $value['id']; ?>
                                        <option value="<?php echo $value['id']; ?>"<?php echo $selected ? ' selected="selected"' : ''; ?><?php echo (!$value['count']) ? ' class="empty-option"' : ''; ?>><?php echo htmlspecialchars($value['name']); ?> [<?php echo $value['count']; ?>]</option>
                                    <?php endforeach; ?>
                                </select>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div>
                    <div<?php echo empty($countHit) ? ' class="empty-checkbox"' : ''; ?>>
                        <label>
                            <input type="checkbox" name="hit"<?php echo $hit ? ' checked="checked"' : ''; ?> value="1" />
                            <span>Лидер продаж</span>
                        </label>
                    </div>
                    <div<?php echo empty($countNew) ? ' class="empty-checkbox"' : ''; ?>>
                        <label>
                            <input type="checkbox" name="new"<?php echo $new ? ' checked="checked"' : ''; ?> value="1" />
                            <span>Новинка</span>
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <?php if ($sort): ?>
                    <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
                <?php endif; ?>
                <input type="hidden" name="change" value="0" />
                <input type="submit" name="submit" value="Применить" />
            </div>
        </form>
    </div>
</div>

<?php if (empty($products)): ?>
    <div id="category-products">
        <p>По вашему запросу ничего не найдено.</p>
    </div>
    <?php return; ?>
<?php endif; ?>

<div id="category-products">

    <div id="sort-orders">
        <ul>
            <li>Сортировка</li>
            <?php foreach ($sortorders as $key => $value): ?>
                <li>
                    <?php if ($key == $sort): ?>
                        <span class="selected<?php echo (!empty($value['class'])) ? ' ' . $value['class'] : ''; ?>"><?php echo $value['name']; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $value['url']; ?>"<?php echo (!empty($value['class'])) ? ' class="' . $value['class'] . '"' : ''; ?>><span><?php echo $value['name']; ?></span></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="products-list-line">
        <?php
            if ( ! empty($param)) {
                $temp = array();
                foreach ($param as $key => $value) {
                    $temp[] = $key . '.' . $value;
                }
                if ( ! empty($temp)) {
                    $prm = implode('-', $temp);
                }
            }
        ?>
        <?php foreach ($products as $product): ?>
            <div>
                <div class="product-line-heading">
                    <h2><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h2>
                    <?php if (!empty($product['title'])): ?>
                        <h3><?php echo $product['title']; ?></h3>
                    <?php endif; ?>
                </div>
                <div class="product-line-image">
                    <a href="<?php echo $product['url']['product']; ?>">
                        <?php if ($product['hit']): ?><span class="hit-product">Лидер продаж</span><?php endif; ?>
                        <?php if ($product['new']): ?><span class="new-product">Новинка</span><?php endif; ?>
                        <img src="<?php echo $product['url']['image']; ?>" alt="" />
                    </a>
                </div>
                <div class="product-line-info">
                    <div>
                        <span>Цена, <?php echo $units[$product['unit']]; ?></span>
                        <span>
                            <span><strong><?php echo number_format($product['price'], 2, '.', ''); ?></strong><span>розничная</span></span>
                            <span><strong><?php echo number_format($product['price2'], 2, '.', ''); ?></strong><span>мелкий опт</span></span>
                            <span><strong><?php echo number_format($product['price3'], 2, '.', ''); ?></strong><span>оптовая</span></span>
                        </span>
                    </div>
                    <div>
                        <span>Код</span>
                        <span><?php echo $product['code']; ?></span>
                    </div>
                    <div>
                        <span>Производитель</span>
                        <span><a href="<?php echo $product['url']['maker']; ?>"<?php echo ($maker) ? ' class="selected"' : ''; ?>><?php echo $product['mkr_name']; ?></a></span>
                    </div>
                </div>
                <div class="product-line-basket">
                    <form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="text" name="count" value="1" size="5" />
                        <input type="hidden" name="return" value="category" />
                        <input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
                        <?php endif; ?>
                        <?php if ($maker): ?>
                            <input type="hidden" name="maker" value="<?php echo $maker; ?>" />
                        <?php endif; ?>
                        <?php if ($hit): ?>
                            <input type="hidden" name="hit" value="1" />
                        <?php endif; ?>
                        <?php if ($new): ?>
                            <input type="hidden" name="new" value="1" />
                        <?php endif; ?>
                        <?php if ( ! empty($prm)): ?>
                            <input type="hidden" name="param" value="<?php echo $prm; ?>" />
                        <?php endif; ?>
                        <?php if ($sort): ?>
                            <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
                        <?php endif; ?>
                        <?php if ($page > 1): ?>
                            <input type="hidden" name="page" value="<?php echo $page; ?>" />
                        <?php endif; ?>
                        <input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
                    </form>
                    <form action="<?php echo $product['action']['wished']; ?>" method="post" class="add-wished-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="hidden" name="return" value="category" />
                        <input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
                        <?php endif; ?>
                        <?php if ($maker): ?>
                            <input type="hidden" name="maker" value="<?php echo $maker; ?>" />
                        <?php endif; ?>
                        <?php if ($hit): ?>
                            <input type="hidden" name="hit" value="1" />
                        <?php endif; ?>
                        <?php if ($new): ?>
                            <input type="hidden" name="new" value="1" />
                        <?php endif; ?>
                        <?php if ( ! empty($prm)): ?>
                            <input type="hidden" name="param" value="<?php echo $prm; ?>" />
                        <?php endif; ?>
                        <?php if ($sort): ?>
                            <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
                        <?php endif; ?>
                        <?php if ($page > 1): ?>
                            <input type="hidden" name="page" value="<?php echo $page; ?>" />
                        <?php endif; ?>
                        <input type="submit" name="submit" value="В избранное" title="Добавить в избранное" />
                    </form>
                    <form action="<?php echo $product['action']['compare']; ?>" method="post" class="add-compare-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="hidden" name="return" value="category" />
                        <input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
                        <?php endif; ?>
                        <?php if ($maker): ?>
                            <input type="hidden" name="maker" value="<?php echo $maker; ?>" />
                        <?php endif; ?>
                        <?php if ($hit): ?>
                            <input type="hidden" name="hit" value="1" />
                        <?php endif; ?>
                        <?php if ($new): ?>
                            <input type="hidden" name="new" value="1" />
                        <?php endif; ?>
                        <?php if ( ! empty($prm)): ?>
                            <input type="hidden" name="param" value="<?php echo $prm; ?>" />
                        <?php endif; ?>
                        <?php if ($sort): ?>
                            <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
                        <?php endif; ?>
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

    <?php if (!empty($pager)): // постраничная навигация ?>
        <ul class="pager">
        <?php if (isset($pager['first'])): ?>
            <li>
                <a href="<?php echo $pager['first']['url']; ?>" class="first-page"></a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['prev'])): ?>
            <li>
                <a href="<?php echo $pager['prev']['url']; ?>" class="prev-page"></a>
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
                <span><?php echo $pager['current']['num']; // текущая страница ?></span>
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
                <a href="<?php echo $pager['next']['url']; ?>" class="next-page"></a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['last'])): ?>
            <li>
                <a href="<?php echo $pager['last']['url']; ?>" class="last-page"></a>
            </li>
        <?php endif; ?>
        </ul>
    <?php endif; ?>

</div>

<!-- Конец шаблона view/example/frontend/template/catalog/category/center.php -->
