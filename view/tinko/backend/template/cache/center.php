<?php
/**
 * Страница управления кэшем, административная часть сайта,
 * файл view/example/backend/template/cache/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $clearCacheUrl - URL ссылки «Очистить кэш»
 * $makeCacheUrl - URL ссылки «Создать кэш»
 */

defined('ZCMS') or die('Access denied');
?>

<!-- view/example/backend/template/cache/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Управление кэшем</h1>

<ul>
    <li><a href="<?php echo $clearCacheUrl; ?>">Очистить кэш</a></li>
    <li><a href="<?php echo $makeCacheUrl; ?>">Создать кэш</a></li>
</ul>
