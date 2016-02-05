<?php
/**
 * Список всех статей, во всех категориях,
 * файл view/example/frontend/template/article/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $articles - массив статей
 * $pager - постраничная навигация
 *
 * $articles = Array (
 *   [0] => Array (
 *     [id] => 7
 *     [name] => Зачем и как обслуживать пожарную сигнализацию?
 *     [excerpt] => Далеко не все руководители организаций могут правильно ответить на вопросы, зачем и как обслуживать...
 *     [date] => 29.11.2014
 *     [time] => 15:22:35
 *     [ctg_id] => 1
 *     [ctg_name] => Охренно-пожарная сигнализация
 *     [url] => Array (
 *       [item] => http://www.host.ru/article/item/7
 *       [image] => http://www.host.ru/files/article/7/7.jpg
 *       [category] => http://www.host.ru/article/category/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 6
 *     [name] => Видеотрансляция на сайте с помощью облачного сервиса Spacecam
 *     [excerpt] => Компания «Спейскам» запускает серию обзоров по применению сервиса облачного видеонаблюдения SpaceCam...
 *     [date] => 29.11.2014
 *     [time] => 15:10:28
 *     [ctg_id] => 1
 *     [ctg_name] => Охранное телевидение
 *     [url] => Array (
 *       [item] => http://www.host.ru/news/item/6
 *       [image] => http://www.host.ru/files/article/6/6.jpg
 *       [category] => http://www.host.ru/article/category/1
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

<!-- Начало шаблона view/example/frontend/template/article/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Статьи</h1>

<?php if (!empty($articles)): // список статей ?>
    <div id="articles-list">
    <?php foreach($articles as $item): ?>
        <div>
            <div>
                <a href="<?php echo $item['url']['item']; ?>">
                    <img src="<?php echo $item['url']['image']; ?>" alt="" />
                </a>
            </div>
            <div>
                <div class="article-date">
                    <?php echo $item['date']; ?>
                </div>
                <div class="article-heading">
                    <h2>
                        <a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a>
                    </h2>
                </div>
                <div class="article-excerpt">
                    <?php echo $item['excerpt']; ?>
                </div>
                <div class="article-category">
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

<!-- Конец шаблона view/example/frontend/template/news/article/center.php -->


