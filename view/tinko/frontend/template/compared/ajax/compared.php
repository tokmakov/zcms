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
            <th>Код</th>
            <th>Наименование</th>
            <th></th>
        </tr>
        <?php foreach ($sideComparedProducts as $item): ?>
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
    <p class="all-products"><a href="<?php echo $comparedUrl; ?>">Перейти к сравнению</a></p>
<?php else: ?>
    <p class="empty-list-right">Нет товаров для сравнения</p>
<?php endif; ?>