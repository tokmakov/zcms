<?php
/**
 * Страница управления партнерами компании,
 * файл view/example/backend/template/partner/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $partners - массив партнеров компании
 * $addPartnerURL - URL страницы с формой для добавления партнера
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/partner/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Партнеры</h1>

<p><a href="<?php echo $addPartnerURL; ?>">Добавить партнера</a></p>

<?php if ( ! empty($partners)): ?>
    <div id="all-partners">
        <ul>
            <?php foreach($partners as $item) : ?>
                <li>
                    <div><span<?php echo $item['expired'] ? ' class="selected"' : ''; ?>><?php echo $item['expire']; ?></span> <?php echo $item['name']; ?></div>
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

<!-- Конец шаблона view/example/backend/template/partner/index/center.php -->
