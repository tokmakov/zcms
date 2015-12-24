<?php
/**
 * Страница отдельной новости, общедоступная часть сайта,
 * файл view/example/frontend/template/news/item/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * name - заголовок новости
 * body - текст новости
 * date - дата публикации новости
 * categoryName - наименование категории новости
 * $categoryPageUrl - URL страницы категории
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/news/item/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<div id="news-item">
    <div>
        <p><?php echo $date; ?></p>
        <p>Категория: <a href="<?php echo $categoryPageUrl; ?>"><?php echo $categoryName; ?></a></p>
    </div>
    <?php echo $body; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/news/item/center.php -->
