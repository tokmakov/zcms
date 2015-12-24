<?php
/**
 * Главная страница новостей, список новостей всех категорий,
 * файл view/example/frontend/template/news/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $news - массив новостей
 * $pager - постраничная навигация
 *
 * $news = Array (
 *   [0] => Array (
 *     [id] => 7
 *     [name] => Снижение цен на IP и HDcctv оборудование EverFocus
 *     [excerpt] => Уважаемые покупатели! C 26 ноября вы сможете приобрести IP и HDcctv оборудование EverFocus...
 *     [date] => 29.11.2014
 *     [time] => 15:22:35
 *     [ctg_id] => 1
 *     [ctg_name] => Новости компании
 *     [url] => Array (
 *       [item] => /news/item/7
 *       [image] => /files/news/7/7.jpg
 *       [category] => /news/ctg/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 6
 *     [name] => Моноблок речевого оповещения Соната-К-120М с внешним микрофоном
 *     [excerpt] => Представляем усовершенствованную модель моноблока речевого оповещения Соната-К-120М...
 *     [date] => 29.11.2014
 *     [time] => 15:10:28
 *     [ctg_id] => 1
 *     [ctg_name] => Новости компании
 *     [url] => Array (
 *       [item] => /news/item/6
 *       [image] => /files/news/6/6.jpg
 *       [category] => /news/ctg/1
 *     )
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/news/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новости</h1>

<?php if (!empty($news)): // список новостей ?>
    <div id="news-list">
    <?php foreach($news as $item): ?>
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
                    <h2>
                        <a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a>
                    </h2>
                </div>
                <div class="news-excerpt">
                    <?php echo $item['excerpt']; ?>
                </div>
                <div class="news-category">
                    <a href="<?php echo $item['url']['category']; ?>"><?php echo $item['ctg_name']; ?></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

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

<!-- Конец шаблона view/example/frontend/template/news/index/center.php -->


