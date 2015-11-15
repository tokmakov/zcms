<?php
/**
 * Страница управления баннерами,
 * файл view/example/backend/template/banner/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $banners - массив баннеров
 * $addBannerUrl - URL страницы с формой для добавления баннера
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/banner/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Баннеры</h1>

<?php if (!empty($banners)): ?>
    <div id="all-banners">
        <ul>
            <?php foreach($banners as $item) : ?>
                <li>
                    <div><span><?php echo $item['date']; ?></span> <?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['up']; ?>" title="Вверх">Вверх</a>
                        <a href="<?php echo $item['url']['down']; ?>" title="Вниз">Вниз</a>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать">Ред.</a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<p><a href="<?php echo $addBannerUrl; ?>">Добавить баннер</a></p>

<!-- Конец шаблона view/example/backend/template/banner/index/center.php -->
