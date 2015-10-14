<?php
/**
 * Страница со списком всех производителей,
 * файл view/example/frontend/template/catalog/allmkrs/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $makers - массив всех производителей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/allmkrs/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>


<div class="center-block">
    <div><h1>Производители</h1></div>
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
        </div>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/catalog/allmkrs/center.php -->


