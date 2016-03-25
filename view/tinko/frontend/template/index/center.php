<?php
/**
 * Главная страница сайта, файл view/example/frontend/template/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $name - заголовок h1
 * $text - текст страницы
 * $banners - массив всех баннеров
 * $hitProducts - массив товаров, лидеры продаж
 * $hitProducts - массив товаров, новинки
 * $units - массив единиц измерения товара
 * $generalNews - массив последних новостей отрасли
 * $companyNews - массив последних новостей компании
 *
 * $banners = Array (
 *   [0] => Array (
 *     [id] => 1
 *     [name] => Первый баннер
 *     [url] => /page/41
 *     [alttext] => Первый баннер
 *   )
 *   [1] => Array (
 *     [id] => 2
 *     [name] => Второй баннер
 *     [url] => /page/12
 *     [alttext] => Второй баннер
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * 
 * $hitProducts = Array (
 *   [0] => Array (
 *     [id] => 1001
 *     [code] => 001001
 *     [name] => ИО 102-2 (СМК-1)
 *     [title] => Извещатель охранный точечный магнитоконтактный
 *     [price] => 36.80000
 *     [unit] => 1
 *     [shortdescr] => Накладной, 58х11х11 мм
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/1001
 *       [image] => http://www.host.ru/files/catalog/imgs/small/1/f/1fccb567a44880e8665b7cb9d0f97271.jpg
 *     )
 *   )
 *   [1] => Array (
 *     .....
 *   )
 * )
 * 
 * $newProducts = Array (
 *   [0] => Array (
 *     [id] => 1015
 *     [code] => 001015
 *     [name] => Окно-5 (ИО 303-4)
 *     [title] => Извещатель охранный поверхностный ударноконтактный
 *     [price] => 1337.09000
 *     [unit] => 1
 *     [shortdescr] => Блокируемая площадь стекла 20 кв.м, в комплекте 5 датчиков, питание по ШС
 *     [url] => Array (
 *       [product] => http://www.host.ru/catalog/product/1015
 *       [image] => http://www.host.ru/files/catalog/imgs/small/b/a/ba28cd5b0e7a77fddecacdd6b8e61b3b.jpg
 *     )
 *   )
 *   [1] => Array (
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
 *
 */
 
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/index/center.php -->

<div class="center-block heading">
    <div><h1><?php echo $name; ?></h1></div>
    <div><?php echo $text; ?></div>
</div>

<?php if (!empty($banners)): // баннеры ?>
    <ul id="banner-slider">
    <?php foreach($banners as $item): ?>
        <li><a href="<?php echo $item['url']; ?>"><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['alttext']; ?>"></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div id="new-hit-tabs">
    <ul>
        <li><a href="#hit-products"><span>Лидеры продаж</span></a></li>
        <li><a href="#new-products"><span>Новинки</span></a></li>
    </ul>
    <div>
        <div id="hit-products">
            <ul>
            <?php foreach($hitProducts as $product): ?>
                <li>
                    <div class="new-hit-item">
                        <div><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></div>
                        <div><a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a></div>
                        <div><span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <div id="new-products">
            <ul>
                <?php foreach($newProducts as $product): ?>
                    <li>
                        <div class="new-hit-item">
                            <div><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></div>
                            <div><a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a></div>
                            <div><span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="tabs">
    <ul>
        <li><a href="#company-news"><span>Новости компании</span></a></li>
        <li><a href="#general-news"><span>События отрасли</span></a></li>
    </ul>
    <div>
        <div class="news-list" id="company-news">
        <?php foreach($companyNews as $item): ?>
            <div>
                <div>
                    <a href="<?php echo $item['url']['item']; ?>">
                        <img src="<?php echo $item['url']['image']; ?>" alt="" />
                    </a>
                </div>
                <div>
                    <div class="news-date">
                        <?php echo $item['date']; ?>
                    </div>
                    <div class="news-heading">
                        <h3>
                            <a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a>
                        </h3>
                    </div>
                    <div class="news-excerpt">
                        <?php echo $item['excerpt']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="news-list" id="general-news">
        <?php foreach($generalNews as $item): ?>
            <div>
                <div>
                    <a href="<?php echo $item['url']['item']; ?>">
                        <img src="<?php echo $item['url']['image']; ?>" alt="" />
                    </a>
                </div>
                <div>
                    <div class="news-date">
                        <?php echo $item['date']; ?>
                    </div>
                    <div class="news-heading">
                        <h3>
                            <a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a>
                        </h3>
                    </div>
                    <div class="news-excerpt">
                        <?php echo $item['excerpt']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/index/center.php -->



