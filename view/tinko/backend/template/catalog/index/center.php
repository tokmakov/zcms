<?php
/**
 * Главная страница каталога, список категорий верхнего уровня,
 * файл view/example/backend/template/catalog/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addCtgUrl - URL страницы с формой для добавления категории
 * $addPrdUrl - URL страницы с формой для добавления товара
 * $allMkrUrl - URL страницы со списком всех производителей
 * $root - массив категорий верхнего уровня
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Каталог</h1>

<ul>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addPrdUrl; ?>">Добавить товар</a></li>
</ul>

<ul>
    <li><a href="<?php echo $allMkrUrl; ?>">Все производители</a></li>
</ul>

<?php if (!empty($root)): ?>
    <div id="child-categories">
        <ul>
        <?php foreach($root as $category) : ?>
            <li>
                <div><?php echo $category['sortorder']; ?> <a href="<?php echo $category['url']['link']; ?>"><?php echo $category['name']; ?></a></div>
                <div>
                    <a href="<?php echo $category['url']['up']; ?>" title="Вверх">Вверх</a>
                    <a href="<?php echo $category['url']['down']; ?>" title="Вниз">Вниз</a>
                    <a href="<?php echo $category['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $category['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/catalog/index/center.php -->
