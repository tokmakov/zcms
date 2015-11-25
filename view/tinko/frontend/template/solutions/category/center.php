<?php
/**
 * Список типовых решений выбранной категории,
 * файл view/example/frontend/template/solutions/category/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории
 * $solutions - массив типовых решений
 * $pager - постраничная навигация
 *
 * $pager = Array (
 *     [first] => 1
 *     [prev] => 2
 *     [current] => 3
 *     [next] => 4
 *     [last] => 5
 *     [left] => Array (
 *         [0] => 2
 *     )
 *     [right] => Array (
 *         [0] => 4
 *     )
 * )
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/solutions/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<?php if (!empty($solutions)): // список типовых решений ?>
    <div id="list-solutions">
        <?php foreach($solutions as $item): ?>
            <div>
                <h2><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></h2>
                <div><?php echo $item['excerpt']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $pager['first']['url']; ?>" class="first-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $pager['prev']['url']; ?>" class="prev-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left) : ?>
            <li>
                <a href="<?php echo $left['url']; ?>"><?php echo $left['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

        <li>
            <span><?php echo $pager['current']['num']; // текущая страница ?></span>
        </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right) : ?>
            <li>
                <a href="<?php echo $right['url']; ?>"><?php echo $right['num']; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $pager['next']['url']; ?>" class="next-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $pager['last']['url']; ?>" class="last-page"></a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/solutions/category/center.php -->


