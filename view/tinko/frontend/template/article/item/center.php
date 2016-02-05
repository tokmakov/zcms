<?php
/**
 * Страница отдельной статьи, общедоступная часть сайта,
 * файл view/example/frontend/template/article/item/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * name - заголовок статьи
 * body - текст статьи
 * date - дата публикации статьи
 * categoryName - наименование категории статьи
 * $categoryPageUrl - URL страницы категории
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/article/item/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<div id="article-item">
    <div>
        <p><?php echo $date; ?></p>
        <p>Категория: <a href="<?php echo $categoryPageUrl; ?>"><?php echo $categoryName; ?></a></p>
    </div>
    <?php echo $body; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/article/item/center.php -->
