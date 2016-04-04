<?php
/**
 * Список всех элементов карты сайта в виде дерева,
 * файл view/example/backend/template/sitemap/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addItemURL - URL ссылки на страницу с формой для добавления элемента карты сайта
 * $sitemapItems - массив всех элементов карты сайта в виде дерева
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/sitemap/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Карта сайта</h1>

<p><a href="<?php echo $addItemURL; ?>">Добавить элемент</a></p>

<?php if ( ! empty($sitemapItems)): ?>
    <div id="all-sitemap-items">
        <ul>
        <?php foreach($sitemapItems as $item): ?>
            <li>
                <div>
                    <div><?php echo $item['sortorder']; ?>. <?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                        <a href="<?php echo $item['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
                <?php if (isset($item['childs'])): ?>
                    <ul>
                    <?php foreach($item['childs'] as $child): ?>
                        <li>
                            <div>
                                <div><?php echo $child['sortorder']; ?>. <?php echo $child['name']; ?></div>
                                <div>
                                    <a href="<?php echo $child['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                                    <a href="<?php echo $child['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                                    <a href="<?php echo $child['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                                    <a href="<?php echo $child['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </div>
                            <?php if (isset($child['childs'])): ?>
                                <ul>
                                <?php foreach($child['childs'] as $value): ?>
                                    <li>
                                        <div>
                                            <div><?php echo $value['sortorder']; ?>. <?php echo $value['name']; ?></div>
                                            <div>
                                                <a href="<?php echo $value['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                                                <a href="<?php echo $value['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                                                <a href="<?php echo $value['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                                                <a href="<?php echo $value['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                                            </div>
                                        </div>
                                    </li>
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
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/sitemap/index/center.php -->
