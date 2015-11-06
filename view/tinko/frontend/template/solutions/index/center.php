<?php
/**
 * Главная страница типовых решений, список всех категорий + список всех типовых
 * решений, файл view/example/frontend/template/solutions/index/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $thisPageUrl - URL этой страницы
 * $categories - массив категорий типовых решений
 * $solutions - массив всех типовых решений
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

<!-- Начало шаблона view/example/frontend/template/solutions/index/center.php -->

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
        <div>Категории:</div>
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
                    <?php if (true): // есть типовые решения в категории? ?>
                        <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span>5</span></span>
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
    <div id="solutions-list">
    <?php foreach($solutions as $item): ?>
        <div>
            <h2><a href="<?php echo $item['url']['item']; ?>"><?php echo $item['name']; ?></a></h2>
            <div><?php echo $item['excerpt']; ?></div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
    <?php if (isset($pager['first'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>" class="first-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['prev'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['prev'] : ''; ?>" class="prev-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['left'])): ?>
        <?php foreach ($pager['left'] as $left) : ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

    <li>
        <span><?php echo $pager['current']; // текущая страница ?></span>
    </li>

    <?php if (isset($pager['right'])): ?>
        <?php foreach ($pager['right'] as $right) : ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($pager['next'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>" class="next-page"></a>
        </li>
    <?php endif; ?>
    <?php if (isset($pager['last'])): ?>
        <li>
            <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>" class="last-page"></a>
        </li>
    <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/solutions/index/center.php -->


