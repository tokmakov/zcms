<?php
/**
 * Страница со списком всех значений параметров подбора,
 * файл view/example/backend/template/filter/allvalues/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $filterPageUrl - URL сводной страницы фильтра товаров
 * $groupsPageUrl - URL страницы со списком всех функциональных групп
 * $paramsPageUrl - URL страницы со списком всех параметров подбора
 * $valuesPageUrl - URL страницы со списком всех значений параметров
 * $addValueUrl - URL страницы с формой для добавления нового значения параметра
 * $values - массив значений параметров подбора
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/filter/allvalues/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Фильтр товаров</h1>

<ul id="tabs">
    <li><a href="<?php echo $filterPageUrl; ?>">Сводка</a></li>
    <li><a href="<?php echo $groupsPageUrl; ?>">Группы</a></li>
    <li><a href="<?php echo $paramsPageUrl; ?>">Параметры</a></li>
    <li class="current"><a href="<?php echo $valuesPageUrl; ?>">Значения</a></li>
</ul>

<h2>Значения</h2>
<p><a href="<?php echo $addValueUrl; ?>">Добавить значение</a></p>
<?php if (!empty($values)): ?>
    <div id="all-values">
        <ul>
            <?php foreach($values as $item) : ?>
                <li>
                    <div><?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p>Нет значений</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/filter/allvalues/center.php -->
