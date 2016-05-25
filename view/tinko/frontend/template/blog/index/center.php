<?php
/**
 * Главная страница блога, список постов блога всех категорий,
 * файл view/example/frontend/template/blog/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $posts - массив всех постов блога
 * $pager - постраничная навигация
 *
 * $posts = Array (
 *   [0] => Array (
 *     [id] => 7
 *     [name] => Снижение цен на IP и HDcctv оборудование EverFocus
 *     [excerpt] => Уважаемые покупатели! C 26 ноября вы сможете приобрести IP и HDcctv оборудование EverFocus...
 *     [date] => 29.11.2014
 *     [time] => 15:22:35
 *     [ctg_id] => 1
 *     [ctg_name] => Новости компании
 *     [url] => Array (
 *       [post] => http://www.host.ru/blog/post/7
 *       [image] => http://www.host.ru/files/blog/thumb/7.jpg
 *       [category] => http://www.host.ru/blog/category/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 6
 *     [name] => Моноблок речевого оповещения Соната-К-120М с внешним микрофоном
 *     [excerpt] => Представляем усовершенствованную модель моноблока речевого оповещения Соната-К-120М...
 *     [date] => 29.11.2014
 *     [time] => 15:10:28
 *     [ctg_id] => 2
 *     [ctg_name] => События отрасли
 *     [url] => Array (
 *       [post] => http://www.host.ru/blog/post/6
 *       [image] => http://www.host.ru/files/blog/thumb/6.jpg
 *       [category] => http://www.host.ru/blog/category/2
 *     )
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $pager = Array (
 *   [first] => Array (
 *     [num] => 1
 *     [url] => http://www.host.ru/blog
 *   )
 *   [prev] => Array (
 *     [num] => 2
 *     [url] => http://www.host.ru/blog/page/2
 *   )
 *   [current] => Array (
 *     [num] => 3
 *     [url] => http://www.host.ru/blog/page/3
 *   )
 *   [last] => Array (
 *     [num] => 32
 *     [url] => http://www.host.ru/blog/page/32
 *   )
 *   [next] => Array (
 *     [num] => 4
 *     [url] => http://www.host.ru/blog/page/4
 *   )
 *   [left] => Array (
 *     [0] => Array (
 *       [num] => 1
 *       [url] => http://www.host.ru/blog
 *     )
 *     [1] => Array (
 *       [num] => 2
 *       [url] => http://www.host.ru/blog/page/2
 *     )
 *   )
 *   [right] => Array (
 *     [0] => Array (
 *       [num] => 4
 *       [url] => http://www.host.ru/blog/page/4
 *     )
 *     [1] => Array (
 *       [num] => 5
 *       [url] => http://www.host.ru/blog/page/5
 *     )
 *   )
 * )
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/blog/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новости</h1>

<?php if (!empty($posts)): // список постов блога ?>
    <div id="posts-list">
    <?php foreach($posts as $item): ?>
        <div>
            <div>
                <a href="<?php echo $item['url']['post']; ?>">
                    <img src="<?php echo $item['url']['image']; ?>" alt="" />
                </a>
            </div>
            <div>
                <div class="post-date">
                    <?php echo $item['date']; ?>
                </div>
                <div class="post-heading">
                    <h2>
                        <a href="<?php echo $item['url']['post']; ?>"><?php echo $item['name']; ?></a>
                    </h2>
                </div>
                <div class="post-excerpt">
                    <?php echo $item['excerpt']; ?>
                </div>
                <div class="post-category">
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

<!-- Конец шаблона view/example/frontend/template/blog/index/center.php -->


