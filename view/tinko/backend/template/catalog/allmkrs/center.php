<?php
/**
 * Список всех производителей,
 * файл view/example/backend/template/catalog/allmkrs/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addMakerUrl - URL ссылки для добавления производителя
 * $makers - массив всех производителей
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/allmkrs/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Производители</h1>

<p><a href="<?php echo $addMakerUrl; ?>">Добавить производителя</a></p>

<?php if (!empty($makers)): ?>
    <div id="all-makers">
        <ul>
        <?php foreach($makers as $maker) : ?>
            <li>
                <div><?php echo $maker['name']; ?></div>
                <div>
                    <a href="<?php echo $maker['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $maker['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/catalog/allmkrs/center.php -->
