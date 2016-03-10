<?php
/**
 * Страница со списком всех производителей,
 * файл view/example/frontend/template/catalog/allmkrs/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $result - массив результатов поиска производителя
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

            <form action="<?php echo $action; ?>" method="post">
                <input type="text" name="query" value="" placeholder="поиск производителя" />
                <input type="submit" name="submit" value="" />
                <div></div>
            </form>

            <?php if (!empty($result)): ?>
                <ol>
                    <li>Результаты поиска</li>
                    <?php foreach ($result as $item): ?>
                        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

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
