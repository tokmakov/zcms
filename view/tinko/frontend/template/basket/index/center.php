<?php
/**
 * Покупательская корзина, общедоступная часть сайта,
 * файл view/example/frontend/template/basket/index/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $basketProducts - массив товаров в корзине
 * $amount - полная стоимость товаров в корзине
 * $checkoutUrl - URL ссылки оформления заказа
 * $wishedProducts - массив отложенных товаров
 * $viewedProducts - массив просмотренных товаров
 */

$units = Array (
    0 => 'руб',
    1 => 'руб/шт',
    2 => 'руб/компл',
    3 => 'руб/упак',
    4 => 'руб/метр',
    5 => 'руб/метр',
    6 => 'руб/пара',
);

defined('ZCMS') or die('Access denied');

?>

<!-- Начало шаблона view/example/frontend/template/basket/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Корзина</h1>

<div id="basket">
<?php if (!empty($basketProducts)): ?>
    <form action="<?php echo $action; ?>" method="post">
        <table>
            <tr>
                <th width="10%">Код</th>
                <th width="55%">Наименование</th>
                <th width="10%">Кол.</th>
                <th width="10%">Цена</th>
                <th width="10%">Стоим.</th>
                <th width="5%">Удл.</th>
            </tr>
        <?php foreach($basketProducts as $item): ?>
            <tr>
                <td><a href="<?php echo $item['url']['product']; ?>"><?php echo $item['code']; ?></a></td>
                <td><?php echo $item['name']; ?></td>
                <td><input type="text" name="ids[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" size="3" /></td>
                <td><?php echo number_format($item['price'], 2, '.', ''); ?></td>
                <td><?php echo number_format($item['cost'], 2, '.', ''); ?></td>
                <td><a href="<?php echo $item['url']['remove']; ?>">Удл.</a></td>
            </tr>
        <?php endforeach; ?>
        </table>
        <div>
            <span><input type="submit" name="submit" value="Пересчитать" /></span>
            <span>Итого: <span><?php echo number_format($amount, 2, '.', ''); ?></span> руб.</span>
        </div>
        <p><a href="<?php echo $checkoutUrl; ?>">Оформить заказ</a></p>
    </form>
<?php else: ?>
    <p>Ваша корзина пуста</p>
<?php endif; ?>
</div>

<?php if (!empty($recommendedProducts)): // рекомендованные товары ?>
    <div class="center-block">
        <div><h2>С этими товарами часто покупают</h2></div>
        <div class="no-padding">
            <div class="products-list-grid">
                <?php foreach($recommendedProducts as $product): ?>
                    <div><div>
                        <div class="product-grid-heading">
                            <h3><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h3>
                        </div>
                        <div class="product-grid-image">
                            <a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
                        </div>
                        <div class="product-grid-price">
                            <span><?php echo number_format($product['price'], 2, '.', ''); ?></span> <?php echo $units[$product['unit']]; ?>
                        </div>
                        <div class="product-grid-basket">
                            <form action="<?php echo $product['action']; ?>" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <input type="hidden" name="return" value="basket" />
                                <input type="submit" name="submit" value="В корзину" />
                            </form>
                        </div>
                    </div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif ?>

<!-- Конец шаблона view/example/frontend/template/basket/index/center.php -->


