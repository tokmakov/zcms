<?php
/**
 * Файл view/example/frontend/template/compare/xhr/compared.php
 *
 * Переменные, которые приходят в шаблон:
 * $sideCompareProducts - массив товаров для сравнения
 * $indexCompareURL - URL ссылки на страницу со списком товаров для сравнения
 * $clearCompareURL - URL ссылки для удаления всех товаров из сравнения
 */

defined('ZCMS') or die('Access denied');
?>

<?php if (!empty($sideCompareProducts)): /* товары для сравнения*/ ?>
    <?php $count = count($sideCompareProducts); ?>
    <table data-count="<?php echo $count; ?>">
        <tr>
            <th>Код</th>
            <th>Наименование</th>
            <th><a href="<?php echo $clearCompareURL; ?>" title="Очистить список сравнения"><i class="fa fa-times"></i></a></th>
        </tr>
        <?php foreach ($sideCompareProducts as $item): ?>
            <tr>
                <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                <td><?php echo $item['name']; ?></td>
                <td>
                    <form action="<?php echo $item['action']; ?>" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="submit" title="Удалить из сравнения"><i class="fa fa-times"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if ($count > 1): ?>
        <p class="all-products"><a href="<?php echo $indexCompareURL; ?>">Перейти к сравнению</a></p>
    <?php endif; ?>
<?php else: ?>
    <p class="empty-list-right">Нет товаров для сравнения</p>
<?php endif; ?>