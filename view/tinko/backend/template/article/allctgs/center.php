<?php
/**
 * Список всех категорий статей,
 * файл view/example/backend/template/article/allctgs/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $categories - массив всех категорий статей
 * $addCtgUrl - URL ссылки на страницу с формой для добавления категории
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/article/allctgs/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Категории статей</h1>

<p><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></p>

<?php if (!empty($categories)): ?>
    <div id="all-categories">
        <ul>
        <?php foreach($categories as $category) : ?>
            <li>
                <div><?php echo $category['name']; ?></div>
                <div>
                    <a href="<?php echo $category['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                    <a href="<?php echo $category['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                    <a href="<?php echo $category['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?php echo $category['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/article/allctgs/center.php -->
