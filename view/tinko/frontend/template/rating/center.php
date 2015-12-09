<?php
/**
 * Райтинг лидеров продаж,
 * файл view/example/frontend/template/rating/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $rating - массив всех категорий и товаров
 * 
 * $rating = Array (
 *   [0] => Array (
 *     [number] => 1
 *     [root] => Охранно-пожарная сигнализация
 *     [childs] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [category] => Извещатели охранные
 *         [products] => Array (
 *           [0] => Array (
 *             [number] => 1
 *             [code] => 001002
 *             [name] => ИО 102-11М (СМК-3)
 *             [title] => Извещатель охранный точечный магнитоконтактный
 *             [url] => http://www.host.ru/catalog/product/1002
 *           )
 *           [1] => Array (
 *             [number] => 2
 *             [code] => 001001
 *             [name] => ИО 102-2 (СМК-1)
 *             [title] => Извещатель охранный точечный магнитоконтактный
 *             [url] => http://www.host.ru/catalog/product/1001
 *           )
 *         )
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [category] => Извещатели пожарные
 *         [products] => Array (
 *           [0] => Array (
 *             [number] => 1
 *             [code] => 005018
 *             [name] => ИП 101-1А-А3
 *             [title] => Извещатель пожарный тепловой максимальный
 *             [url] => http://www.host.ru/catalog/product/5018
 *           )
 *           [1] => Array (
 *             [number] => 2
 *             [code] => 233016
 *             [name] => RT-A2
 *             [title] => Извещатель пожарный тепловой максимальный
 *             [url] => http://www.host.ru/catalog/product/233016
 *           )
 *         )
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [number] => 2
 *     [root] => Охранное телевидение
 *     [childs] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [category] => Видеокамеры
 *         [products] => Array (
 *           [0] => Array (
 *             [number] => 1
 *             [code] => 231533
 *             [name] => EZN1260
 *             [title] => Уличная IP камера
 *             [url] => http://www.host.ru/catalog/product/231533
 *           )
 *           [1] => Array (
 *             [number] => 2
 *             [code] => 231534
 *             [name] => EZN1360
 *             [title] => Уличная IP камера
 *             [url] => http://www.host.ru/catalog/product/231534
 *           )
 *         )
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [category] => Видеорегистраторы
 *         [products] => Array (
 *           [0] => Array (
 *             [number] => 1
 *             [code] =>
 *             [name] => MDR-4000
 *             [title] => Видеорегистратор цифровой 4-канальный
 *             [url] => http://www.host.ru/catalog/product/224523
 *           )
 *           [1] => Array (
 *             [number] => 2
 *             [code] =>
 *             [name] => MDR-8900
 *             [title] => Видеорегистратор 8-канальный
 *             [url] => http://www.host.ru/catalog/product/224526
 *           )
 *         )
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/rating/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Рейтинг лидеров продаж</h1>

<?php if ( ! empty($rating)): ?>
    <div id="rating">
    <?php foreach($rating as $item): ?>
        <h2><?php echo $item['root']; ?></h2>
        <?php if ( ! empty($item['childs'])): ?>
            <div>
            <?php foreach($item['childs'] as $child): ?>
                <p><span><?php echo $child['category']; ?></span></p>
                <?php if ( ! empty($child['products'])): ?>
                    <div>
                        <table>
                        <tr>
                            <th>Место</th>
                            <th>Код</th>
                            <th>Наименование</th>
                        </tr>
                        <?php foreach($child['products'] as $product): ?>
                            <tr>
                                <td><?php echo $product['number']; ?></td>
                                <td>
                                    <?php if ( ! empty($product['url'])): ?>
                                        <a href="<?php echo $product['url']; ?>"><?php echo $product['code']; ?></a>
                                    <?php else: ?>
                                        <?php echo $product['code']; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['name']; ?> <span><?php echo $product['title']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/rating/center.php -->
