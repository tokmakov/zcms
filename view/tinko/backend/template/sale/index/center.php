<?php
/**
 * Список всех товаров со скидкой,
 * файл view/example/backend/template/sale/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $addCtgUrl - URL ссылки на страницу с формой для добавления категории
 * $addPrdUrl - URL ссылки на страницу с формой для добавления товара
 * $sale - массив всех категорий и товаров со скидкой
 *
 * $sale = Array (
 *   [0] => Array (
 *     [number] => 1
 *     [name] => Охранное телевидение
 *     [ctgup] => http://www.host.ru/backend/sale/ctgup/id/1
 *     [ctgdown] => http://www.host.ru/backend/sale/ctgdown/id/1
 *     [edit] => http://www.host.ru/backend/sale/editctg/id/1
 *     [remove] => http://www.host.ru/backend/sale/rmvctg/id/1
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [name] => STC-3523/3 ULTIMATE
 *         [title] => Видеокамера купольная
 *         [count] => 5
 *         [price1] => 14591.00000
 *         [price2] => 8000.00000
 *         [prdup] => http://www.host.ru/backend/sale/prdup/id/1
 *         [prddown] => http://www.host.ru/backend/sale/prddown/id/1
 *         [edit] => http://www.host.ru/backend/sale/editprd/id/1
 *         [remove] => http://www.host.ru/backend/sale/rmvprd/id/1
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [name] => ED-100/C-3W
 *         [title] => Видеокамера купольная
 *         [count] => 1
 *         [price1] => 3368.00000
 *         [price2] => 1200.00000
 *         [prdup] => http://www.host.ru/backend/sale/prdup/id/2
 *         [prddown] => http://www.host.ru/backend/sale/prddown/id/2
 *         [edit] => http://www.host.ru/backend/sale/editprd/id/2
 *         [remove] => http://www.host.ru/backend/sale/rmvprd/id/2
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [number] => 2
 *     [name] => Охранно-пожарная сигнализация
 *     [ctgup] => http://www.host.ru/backend/sale/ctgup/id/2
 *     [ctgdown] => http://www.host.ru/backend/sale/ctgdown/id/2
 *     [edit] => http://www.host.ru/backend/sale/editctg/id/2
 *     [remove] => http://www.host.ru/backend/sale/rmvctg/id/2
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [name] => ВС-СМК ВЕКТОР
 *         [title] => Извещатель магнитоконтактный
 *         [count] => 17
 *         [price1] => 1244.00000
 *         [price2] => 800.00000
 *         [prdup] => http://www.host.ru/backend/sale/prdup/id/3
 *         [prddown] => http://www.host.ru/backend/sale/prddown/id/3
 *         [edit] => http://www.host.ru/backend/sale/editprd/id/3
 *         [remove] => http://www.host.ru/backend/sale/rmvprd/id/3
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [name] => FG-1608
 *         [title] => Извещатель охранный
 *         [count] => 48
 *         [price1] => 1452.00000
 *         [price2] => 900.00000
 *         [prdup] => http://www.host.ru/backend/sale/prdup/id/4
 *         [prddown] => http://www.host.ru/backend/sale/prddown/id/4
 *         [edit] => http://www.host.ru/backend/sale/editprd/id/4
 *         [remove] => http://www.host.ru/backend/sale/rmvprd/id/4
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/sale/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Распродажа</h1>

<ul>
    <li><a href="<?php echo $addCtgUrl; ?>">Добавить категорию</a></li>
    <li><a href="<?php echo $addPrdUrl; ?>">Добавить товар</a></li>
</ul>

<?php if ( ! empty($sale)): ?>
    <table class="data-table">
        <tr>
            <th width="3%">№</th>
            <th width="52%">Наименование</th>
            <th width="5%">Кол.</th>
            <th width="10%">Цена</th>
            <th width="10%">Цена</th>
            <th width="5%">Вверх</th>
            <th width="5%">Вниз</th>
            <th width="5%">Ред.</th>
            <th width="5%">Удл.</th>
        </tr>
        <?php foreach($sale as $category): ?>
            <tr>
                <th><?php echo $category['number']; ?></th>
                <th colspan="4"><?php echo $category['name']; ?></th>
                <th><a href="<?php echo $category['ctgup']; ?>" title="Вверх">Вверх</a></th>
                <th><a href="<?php echo $category['ctgdown']; ?>" title="Вниз">Вниз</a></th>
                <th><a href="<?php echo $category['edit']; ?>" title="Редактировать">Ред.</a></th>
                <th><a href="<?php echo $category['remove']; ?>" title="Удалить">Удл.</a></th>
            </tr>
            <?php if ( ! empty($category['products'])): ?>
                <?php foreach($category['products'] as $product): ?>
                    <tr>
                        <td><?php echo $product['number']; ?></td>
                        <td><?php echo $product['name']; ?> <em><?php echo $product['title']; ?></em></td>
                        <td><?php echo $product['count']; ?></td>
                        <td><?php echo number_format($product['price1'], 2, '.', ''); ?></td>
                        <td><?php echo number_format($product['price2'], 2, '.', ''); ?></td>
                        <td><a href="<?php echo $product['prdup']; ?>" title="Вверх">Вверх</a></td>
                        <td><a href="<?php echo $product['prddown']; ?>" title="Вниз">Вниз</a></td>
                        <td><a href="<?php echo $product['edit']; ?>" title="Редактировать">Ред.</a></td>
                        <td><a href="<?php echo $product['remove']; ?>" title="Удалить">Удл.</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/sale/index/center.php -->
