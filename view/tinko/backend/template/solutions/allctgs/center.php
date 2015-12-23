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
 *
 * $categories = Array (
 *   [0] => Array (
 *     [id] => 1
 *     [name] => Охранно-пожарная сигнализация
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/category/id/1
 *       [up] => http://www.host.ru/backend/solutions/ctgup/id/1
 *       [down] => http://www.host.ru/backend/solutions/ctgdown/id/1
 *       [edit] => http://www.host.ru/backend/solutions/editctg/id/1
 *       [remove] => http://www.host.ru/backend/solutions/rmvctg/id/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 2
 *     [name] => Охранное телевидение
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/category/id/2
 *       [up] => http://www.host.ru/backend/solutions/ctgup/id/2
 *       [down] => http://www.host.ru/backend/solutions/ctgdown/id/2
 *       [edit] => http://www.host.ru/backend/solutions/editctg/id/2
 *       [remove] => http://www.host.ru/backend/solutions/rmvctg/id/2
 *     )
 *   )
 *   [2] => Array (
 *     [id] => 3
 *     [name] => Контроль и управление доступом
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/category/id/3
 *       [up] => http://www.host.ru/backend/solutions/ctgup/id/3
 *       [down] => http://www.host.ru/backend/solutions/ctgdown/id/3
 *       [edit] => http://www.host.ru/backend/solutions/editctg/id/3
 *       [remove] => http://www.host.ru/backend/solutions/rmvctg/id/3
 *     )
 *   )
 * )
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

<ul id="tabs">
    <li><a href="<?php echo $indexPageUrl; ?>">Сводка</a></li>
    <li class="current"><a href="<?php echo $ctgsPageUrl; ?>">Категории</a></li>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addSltnUrl; ?>">Добавить решение</a></li>
</ul>

<h2>Категории</h2>

<p><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></p>

<?php if (!empty($categories)): ?>
    <div id="all-categories">
        <ul>
        <?php foreach($categories as $item) : ?>
            <li>
                <div>
                    <?php echo $item['sortorder']; ?>.
                    <a href="<?php echo $item['url']['show']; ?>"><?php echo $item['name']; ?></a>
                </div>
                <div>
                    <a href="<?php echo $item['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                    <a href="<?php echo $item['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                    <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p>Нет категорий</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/allctgs/center.php -->
