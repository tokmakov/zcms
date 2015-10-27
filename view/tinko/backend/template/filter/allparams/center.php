<?php
/**
 * Страница со списком всех параметров подбора,
 * файл view/example/backend/template/filter/allparams/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $filterPageUrl - URL сводной страницы фильтра товаров
 * $groupsPageUrl - URL страницы со списком всех функциональных групп
 * $paramsPageUrl - URL страницы со списком всех параметров подбора
 * $valuesPageUrl - URL страницы со списком всех значений параметров
 * $addParamUrl - URL страницы с формой для добавления нового параметра
 * $params - массив параметров подбора
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/filter/allparams/center.php -->

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
    <li><a href="<?php echo $groupsPageUrl; ?>">Группы</a></li>
    <li><a href="<?php echo $paramsPageUrl; ?>" class="active">Параметры</a></li>
    <li><a href="<?php echo $valuesPageUrl; ?>">Значения</a></li>
</ul>
<div style="border-top: 1px solid #000; margin-top: -1px;"></div>

<h2>Параметры</h2>
<p><a href="<?php echo $addParamUrl; ?>">Добавить параметр</a></p>
<?php if (!empty($params)): ?>
    <div id="all-params">
        <ul>
            <?php foreach($params as $item) : ?>
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
    <p>Нет параметров</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/filter/allparams/center.php -->
