<?php
/**
 * Главная старница типовых решений,
 * файл view/example/backend/template/solutions/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $indexPageUrl - URL сводной страницы типовых решений
 * $ctgsPageUrl - URL страницы со списком всех категорий
 * $addCtgUrl - URL страницы с формой для добавления категории
 * $addSltnUrl - URL страницы с формой для добавления типового решения
 * $solutions - массив категорий и типовых решений
 *
 * $solutions = Array (
 *   [0] => Array (
 *     [id] => 1
 *     [name] => Охранно-пожарная сигнализация
 *     [edit] => http://www.host.ru/backend/solutions/editctg/id/1
 *     [childs] => Array (
 *       [0] => Array (
 *         [id] => 1
 *         [name] => Типовое решение № 1
 *         [url] => Array (
 *           [show] => http://www.host.ru/backend/solutions/show/id/1
 *           [edit] => http://www.host.ru/backend/solutions/editsltn/id/1
 *         )
 *       )
 *       [1] => Array (
 *         [id] => 5
 *         [name] => Типовое решение № 2
 *         [url] => Array (
 *           [show] => http://www.host.ru/backend/solutions/show/id/5
 *           [edit] => http://www.host.ru/backend/solutions/editsltn/id/5
 *         )
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 2
 *     [name] => Охранное телевидение
 *     [edit] => http://www.host.ru/backend/solutions/editctg/id/2
 *     [childs] => Array (
 *       [0] => Array (
 *         [id] => 2
 *         [name] => Типовое решение № 1
 *         [url] => Array (
 *           [show] => http://www.host.ru/backend/solutions/show/id/2
 *           [edit] => http://www.host.ru/backend/solutions/editsltn/id/2
 *         )
 *       )
 *       [1] => Array (
 *         [id] => 3
 *         [name] => Типовое решение № 2
 *         [url] => Array (
 *           [show] => http://www.host.ru/backend/solutions/show/id/3
 *           [edit] => http://www.host.ru/backend/solutions/editsltn/id/3
 *         )
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Типовые решения</h1>

<ul id="solutions">
    <li><a href="<?php echo $indexPageUrl; ?>" class="active">Сводка</a></li>
    <li><a href="<?php echo $ctgsPageUrl; ?>">Категории</a></li>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addSltnUrl; ?>">Добавить решение</a></li>
</ul>
<div style="border-top: 1px solid #000; margin-top: -1px;"></div>

<div id="index-solutions">
    <h2>Все типовые решения</h2>
    <?php if (!empty($solutions)): ?>
        <ul>
        <?php foreach ($solutions as $category): ?>
            <li>
                <div>
                    <span><?php echo $category['name']; ?></span>
                    <span><a href="<?php echo $category['edit']; ?>" title="Редактировать">Ред.</a></span>
                </div>
                <?php if (!empty($category['childs'])): ?>
                    <ul>
                    <?php foreach ($category['childs'] as $item): ?>
                        <li>
                            <span><a href="<?php echo $item['url']['show']; ?>"><?php echo $item['name']; ?></a></span>
                            <span><a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a></span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет типовых решений</p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Нет категорий</p>
    <?php endif; ?>
</div>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/index/center.php -->
