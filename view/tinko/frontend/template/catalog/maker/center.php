<?php
/**
 * Список товаров выбранного производителя,
 * файл view/example/frontend/template/catalog/maker/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор производителя
 * $name - наименование производителя
 * $action - атрибут action тега form
 * $view - представление списка товаров
 * $group - id выбранной функциональной группы или ноль
 * $groups - массив функциональных групп
 * $param - массив выбранных параметров подбора
 * $params - массив всех параметров подбора
 * $hit - показывать только лидеров продаж?
 * $countHit - количество лидеров продаж
 * $new - показывать только новинки?
 * $countNew - количество новинок
 * $sort - выбранная сортировка
 * $sortorders - массив всех вариантов сортировки
 * $products - массив товаров производителя
 * $units - массив единиц измерения товара
 * $clearFilterURL - URL ссылки для сброса фильтра
 * $pager - постраничная навигация
 * $page - текущая страница
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 230524
 *     [code] => 230524
 *     [name] => AVP-453 (PAL)
 *     [title] => Видеопанель вызывная цветная
 *     [price] => 3159.00
 *     [price2] => 3059.00
 *     [price3] => 2959.00
 *     [unit] => 1
 *     [shortdescr] => Дверной блок, накладной, ЛС 4-х пров.; 420 Твл, ИК-подветка; -50…+50°С; 140х70х20 мм
 *     [ctg_id] => 844
 *     [ctg_name] => Видеопенели вызывные
 *     [grp_id] => 7
 *     [grp_name] => Видеопанель вызывная
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/230524
 *       [image] => http://www.host.ru/files/catalog/imgs/small/6/9/690535d0ce3fd37599827a20d9ced8de.jpg
 *     )
 *     [action] => Array (
 *       [basket] => http://www.host.ru/basket/addprd
 *       [wished] => http://www.host.ru/wished/addprd
 *       [compare] => http://www.host.ru/compare/addprd
 *     )
 *   )
 *   [1] => Array (
 *     ..........
 *   )
 *   ..........
 * )
 *
 * $param = Array (
 *   [187] => 1943 // 187 - уникальный ID параметра, 1943 - уникальный ID значения параметра
 *   [241] => 1937
 * )
 *
 * $params = Array (
 *   [0] => Array (
 *     [id] => 187
 *     [name] => Напряжение питания, В
 *     [selected] => 1
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 1605
 *         [name] => переменное 220
 *         [count] => 2
 *         [selected] => 0
 *       )
 *       [1] => Array (
 *         [id] => 1603
 *         [name] => постоянное 12
 *         [count] => 2
 *         [selected] => 1
 *       )
 *       [2] => Array (
 *         [id] => 1945
 *         [name] => постоянное 24
 *         [count] => 1
 *         [selected] => 0
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 241
 *     [name] => Тип табло
 *     [selected] => 0
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 1937
 *         [name] => Световое табло
 *         [count] => 5
 *         [selected] => 0
 *       )
 *       [1] => Array (
 *         [id] => 1940
 *         [name] => Световое табло с РИП
 *         [count] => 0
 *         [selected] => 0
 *       )
 *     )
 *   )
 * )
 *
 * $sortorders = Array (
 *   [0] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74
 *     [name] => без сортировки
 *   )
 *   [1] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/1
 *     [name] => цена, возр.
 *   )
 *   [2] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/2
 *     [name] => цена, убыв.
 *   )
 *   [3] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/3
 *     [name] => название, возр.
 *   )
 *   [4] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/4
 *     [name] => название, убыв.
 *   )
 *   [5] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/5
 *     [name] => код, возр.
 *   )
 *   [6] => Array (
 *     [url] => http://www.host.ru/catalog/maker/74/sort/6
 *     [name] => код, убыв.
 *   )
 * )
 *
 * $units = Array (
 *   0 => '-',
 *   1 => 'шт',
 *   2 => 'компл',
 *   3 => 'упак',
 *   4 => 'метр',
 *   5 => 'пара',
 *   6 => 'кг'
 * )
 *
 * $pager = Array (
 *   [first] => Array (
 *     [num] => 1
 *     [url] => http://www.host.ru/catalog/maker/384
 *   )
 *   [prev] => Array (
 *     [num] => 2
 *     [url] => http://www.host.ru/catalog/maker/384/page/2
 *   )
 *   [current] => Array (
 *     [num] => 3
 *     [url] => http://www.host.ru/catalog/maker/384/page/3
 *   )
 *   [last] => Array (
 *     [num] => 37
 *     [url] => http://www.host.ru/catalog/maker/384/page/37
 *   )
 *   [next] => Array (
 *     [num] => 4
 *     [url] => http://www.host.ru/catalog/maker/384/page/4
 *   )
 *   [left] => Array (
 *     [0] => Array (
 *       [num] => 1
 *       [url] => http://www.host.ru/catalog/maker/384
 *     )
 *     [1] => Array (
 *       [num] => 2
 *       [url] => http://www.host.ru/catalog/maker/384/page/2
 *     )
 *   )
 *   [right] => Array (
 *     [0] => Array (
 *       [num] => 4
 *       [url] => http://www.host.ru/catalog/maker/384/page/4
 *     )
 *     [1] => Array (
 *       [num] => 5
 *       [url] => http://www.host.ru/catalog/maker/384/page/5
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

<!-- Начало шаблона view/example/frontend/template/catalog/maker/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
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
<h1><?php echo $name; ?></h1>

<?php if (empty($products) && empty($group) && empty($hit) && empty($new)): ?>
    <p>Нет товаров у выбранного производителя</p>
    <?php return; ?>
<?php endif; ?>

<div id="catalog-filter">
    <div>
        <span>
            Фильтр
            <a href="<?php echo $clearFilterURL; ?>"<?php if ($group || $hit || $new) echo ' class="show-clear-filter"'; ?>>
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
                                <?php if (isset($item['bound'])): ?>
                                    <?php $bound = true; ?>
                                    <optgroup label="Разное">
                                <?php endif; ?>
                                <option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $group) ? ' selected="selected"' : ''; ?><?php echo (!$item['count']) ? ' class="empty-option"' : ''; ?>><?php echo htmlspecialchars($item['name']) . ' ► ' . $item['count']; ?> шт.</option>
                            <?php endforeach; ?>
                            <?php if (isset($bound)): ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        </span>
                        <?php if ($group): ?><i class="fa fa-times"></i><?php endif; ?>
                    </div>
                </div>
                <?php if ( ! empty($params)): ?>
                    <?php foreach ($params as $item): ?>
                        <div>
                            <div>
                                <span><?php echo $item['name']; ?></span>
                            </div>
                            <div>
                                <span>
                                <select name="param[<?php echo $item['id']; ?>]">
                                    <option value="0">Выберите</option>
                                    <?php foreach ($item['values'] as $value): ?>
                                        <option value="<?php echo $value['id']; ?>"<?php echo $value['selected'] ? ' selected="selected"' : ''; ?><?php echo (!$value['count']) ? ' class="empty-option"' : ''; ?>><?php echo htmlspecialchars($value['name']) . ' ► ' . $value['count']; ?> шт.</option>
                                    <?php endforeach; ?>
                                </select>
                                </span>
                                <?php if ($item['selected']): ?><i class="fa fa-times"></i><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div>
                    <div<?php echo empty($countHit) ? ' class="empty-checkbox"' : ''; ?>>
                        <span>
                            <input type="checkbox" name="hit"<?php echo $hit ? ' checked="checked"' : ''; ?> value="1" id="hit-prd-box" />
                            <label for="hit-prd-box">Лидер продаж</label>
                        </span>
                    </div>
                    <div<?php echo empty($countNew) ? ' class="empty-checkbox"' : ''; ?>>
                        <span>
                            <input type="checkbox" name="new"<?php echo $new ? ' checked="checked"' : ''; ?> value="1" id="new-prd-box" />
                            <label for="new-prd-box">Новинка</label>
                        </span>
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
    <div id="catalog-products">
        <p>По вашему запросу ничего не найдено.</p>
    </div>
    <?php return; ?>
<?php endif; ?>

<div id="catalog-products">

    <div id="sort-orders">
        <ul>
            <li>Сортировка</li>
        <?php foreach($sortorders as $key => $value): ?>
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

    <div class="product-list-<?php echo $view; ?>">
        <?php
            /*
             * При добавлении товара в корзину, в избранное, к сравнению — отравляются данные формы.
             * Если нет поддержки JavaScript, данные будут отправляться с перезагрузкой страницы,
             * т.е. без использования объекта XmlHttpRequest. На этот случай надо в форму добавить
             * множество полей (фильтр по функционалу, новинкам и лидерам продаж, сортировка, номер
             * страницы и т.п.), чтобы восстановить исходную страницу после перезагрузки. Переменная
             * $prm содержит параметры подбора для товаров производителя $id и выбранной из списка
             * <select name="group"> функциональной группы в формате 12.34-56.78, где
             * 12, 56 — уникальные идентификаторы параметров подбора
             * 34, 78 — уникальные идентификаторы значений параметров подбора
             */
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
                        <span><span class="selected"><?php echo $name; ?></span></span>
                    </div>
                </div>
                <div class="product-list-basket">
                    <form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="text" name="count" value="1" size="5" />
                        <input type="hidden" name="return" value="maker" />
                        <input type="hidden" name="return_mkr_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
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
                        <input type="hidden" name="return" value="maker" />
                        <input type="hidden" name="return_mkr_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
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
                    <form action="<?php echo $product['action']['compare']; ?>" method="post" class="add-compare-form" data-group="<?php echo $product['grp_id']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="hidden" name="return" value="maker" />
                        <input type="hidden" name="return_mkr_id" value="<?php echo $id; ?>" />
                        <?php if ($group): ?>
                            <input type="hidden" name="group" value="<?php echo $group; ?>" />
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
                <div class="product-list-descr"><?php echo $product['shortdescr']; ?></div>
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

<!-- Конец шаблона view/example/frontend/template/catalog/maker/center.php -->
