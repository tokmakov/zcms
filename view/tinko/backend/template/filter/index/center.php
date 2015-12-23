<?php
/**
 * Главная старница фильтра товаров,
 * файл view/example/backend/template/filter/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $filterPageUrl - URL сводной страницы фильтра товаров
 * $groupsPageUrl - URL страницы со списком всех функциональных групп
 * $paramsPageUrl - URL страницы со списком всех параметров подбора
 * $valuesPageUrl - URL страницы со списком всех значений параметров
 * $groups - массив функциональных групп
 * $params - массив параметров подбора
 * $values - массив значений параметров подбора
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/filter/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Фильтр товаров</h1>

<ul id="tabs">
    <li class="current"><a href="<?php echo $filterPageUrl; ?>">Сводка</a></li>
    <li><a href="<?php echo $groupsPageUrl; ?>">Группы</a></li>
    <li><a href="<?php echo $paramsPageUrl; ?>">Параметры</a></li>
    <li><a href="<?php echo $valuesPageUrl; ?>">Значения</a></li>
</ul>

<div id="index-filter">
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

    <h2>Параметры</h2>
    <?php if (!empty($params)): ?>
        <ul>
        <?php foreach ($params as $item): ?>
            <li><?php echo $item['name']; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нет параметров</p>
    <?php endif; ?>

    <h2>Значения</h2>
    <?php if (!empty($values)): ?>
        <ul>
        <?php foreach ($values as $item): ?>
            <li><?php echo $item['name']; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нет значений</p>
    <?php endif; ?>
</div>

<!-- Конец шаблона шаблона view/example/backend/template/filter/index/center.php -->
