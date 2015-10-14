<?php
/**
 * Страница 404 Not Found, файл view/example/frontend/template/notfound/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/notfound/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Страница не найдена</h1>

<p>Извините, запрошенная вами страница не найдена. Возможно она была удалена с сервера или вы ошиблись при вводе URL-адреса страницы.</p>

<!-- Конец шаблона view/example/frontend/template/notfound/center.php -->
