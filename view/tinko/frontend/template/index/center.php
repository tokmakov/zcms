<?php
/**
 * Главная страница сайта, файл view/example/frontend/template/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $name - заголовок h1
 * $text - текст страницы
 * $banners - массив всех баннеров
 * $news - массив последних новостей
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
    <ul class="slider">
    <?php foreach($banners as $item): ?>
        <li><a href="<?php echo $item['url']; ?>"><img src="/files/index/slider/<?php echo $item['id']; ?>.jpg" alt="<?php echo $item['alttext']; ?>"></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($news)): // список новостей ?>
    <div class="center-block">
        <div><h2>Последние новости</h2></div>
        <div>
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
                            <h3>
                                <a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a>
                            </h3>
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
        </div>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/index/center.php -->



