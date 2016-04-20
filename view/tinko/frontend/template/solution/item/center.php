<?php
/**
 * Страница отдельного типового решения,
 * файл view/example/frontend/template/solutions/item/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор типового решения
 * $pdfURL - URL ссылки для скачивания PDF-файла
 * $imgURL - URL ссылки на файл изображения
 * $products - массив товаров типового решения
 * $units - единицы измерения
 * $content1 - основное содержание типового решения
 * $content2 - дополнительное содержание типового решения (заключение)
 * $action - атрибут action тега form для добавления товаров в корзину
 *
 * $units = Array (
 *   0 => '-',
 *   1 => 'шт',
 *   2 => 'компл',
 *   3 => 'упак',
 *   4 => 'метр',
 *   5 => 'пара'
 * );
 * 
 * $products = Array (
 *   [0] => Array (
 *     [id] => 3
 *     [name] => Объектовое оборудование
 *     [amount] => 11027.25
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 225676
 *         [code] => 225676
 *         [name] => RS-200TP-RB
 *         [title] => Прибор объектовый со встроенным радиопередатчиком
 *         [shortdescr] => Объект.радиоканал.прибор системы Риф Стринг-200 433.92МГц...
 *         [price] => 5510.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 5510.00
 *         [note] => 0
 *         [sortorder] => 1
 *         [empty] => 0
 *         [url] => http://www.host.ru/catalog/product/225676
 *       )
 *       [1] => Array (
 *         [id] => 224210
 *         [code] => 224210
 *         [name] => Риф-КТМ-N
 *         [title] => Клавиатура кодовая
 *         [shortdescr] => Клавиатура без подсветки, питание по шлейфу ТМ, I-потр. до 400 мкА...
 *         [price] => 1240.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 1240.00
 *         [note] => 0
 *         [sortorder] => 2
 *         [empty] => 0
 *         [url] => http://www.host.ru/catalog/product/224210
 *       )
 *       [2] => Array (
 *         ..........
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 4
 *     [name] => Пультовое оборудование
 *     [amount] => 21132.81
 *     [products] => Array (
 *       [0] => Array (
 *         [id] => 206633
 *         [code] => 206633
 *         [name] => RS-200PN
 *         [title] => Пульт централизованного наблюдения
 *         [shortdescr] => ПЦН, 300 объектов, работа с передатчиками систем RR-701, RS-200...
 *         [price] => 16000.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 16000.00
 *         [note] => 0
 *         [sortorder] => 1
 *         [empty] => 0
 *         [url] => http://www.host.ru/catalog/product/206633
 *       )
 *       [1] => Array (
 *         [id] => 20124
 *         [code] => 020124
 *         [name] => RS-200RD
 *         [title] => Устройство радиоприемное
 *         [shortdescr] => Приемник внешний для ПЦН RS-200P и внешней антенны...
 *         [price] => 2970.00
 *         [unit] => 1
 *         [count] => 1
 *         [cost] => 2970.00
 *         [note] => 0
 *         [sortorder] => 2
 *         [empty] => 0
 *         [url] => http://www.host.ru/catalog/product/20124
 *       )
 *       [2] => Array (
 *         ..........
 *       )
 *     )
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/solutions/item/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<div id="item-solutions">

    <?php if (!empty($pdfURL)): ?>
        <a href="<?php echo $pdfURL; ?>" id="download-solution" title="Скачать" target="_blank">
            <i class="fa fa-file-pdf-o"></i>
        </a>
    <?php endif; ?>

    <?php echo $content1; ?>

    <?php if (!empty($imgURL)): ?>
        <div id="image-solution">
            <a href="<?php echo $imgURL; ?>" class="zoom"><img src="<?php echo $imgURL; ?>" alt="" /></a>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo $action; ?>" method="post">
        <h2>Комплект оборудования</h2>
        <input type="submit" name="submit" value="Добавить в корзину" />
    </form>

    <?php if (!empty($products)): ?>
        <table>
            <tr>
                <th>№</th>
                <th>Код</th>
                <th>Наименование</th>
                <th>Кол.</th>
                <th>Цена</th>
                <th>Ед.изм.</th>
                <th>Стоим.</th>
            </tr>
            <?php $amount = 0.0; ?>
            <?php foreach($products as $value) : ?>
                <?php if (isset($products[1])): /* если групп товаров в типовом решении больше одной */ ?>
                    <tr>
                        <th colspan="7"><?php echo $value['name']; ?></th>
                    </tr>
                <?php endif; ?>
                <?php foreach ($value['products'] as $item): ?>
                    <tr>
                        <td><?php echo $item['sortorder']; ?></td>
                        <?php if ( ! $item['empty']) : ?>
                            <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                        <?php else: ?>
                            <td><?php echo $item['code']; ?></td>
                        <?php endif; ?>
                        <td>
                            <span><?php echo $item['name']; ?></span>
                            <div>
                                <span><?php echo $item['title']; ?></span>
                                <span><?php echo nl2br($item['shortdescr']); ?></span>
                            </div>
                        </td>
                        <td><?php echo $item['count']; ?><?php echo $item['note'] ? '*' : ''; ?></td>
                        <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                        <td><i class="fa fa-rub"></i>/<?php echo $units[$item['unit']]; ?></td>
                        <td><?php echo number_format($item['cost'], 2, '.', ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7">
                        <strong><?php echo number_format($value['amount'], 2, '.', ' '); ?></strong> руб.
                    </td>
                </tr>
                <?php $amount = $amount + $value['amount']; ?>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php echo $content2; ?>

</div>

<!-- Конец шаблона view/example/frontend/template/solutions/item/center.php -->


