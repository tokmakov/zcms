<?php
/**
 * Покупательская корзина, общедоступная часть сайта,
 * файл view/example/frontend/template/basket/index/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $basketProducts - массив товаров в корзине
 * $amount - полная стоимость товаров в корзине без учета скидки
 * $userAmount - полная стоимость товаров в корзине с учетом скидки
 * $checkoutUrl - URL ссылки оформления заказа
 * $units - массив единиц измерения товара
 * $type - тип пользователя
 * $recommendedProducts - массив рекомендованных товаров
 */

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
                <th>Код</th>
                <th>Наименование</th>
                <th>Кол.</th>
                <th>Цена</th>
                <th>Стоим.</th>
                <th>Удл.</th>
            </tr>
            <?php foreach($basketProducts as $item): ?>
                <tr>
                    <td><?php echo $item['code']; ?></td>
                    <td><a href="<?php echo $item['url']['product']; ?>"><?php echo $item['name']; ?></a></td>
                    <td><input type="text" name="ids[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" size="3" /></td>
                    <td><?php echo number_format($item['user_price'], 2, '.', ''); ?></td>
                    <td><?php echo number_format($item['user_cost'], 2, '.', ''); ?></td>
                    <td><a href="<?php echo $item['url']['remove']; ?>" title="Удалить">Удл.</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($type > 1): ?>
                <tr><td colspan="6" class="note-user-price">Цены и стоимость заказа указаны с учетом скидки</td></tr>
            <?php endif; ?>
        </table>
        <div>
            <span><input type="submit" name="submit" value="Пересчитать" /></span>
            <span>
                <?php if ($type > 1): ?>
                    <strong>&nbsp;<?php echo number_format($amount, 2, '.', ''); ?>&nbsp;</strong>
                <?php endif; ?>
                <strong><?php echo number_format($userAmount, 2, '.', ''); ?></strong>
                руб.
            </span>
        </div>
        <p><a href="<?php echo $checkoutUrl; ?>">Оформить заказ</a></p>
    </form>
<?php else: ?>
    <p>Ваша корзина пуста</p>
<?php endif; ?>
</div>

<?php if (!empty($recommendedProducts)): // рекомендованные товары ?>
    <div class="center-block" id="basket-related">
        <div><h2>С этими товарами покупают</h2></div>
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


