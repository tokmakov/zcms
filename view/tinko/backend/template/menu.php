<?php
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/menu.php -->

<ul>
<?php foreach($menu as $item): ?>
    <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
<?php endforeach; ?>
</ul>

<!-- Конец шаблона view/example/backend/template/menu.php -->
