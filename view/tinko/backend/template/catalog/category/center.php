<?php
/**
 * Категория каталога, список дочерних категорий + список товаров категории,
 * файл view/example/backend/template/catalog/category/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории
 * $addCtgUrl - URL ссылки для добавления категории
 * $addPrdUrl - URL ссылки для добавления товара
 * $childCategories - массив дочерних категорий
 * $products - массив товаров категории
 * $thisPageUrl - URL ссылки на эту страницу
 * $pager - постраничная навигация
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<ul>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addPrdUrl; ?>">Добавить товар</a></li>
</ul>

<?php if (!empty($childCategories)): // дочерние категории ?>
    <div id="child-categories">
        <ul>
        <?php foreach($childCategories as $category) : ?>
            <li>
                <div><?php echo $category['sortorder']; ?> : <?php echo $category['globalsort']; ?> <a href="<?php echo $category['url']['link']; ?>"><?php echo $category['name']; ?></a></div>
                <div>
                    <a href="<?php echo $category['url']['up']; ?>" title="Вверх">Вверх</a>
                    <a href="<?php echo $category['url']['down']; ?>" title="Вниз">Вниз</a>
                    <a href="<?php echo $category['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $category['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($products)): // товары категории ?>
    <div id="category-products">
        <ul>
        <?php foreach($products as $product) : ?>
            <li>
                <div><?php echo $product['sortorder']; ?>. <?php echo $product['name']; ?></div>
                <div>
                    <a href="<?php echo $product['url']['up']; ?>" title="Вверх">Вверх</a>
                    <a href="<?php echo $product['url']['down']; ?>" title="Вниз">Вниз</a>
                    <a href="<?php echo $product['url']['edit']; ?>" title="Редактировать">Ред.</a>
                    <a href="<?php echo $product['url']['remove']; ?>" title="Удалить">Удл.</a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($pager) && is_array($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>">&lt;&lt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['prev'] != 1) ? '/page/'.$pager['prev'] : ''; ?>">&lt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($left != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

    <li>
        <span><?php echo $pager['current']; // текущая страница ?></span>
    </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>">&gt;</a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>">&gt;&gt;</a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/catalog/category/center.php -->
