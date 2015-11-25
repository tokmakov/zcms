<?php
/**
 * Файл view/example/frontend/template/wished/ajax/wished.php
 *
 * Переменные, которые приходят в шаблон:
 * $sideWishedProducts - массив отложенных товаров
 * $wishedUrl - URL ссылки на страницу со списком отложенных товаров
 */

defined('ZCMS') or die('Access denied');
?>

<?php if (!empty($sideWishedProducts)): /* отложенные товары */ ?>
    <table>
        <tr>
            <th>Код</th>
            <th>Наименование</th>
            <th>Цена</th>
        </tr>
        <?php foreach ($sideWishedProducts as $item): ?>
            <tr>
                <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="all-products"><a href="<?php echo $wishedUrl; ?>">Все отложенные товары</a></p>
<?php else: ?>
    <p class="empty-list-right">Нет отложенных товаров</p>
<?php endif; ?>