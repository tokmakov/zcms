<?php
/**
 * Список всех новостей, файл view/example/backend/template/news/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $news - массив всех новостей
 * $addNewsUrl - URL ссылки на страницу с формой для добавления новости
 * $addCtgUrl - URL ссылки на страницу с формой для добавления категории
 * $allCtgsUrl - URL ссылки на страницу со списком всех категорий
 * $thisPageUrl - URL ссылки на эту страницу
 * $pager - постраничная навигация
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/news/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новости</h1>

<ul>
    <li><a href="<?php echo $addNewsUrl; ?>">Добавить новость</a></li>
    <!-- <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>  -->
    <li><a href="<?php echo $allCtgsUrl; ?>">Все категории</a></li>
</ul>

<?php if (!empty($news)): ?>
    <div id="all-news-items">
        <ul>
            <?php foreach($news as $item) : ?>
                <li>
                    <div><?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
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
            <?php foreach ($pager['left'] as $left) : ?>
                <li>
                    <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['left'] != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
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

<!-- Конец шаблона view/example/backend/template/news/index/center.php -->
