<?php
/**
 * Страница со списком всех категорий типовых решений,
 * файл view/example/backend/template/solutions/allctgs/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $indexPageUrl - URL сводной страницы типовых решений
 * $ctgsPageUrl - URL страницы со списком всех категорий
 * $addCtgUrl - URL страницы с формой для добавления категории
 * $addSltnUrl - URL страницы с формой для добавления типового решения
 * $categories - массив категорий типовых решений
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/allctgs/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Типовые решения</h1>

<ul id="solutions">
    <li><a href="<?php echo $indexPageUrl; ?>">Сводка</a></li>
    <li><a href="<?php echo $ctgsPageUrl; ?>" class="active">Категории</a></li>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addSltnUrl; ?>">Добавить решение</a></li>
</ul>
<div style="border-top: 1px solid #000; margin-top: -1px;"></div>

<h2>Категории</h2>

<?php if (!empty($categories)): ?>
    <div id="all-categories">
        <ul>
        <?php foreach($categories as $item) : ?>
            <li>
                <div><a href="<?php echo $item['url']['show']; ?>"><?php echo $item['name']; ?></a></div>
                <div>
                    <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p>Нет категорий</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/allctgs/center.php -->
