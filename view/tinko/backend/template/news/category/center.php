<?php
/**
 * Список новостей выбранной категории,
 * файл view/example/backend/template/news/category/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории
 * $news - массив новостей категории
 * $thisPageUrl - URL ссылки на эту страницу
 * $pager - постраничная навигация
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/news/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<?php if (!empty($news)): // список новостей категории ?>
    <ul>
    <?php foreach($news as $item) : ?>
        <li><?php echo $item['date']; ?> <?php echo $item['name']; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>">&lt;&lt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['prev'] != 1) ? '/page/'.$pager['prev'] : ''; ?>">&lt;</a>
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
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>">&gt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>">&gt;&gt;</a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/news/category/center.php -->
