<?php
/**
 * Карта сайта, файл view/example/frontend/template/sitemap/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $pages - массив страниц сайта
 * $solutionsCategories - массив категорий типовых решений
 * $newsCategories - массив категорий новостей
 * $root - массив категорий каталога верхнего уровня и их детей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/sitemap/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Карта сайта</h1>

<div id="sitemap">
    <?php if (!empty($pages)): ?>
        <ul>
        <?php foreach($pages as $item1): ?>
            <li><a href="<?php echo $item1['url']; ?>"><?php echo $item1['name']; ?></a>
            <?php if (isset($item1['childs'])): ?>
                <ul>
                <?php foreach($item1['childs'] as $item2): ?>
                    <li><a href="<?php echo $item2['url']; ?>"><?php echo $item2['name']; ?></a>
                    <?php if (isset($item2['childs'])): ?>
                        <ul>
                        <?php foreach($item2['childs'] as $item3): ?>
                            <li><a href="<?php echo $item3['url']; ?>"><?php echo $item3['name']; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($solutionsCategories)): ?>
        <h2>Типовые решения</h2>
        <ul>
            <?php foreach($solutionsCategories as $item): ?>
                <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($newsCategories)): ?>
        <h2>Новости</h2>
        <ul>
        <?php foreach($newsCategories as $item): ?>
            <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($root)): ?>
        <h2>Каталог оборудования</h2>
        <ul id="catalog-tree">
            <?php foreach($root as $item): ?>
                <li><span><?php echo $item['name']; ?></span>
                <?php if (isset($item['childs'])): ?>
                    <ul>
                    <?php foreach($item['childs'] as $value): ?>
                        <li><a href="<?php echo $value['url']; ?>"><?php echo $value['name']; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/sitemap/center.php -->

