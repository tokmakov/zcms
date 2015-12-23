<?php
/**
 * Список всех новостей, файл view/example/backend/template/news/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $news - массив всех новостей
 * $addNewsUrl - URL ссылки на страницу с формой для добавления новости
 * $addCtgUrl - URL ссылки на страницу с формой для добавления категории
 * $allCtgsUrl - URL ссылки на страницу со списком всех категорий
 * $pager - постраничная навигация
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/news/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новости</h1>

<ul id="add-news-all-ctgs">
    <li><a href="<?php echo $addNewsUrl; ?>">Добавить новость</a></li>
    <li><a href="<?php echo $allCtgsUrl; ?>">Все категории</a></li>
</ul>

<?php if (!empty($news)): ?>
    <div id="all-news-items">
        <ul>
            <?php foreach($news as $item) : ?>
                <li>
                    <div><?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
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

<!-- Конец шаблона view/example/backend/template/news/index/center.php -->
