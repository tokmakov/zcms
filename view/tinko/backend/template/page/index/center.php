<?php
/**
 * Список всех страниц сайта,
 * файл view/example/backend/template/page/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addPageUrl - URL ссылки на страницу с формой добавления страницы
 * $pages - массив всех страниц сайта
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/page/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Страницы</h1>

<p><a href="<?php echo $addPageUrl; ?>">Добавить страницу</a></p>

<?php if (!empty($pages)): ?>
    <div id="all-pages">
        <ul>
            <?php foreach($pages as $value1): ?>
                <li>
                    <div>
                        <div><?php echo $value1['name']; ?></div>
                        <div>
                            <a href="<?php echo $value1['url']['moveup']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                            <a href="<?php echo $value1['url']['movedown']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                            <a href="<?php echo $value1['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                            <a href="<?php echo $value1['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>
                    <?php if (isset($value1['childs'])): ?>
                        <ul>
                            <?php foreach($value1['childs'] as $value2): ?>
                                <li>
                                    <div>
                                        <div><?php echo $value2['name']; ?></div>
                                        <div>
                                            <a href="<?php echo $value2['url']['moveup']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                                            <a href="<?php echo $value2['url']['movedown']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                                            <a href="<?php echo $value2['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                                            <a href="<?php echo $value2['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    </div>
                                    <?php if (isset($value2['childs'])): ?>
                                        <ul>
                                            <?php foreach($value2['childs'] as $value3): ?>
                                                <li>
                                                    <div>
                                                        <div><?php echo $value3['name']; ?></div>
                                                        <div>
                                                            <a href="<?php echo $value3['url']['moveup']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                                                            <a href="<?php echo $value3['url']['movedown']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                                                            <a href="<?php echo $value3['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                                                            <a href="<?php echo $value3['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
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

<!-- Конец шаблона view/example/backend/template/page/index/center.php -->
