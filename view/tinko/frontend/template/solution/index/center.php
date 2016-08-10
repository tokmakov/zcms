<?php
/**
 * Главная страница типовых решений, список всех категорий + список всех типовых
 * решений, файл view/example/frontend/template/solution/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $categories - массив категорий типовых решений
 * $solutions - массив всех типовых решений
 * $pager - постраничная навигация
 *
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/solution/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Типовые решения</h1>

<?php if (!empty($categories)): // категории ?>
    <div id="categories-solutions">
        <div>Категории</div>
        <div>
            <ul>
                <?php
                $divide = 0;
                $count = count($categories);
                if ($count > 5) {
                    $divide = ceil($count/2);
                }
                ?>
                <?php foreach ($categories as $key => $item): ?>
                <li>
                    <?php if ($item['count']): // есть типовые решения в категории? ?>
                        <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                    <?php else: ?>
                        <span><span><?php echo $item['name']; ?></span> <span>0</span></span>
                    <?php endif; ?>
                </li>
                <?php if ($divide && $divide == ($key+1)): ?>
            </ul><ul>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($solutions)): // список типовых решений ?>
    <div id="list-solutions">
    <?php foreach($solutions as $item): ?>
        <div>
            <h2><a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a></h2>
            <div><?php echo $item['excerpt']; ?></div>
            <a href="<?php echo $item['url']['ctg']; ?>"><?php echo $item['ctg_name']; ?></a>
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

<!-- Конец шаблона view/example/frontend/template/solution/index/center.php -->


