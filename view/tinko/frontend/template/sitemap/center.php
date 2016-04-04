<?php
/**
 * Карта сайта, файл view/example/frontend/template/sitemap/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $sitemap - массив всех элементов карты сайта в виде дерева
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

    <?php if ( ! empty($sitemap)): ?>
        <ul>
        <?php foreach($sitemap as $item): ?>
            <li>
                <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>
                <?php if (isset($item['childs'])): ?>
                    <ul>
                    <?php foreach($item['childs'] as $child): ?>
                        <li>
                            <a href="<?php echo $child['url']; ?>"><?php echo $child['name']; ?></a>
                            <?php if (isset($child['childs'])): ?>
                                <ul>
                                <?php foreach($child['childs'] as $value): ?>
                                    <li><a href="<?php echo $value['url']; ?>"><?php echo $value['name']; ?></a></li>
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

</div>

<!-- Конец шаблона view/example/frontend/template/sitemap/center.php -->

