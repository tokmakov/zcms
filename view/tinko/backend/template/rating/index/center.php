<?php
/**
 * Список всех категорий рейтинга продаж,
 * файл view/example/backend/template/rating/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addCtgUrl - URL ссылки на страницу с формой для добавления категории
 * $categories - массив всех категорий
 * 
 * $categories = Array(
 *   [1] => Array (
 *     [id] => 1
 *     [parent] => 0
 *     [name] => Охранно-пожарная сигнализация
 *     [sortorder] => 1
 *     [url] => Array (
 *       [link] => http://www.host.ru/backend/rating/category/id/1
 *       [up] => http://www.host.ru/backend/rating/ctgup/id/1
 *       [down] => http://www.host.ru/backend/rating/ctgdown/id/1
 *       [edit] => http://www.host.ru/backend/rating/editctg/id/1
 *       [remove] => http://www.host.ru/backend/rating/rmvctg/id/1
 *     )
 *     [childs] => Array (
 *       [3] => Array (
 *         [id] => 3
 *         [parent] => 1
 *         [name] => Извещатели охранные
 *         [sortorder] => 1
 *         [url] => Array (
 *           [link] => null
 *           [up] => http://www.host.ru/backend/rating/ctgup/id/3
 *           [down] => http://www.host.ru/backend/rating/ctgdown/id/3
 *           [edit] => http://www.host.ru/backend/rating/editctg/id/3
 *           [remove] => http://www.host.ru/backend/rating/rmvctg/id/3
 *         )
 *       )
 *       [4] => Array (
 *         [id] => 4
 *         [parent] => 1
 *         [name] => Извещатели пожарные
 *         [sortorder] => 2
 *         [url] => Array (
 *           [link] => null
 *           [up] => http://www.host.ru/backend/rating/ctgup/id/4
 *           [down] => http://www.host.ru/backend/rating/ctgdown/id/4
 *           [edit] => http://www.host.ru/backend/rating/editctg/id/4
 *           [remove] => http://www.host.ru/backend/rating/rmvctg/id/4
 *         )
 *       )
 *     )
 *   )
 *   [2] => Array (
 *     [id] => 2
 *     [parent] => 0
 *     [name] => Охранное телевидение
 *     [sortorder] => 2
 *     [url] => Array (
 *       [link] => http://www.host.ru/backend/rating/category/id/2
 *       [up] => http://www.host.ru/backend/rating/ctgup/id/2
 *       [down] => http://www.host.ru/backend/rating/ctgdown/id/2
 *       [edit] => http://www.host.ru/backend/rating/editctg/id/2
 *       [remove] => http://www.host.ru/backend/rating/rmvctg/id/2
 *     )
 *     [childs] => Array (
 *       [5] => Array (
 *         [id] => 5
 *         [parent] => 2
 *         [name] => Видеокамеры
 *         [sortorder] => 1
 *         [url] => Array (
 *           [link] => null
 *           [up] => http://www.host.ru/backend/rating/ctgup/id/5
 *           [down] => http://www.host.ru/backend/rating/ctgdown/id/5
 *           [edit] => http://www.host.ru/backend/rating/editctg/id/5
 *           [remove] => http://www.host.ru/backend/rating/rmvctg/id/5
 *         )
 *       )
 *       [6] => Array (
 *         [id] => 6
 *         [parent] => 2
 *         [name] => Видеорегистраторы
 *         [sortorder] => 2
 *         [url] => Array (
 *           [link] => null
 *           [up] => http://www.host.ru/backend/rating/ctgup/id/6
 *           [down] => http://www.host.ru/backend/rating/ctgdown/id/6
 *           [edit] => http://www.host.ru/backend/rating/editctg/id/6
 *           [remove] => http://www.host.ru/backend/rating/rmvctg/id/6
 *         )
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/rating/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Рейтинг</h1>

<p><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></p>

<?php if ( ! empty($categories)): ?>
    <div id="all-categories">
        <ul>
        <?php foreach($categories as $item): ?>
            <li>
                <div>
                    <div>
                        <?php echo $item['sortorder']; ?>. <a href="<?php echo $item['url']['link']; ?>"><?php echo $item['name']; ?></a>
                    </div>
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
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/rating/index/center.php -->
