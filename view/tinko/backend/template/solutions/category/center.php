<?php
/**
 * Список типовых решений выбранной категории,
 * файл view/example/backend/template/solutions/category/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $solutions - массив типовых решений
 *
 * $solutions = Array (
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

<?php if (!empty($solutions)): ?>
    <div id="all-solutions">
        <ul>
        <?php foreach($solutions as $item) : ?>
            <li>
                <div><a href="<?php echo $item['url']['show']; ?>"><?php echo $item['name']; ?></a></div>
                <div>
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
