<?php
/**
 * Покупательская корзина: центральная и правая колонка,
 * файл view/example/frontend/template/basket/xhr/basket.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $sideBasketProducts - товары в корзине (правая колонка)
 * $sideBasketTotalCost - общая стоимость товаров в корзине (правая колонка)
 * $thisPageURL - URL страницы с корзиной
 * $checkoutURL - URL страницы с формой для оформления заказа
 *
 * $action - атрибут action тега form
 * $clearBasketURL - ссылка для удаления всех товаров из корзины
 * $basketProducts - массив товаров в корзине (центральная колонка)
 * $amount - полная стоимость товаров в корзине без учета скидки (центральная колонка)
 * $userAmount - полная стоимость товаров в корзине с учетом скидки (центральная колонка)
 * $units - массив единиц измерения товара
 * $type - тип пользователя
 *
 * $recommendedProducts - массив рекомендованных товаров
 */

defined('ZCMS') or die('Access denied');
?>

<?php if ( ! empty($sideBasketProducts)): /* покупательская корзина */ ?>
    <table>
        <tr>
            <th width="20%">Код</th>
            <th width="65%">Наименование</th>
            <th width="15%">Кол.</th>
        </tr>
        <?php foreach ($sideBasketProducts as $item): ?>
            <tr>
                <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><span><?php echo number_format($sideBasketTotalCost, 2, '.', ' '); ?></span> руб.</td>
        </tr>
    </table>
    <ul id="goto-basket-checkout">
        <li><a href="<?php echo $thisPageURL; ?>">Перейти в корзину</a></li>
        <li><a href="<?php echo $checkoutURL; ?>">Оформить заказ</a></li>
    </ul>
<?php else: ?>
    <p class="empty-list-right">Ваша корзина пуста</p>
<?php endif; ?>
¤
<?php if (!empty($basketProducts)): ?>
    <a href="<?php echo $clearBasketURL; ?>">
        <i class="fa fa-trash-o"></i>&nbsp; <span>Очистить корзину</span>
    </a>
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
                    <strong>&nbsp;<?php echo number_format($amount, 2, '.', ' '); ?>&nbsp;</strong>
                <?php endif; ?>
                <strong><?php echo number_format($userAmount, 2, '.', ' '); ?></strong>
                руб.
            </span>
        </div>
        <p><a href="<?php echo $checkoutURL; ?>">Оформить заказ</a></p>
    </form>
<?php else: ?>
    <p>Ваша корзина пуста</p>
<?php endif; ?>
¤
<?php if (!empty($recommendedProducts)): // рекомендованные товары ?>
    <div class="center-block">
        <div><h3>С этими товарами покупают</h3></div>
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
                                <span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <?php echo $units[$product['unit']]; ?>
                            </div>
                            <div class="product-grid-basket">
                                <form action="<?php echo $product['action']; ?>" method="post" class="upsell-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                    <input type="submit" name="submit" value="В корзину" />
                                </form>
                            </div>
                        </div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif ?>
