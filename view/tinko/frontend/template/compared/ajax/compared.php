<?php
/**
 * Файл view/example/frontend/template/compared/ajax/compared.php
 *
 * Переменные, которые приходят в шаблон:
 * $sideComparedProducts - массив товаров для сравнения
 * $comparedUrl - URL ссылки на страницу со списком товаров для сравнения
 */

defined('ZCMS') or die('Access denied');
?>

<?php if (!empty($sideComparedProducts)): /* товары для сравнения*/ ?>
    <table>
        <tr>
            <th width="20%">Код</th>
            <th width="55%">Наименование</th>
            <th width="25%">Цена</th>
        </tr>
        <?php foreach ($sideComparedProducts as $item): ?>
            <tr>
                <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="all-products"><a href="<?php echo $comparedUrl; ?>">Все товары</a></p>
<?php else: ?>
    <p class="empty-list-right">Нет товаров для сравнения</p>
<?php endif; ?>