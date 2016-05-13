<?php
/**
 * Список всех брендов,
 * файл view/example/backend/template/brand/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addBrandURL - URL страницы с формой для добавления бренда
 * $popular - массив популярных брендов
 * $brands - массив всех брендов
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/brand/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Бренды</h1>

<p><a href="<?php echo $addBrandURL; ?>">Добавить бренд</a></p>

<?php if ( ! empty($popular)): /* популярные бренды */ ?>
    <div id="popular">  
        <ul>
        <?php foreach($popular as $item): ?>
            <li>
                <div><?php echo $item['sortorder']; ?>. <?php echo $item['name']; ?></div>
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
<?php endif; ?>

<div id="all-brands">
    <?php if ( ! empty($brands['A-Z'])): /* бренды A-Z */ ?>
        <ul>
        <?php foreach ($brands['A-Z'] as $letter => $items): ?>
            <li>
                <div><?php echo $letter; ?></div>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <div><?php echo $item['sortorder']; ?>. <?php echo $item['name']; ?></div>
                        <div>
                            <a href="<?php echo $item['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                            <a href="<?php echo $item['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                            <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                            <a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php if ( ! empty($brands['А-Я'])): /* бренды А-Я */ ?>
        <ul>
        <?php foreach ($brands['А-Я'] as $letter => $items): ?>
            <li>
                <div><?php echo $letter; ?></div>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <div><?php echo $item['sortorder']; ?>. <?php echo $item['name']; ?></div>
                        <div>
                            <a href="<?php echo $item['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                            <a href="<?php echo $item['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                            <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                            <a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/backend/template/brand/index/center.php -->
