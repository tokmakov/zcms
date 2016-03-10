<?php
/**
 * Страница со списком всех функциональных групп,
 * файл view/example/frontend/template/catalog/groups/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $result - массив результатов поиска функционала
 * $groups - массив всех функциональных групп
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/groups/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="center-block">
    <div><h1>Функциональные группы</h1></div>
    <div class="no-padding">
        <div id="all-groups">
        
            <form action="<?php echo $action; ?>" method="post">
                <input type="text" name="query" value="" placeholder="поиск функциональной группы" />
                <input type="submit" name="submit" value="" />
                <div></div>
            </form>
            
            <?php if (!empty($result)): ?>
                <ol>
                    <li>Результаты поиска</li>
                <?php foreach ($result as $item): ?>
                    <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
                <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        
            <?php $divide = ceil(count($groups)/2); ?>
            <ul>
            <?php foreach ($groups as $key => $item): ?>
                <li>
                    <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                </li>
                <?php if ($divide == ($key+1)): ?>
                    </ul><ul>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>

        </div>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/catalog/groups/center.php -->
