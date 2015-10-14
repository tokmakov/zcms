<?php
/**
 * Главная страница каталога, список категорий верхнего уровня,
 * файл view/example/frontend/template/catalog/index/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $root - массив категорий верхнего уровня
 * $makers - массив производителей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="center-block">
    <div><h1>Каталог оборудования</h1></div>
    <div>
        <div id="root-categories">
            <ul>
            <?php foreach ($root as $ctg): ?>
                <li><a href="<?php echo $ctg['url']; ?>"><span><?php echo $ctg['name']; ?></span></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="center-block">
    <div><h2>Производители</h2></div>
    <div class="no-padding">
        <div id="all-makers">
            <?php $divide = ceil(count($makers)/2); ?>
            <ul>
            <?php foreach ($makers as $key => $item): ?>
                <li>
                    <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                </li>
                <?php if ($divide == ($key+1)): ?>
                    </ul><ul>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
            <p><a href="<?php echo $allMakersUrl; ?>">Все производители</a></p>
        </div>

    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/catalog/index/center.php -->


