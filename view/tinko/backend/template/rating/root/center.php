<?php
/**
 * Категория верхнего уровня рейтинга, список дочерних категорий + список товаров рейтинга,
 * файл view/example/backend/template/rating/root/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории верхнего уровня
 * $root - массив дочерних категорий и товаров рейтинга
 * $addPrdUrl - URL ссылки для добавления товара
 * $root - массив дочерних категорий и товаров
 * 
 * $root = Array (
 *   [0] => Array (
 *     [number] => 1
 *     [category] => Извещатели охранные
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [name] => ИО 102-2 (СМК-1)
 *         [title] => Извещатель охранный точечный магнитоконтактный
 *         [up] => http://www.host.ru/backend/rating/prdup/id/2
 *         [down] => http://www.host.ru/backend/rating/prddown/id/2
 *         [edit] => http://www.host.ru/backend/rating/editprd/id/2
 *         [remove] => http://www.host.ru/backend/rating/rmvprd/id/2
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [name] => ИО 102-11М (СМК-3)
 *         [title] => Извещатель охранный точечный магнитоконтактный
 *         [up] => http://www.host.ru/backend/rating/prdup/id/3
 *         [down] => http://www.host.ru/backend/rating/prddown/id/3
 *         [edit] => http://www.host.ru/backend/rating/editprd/id/3
 *         [remove] => http://www.host.ru/backend/rating/rmvprd/id/3
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [number] => 2
 *     [category] => Извещатели пожарные
 *     [products] => Array (
 *       [0] => Array (
 *         [number] => 1
 *         [name] => ИП 101-1А-А3
 *         [title] => Извещатель пожарный тепловой максимальный
 *         [up] => http://www.host.ru/backend/rating/prdup/id/4
 *         [down] => http://www.host.ru/backend/rating/prddown/id/4
 *         [edit] => http://www.host.ru/backend/rating/editprd/id/4
 *         [remove] => http://www.host.ru/backend/rating/rmvprd/id/4
 *       )
 *       [1] => Array (
 *         [number] => 2
 *         [name] => АРГО-А1
 *         [title] => Извещатель пожарный тепловой максимальный
 *         [up] => http://www.host.ru/backend/rating/prdup/id/5
 *         [down] => http://www.host.ru/backend/rating/prddown/id/5
 *         [edit] => http://www.host.ru/backend/rating/editprd/id/5
 *         [remove] => http://www.host.ru/backend/rating/rmvprd/id/5
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/rating/root/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<p><a href="<?php echo $addPrdUrl; ?>">Добавить товар</a></p>

<?php if ( ! empty($root)): ?>
    <div id="all-categories">
        <ul>
        <?php foreach($root as $item): ?>
            <li>
                <div>
                    <div>
                        <?php echo $item['number']; ?>. <?php echo $item['category']; ?>
                    </div>
                    <div></div>
                </div>
                <?php if (isset($item['products'])): ?>
                    <ul>
                    <?php foreach($item['products'] as $product): ?>
                        <li>
                            <div>
                                <div><?php echo $product['number']; ?>. <?php echo $product['name']; ?></div>
                                <div>
                                    <a href="<?php echo $product['up']; ?>" title="Вверх">Вверх</a>
                                    <a href="<?php echo $product['down']; ?>" title="Вниз">Вниз</a>
                                    <a href="<?php echo $product['edit']; ?>" title="Редактировать">Ред.</a>
                                    <a href="<?php echo $product['remove']; ?>" title="Удалить">Удл.</a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/rating/root/center.php -->
