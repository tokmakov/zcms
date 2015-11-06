<?php
/**
 * Список типовых решений выбранной категории,
 * файл view/example/backend/template/solutions/category/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addSltnUrl - URL страницы с формой для добавления типового решения
 * $solutions - массив типовых решений
 *
 * $solutions = Array (
 *   [0] => Array (
 *     [id] => 1
 *     [name] => Охрана объекта на базе радиосистемы охранно-пожарной сигнализации Астра РИ-М
 *     [sortorder] => 1
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/show/id/1
 *       [up] => http://www.host.ru/backend/solutions/sltnup/id/1
 *       [down] => http://www.host.ru/backend/solutions/sltndown/id/1
 *       [edit] => http://www.host.ru/backend/solutions/editsltn/id/1
 *       [remove] => http://www.host.ru/backend/solutions/rmvsltn/id/1
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 5
 *     [name] => Система ОПС на базе ПКП «Астра-Дозор» с возможностью радиорасширения
 *     [sortorder] => 2
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/show/id/5
 *       [up] => http://www.host.ru/backend/solutions/sltnup/id/5
 *       [down] => http://www.host.ru/backend/solutions/sltndown/id/5
 *       [edit] => http://www.host.ru/backend/solutions/editsltn/id/5
 *       [remove] => http://www.host.ru/backend/solutions/rmvsltn/id/5
 *     )
 *   )
 *   [2] => Array (
 *     [id] => 11
 *     [name] => Радиоканальная система централизованиий охраны РИФ СТРИНГ-200
 *     [sortorder] => 3
 *     [url] => Array (
 *       [show] => http://www.host.ru/backend/solutions/show/id/11
 *       [up] => http://www.host.ru/backend/solutions/sltnup/id/11
 *       [down] => http://www.host.ru/backend/solutions/sltndown/id/11
 *       [edit] => http://www.host.ru/backend/solutions/editsltn/id/11
 *       [remove] => http://www.host.ru/backend/solutions/rmvsltn/id/11
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<p><a href="<?php echo $addSltnUrl; ?>">Добавить типовое решение</a></p>

<?php if (!empty($solutions)): ?>
    <div id="all-solutions">
        <ul>
        <?php foreach($solutions as $item) : ?>
            <li>
                <div>
                    <?php echo $item['sortorder']; ?>.
                    <a href="<?php echo $item['url']['show']; ?>"><?php echo $item['name']; ?></a>
                </div>
                <div>
                    <a href="<?php echo $item['url']['up']; ?>" title="Вверх">Вверх</a>
                    <a href="<?php echo $item['url']['down']; ?>" title="Вниз">Вниз</a>
                    <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p>Нет типовых решений</p>
<?php endif; ?>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/category/center.php -->
