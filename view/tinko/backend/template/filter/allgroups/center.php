<?php
/**
 * Страница со списком всех функциональных групп,
 * файл view/example/backend/template/admin/filter/allgroups/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/admin/filter/allgroups/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Фильтр товаров</h1>

<ul id="filter">
    <li><a href="<?php echo $filterPageUrl; ?>">Сводка</a></li>
    <li><a href="<?php echo $groupsPageUrl; ?>" class="active">Группы</a></li>
    <li><a href="<?php echo $paramsPageUrl; ?>">Параметры</a></li>
    <li><a href="<?php echo $valuesPageUrl; ?>">Значения</a></li>
</ul>
<div style="border-top: 1px solid #000; margin-top: -1px;"></div>

<h2>Группы</h2>
<?php if (!empty($groups)): ?>
    <ul>
    <?php foreach ($groups as $item): ?>
        <li><?php echo $item['name']; ?></li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Нет групп</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/admin/filter/allgroups/center.php -->
