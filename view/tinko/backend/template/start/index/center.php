<?php
/**
 * Страница управления витриной (главная страница сайта),
 * файл view/example/backend/template/start/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $editStartUrl - URL страницы с формой для редактирования витрины
 * $banners - массив баннеров
 * $addBannerUrl - URL страницы с формой для добавления баннера
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/start/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Витрина</h1>

<p><a href="<?php echo $editStartUrl; ?>">Редактировать витрину</a></p>

<?php if (!empty($banners)): ?>
    <h2>Баннеры</h2>
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

<!-- Конец шаблона view/example/backend/template/start/index/center.php -->
