<?php
/**
 * Страница отдельного поста блога,
 * файл view/example/frontend/template/blog/post/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * name - заголовок поста блога
 * body - текст поста блога в формате html
 * date - дата публикации
 * categoryName - наименование категории
 * $categoryPageUrl - URL страницы категории
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/blog/post/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<div id="post-item">
    <div>
        <p><?php echo $date; ?></p>
        <p>Категория: <a href="<?php echo $categoryPageUrl; ?>"><?php echo $categoryName; ?></a></p>
    </div>
    <?php echo $body; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/blog/post/center.php -->
