<?php
/**
 * Главное меню, файл view/example/backend/template/menu.php,
 * административная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $menu - массив элементов меню
 */
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/menu.php -->

<div id="menu">
    <ul>
    <?php foreach($menu as $item): ?>
        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
    <?php endforeach; ?>
    </ul>
</div>

<!-- Конец шаблона view/example/backend/template/menu.php -->
