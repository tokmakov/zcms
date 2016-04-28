<?php
/**
 * Партнерские сертификаты,
 * файл view/example/frontend/template/partner/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $partners - массив партнеров компании
 *
 * $partners = Array (
 *   [0] => Array (
 *     [id] => 11
 *     [name] => Болид
 *     [alttext] => Болид
 *     [sortorder] => 1
 *     [url] => Array (
 *       [image] => http://www.host.ru/files/partner/images/11.jpg
 *       [thumb] => http://www.host.ru/files/partner/thumbs/11.jpg
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 12
 *     [name] => Аргус-Спектр
 *     [alttext] => Аргус-Спектр
 *     [sortorder] => 2
 *     [url] => Array (
 *       [image] => http://www.host.ru/files/partner/images/12.jpg
 *       [thumb] => http://www.host2.ru/files/partner/thumbs/12.jpg
 *     )
 *   )
 *   [2] = Array (
 *     ..........
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/partner/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Партнеры</h1>

<?php if ( ! empty($partners)): ?>
    <div id="partners">
    <?php foreach($partners as $item): ?>
        <a href="<?php echo $item['url']['image']; ?>" class="zoom" rel="partner">
            <span><?php echo $item['sortorder']; ?></span>
            <img src="<?php echo $item['url']['thumb']; ?>" alt="<?php echo $item['alttext']; ?>" />
        </a>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/partner/center.php -->
