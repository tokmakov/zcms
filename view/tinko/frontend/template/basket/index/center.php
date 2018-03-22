<?php
/**
 * Покупательская корзина, общедоступная часть сайта,
 * файл view/example/frontend/template/basket/index/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $allBaskets - массив всех корзин
 * $thisPageURL - URL этой страницы
 * $action - атрибут action тега form
 * $clearBasketURL - ссылка для удаления всех товаров из корзины
 * $basketProducts - массив товаров в корзине
 * $amount - полная стоимость товаров в корзине без учета скидки
 * $userAmount - полная стоимость товаров в корзине с учетом скидки
 * $checkoutURL - URL ссылки оформления заказа
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

<ul id="all-baskets">
<?php foreach($allBaskets as $basket): ?>
    <li>
        <?php if ($basket['current']): ?>
            <span class="selected"><?php echo $basket['name'] ?></span>
        <?php else: ?>
            <a href="<?php echo $thisPageURL; ?>"><?php echo $basket['name'] ?></a>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<div id="basket">
<?php if (!empty($basketProducts)): ?>
    <!-- товары в корзине, центральная колонка -->
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
                <th></th>
            </tr>
            <?php foreach($basketProducts as $item): ?>
                <tr>
                    <td><?php echo $item['code']; ?></td>
                    <td><a href="<?php echo $item['url']['product']; ?>"><?php echo $item['name']; ?></a></td>
                    <td><input type="text" name="ids[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" /></td>
                    <td><?php echo $item['user_price'] > 1000000 ? number_format(round(($item['user_price']/1000000),2), 2, '.', '').' млн' : number_format($item['user_price'], 2, '.', ''); ?></td>
                    <td><?php echo $item['user_cost'] > 1000000 ? number_format(round(($item['user_cost']/1000000),2), 2, '.', '').' млн' : number_format($item['user_cost'], 2, '.', ''); ?></td>
                    <td><a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-times"></i></a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($type > 1): ?>
                <tr><td colspan="6" class="note-user-price">Цены и стоимость заявки указаны с учетом скидки</td></tr>
            <?php endif; ?>
        </table>
        <div>
            <span><input type="submit" name="submit" value="Пересчитать" /></span>
            <span>
                <?php if ($type > 1): ?>
                    <strong>&nbsp;<?php echo $amount > 1000000 ? number_format(round(($amount/1000000),3), 3, '.', '').' млн.' : number_format($amount, 2, '.', ' '); ?>&nbsp;</strong>
                <?php endif; ?>
                <strong><?php echo $userAmount > 1000000 ? number_format(round(($userAmount/1000000),3), 3, '.', '').' млн.' : number_format($userAmount, 2, '.', ' '); ?></strong>
                руб.
            </span>
        </div>
        <p><a href="<?php echo $checkoutURL; ?>">Оформить заявку</a></p>
    </form>
<?php else: ?>
    <!-- пустая корзина, центральная колонка -->
    <p>Ваша корзина пуста</p>
<?php endif; ?>
</div>

<div id="upsell">
<?php if ( ! empty($recommendedProducts)): // рекомендованные товары ?>
    <div class="center-block"> <!-- с этими товарами покупают -->
        <div><h3>С этими товарами покупают</h3></div>
        <div class="no-padding">
            <div class="product-list-upsell">
                <?php foreach($recommendedProducts as $product): ?>
                    <div><div>
                        <div class="product-upsell-heading">
                            <h3><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h3>
                        </div>
                        <div class="product-upsell-image">
                            <a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
                        </div>
                        <div class="product-upsell-price">
                            <span><?php echo number_format($product['price'], 2, '.', ' '); ?></span> <i class="fa fa-rub"></i>/<?php echo $units[$product['unit']]; ?>
                        </div>
                        <div class="product-upsell-basket">
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
</div>

<!-- Конец шаблона view/example/frontend/template/basket/index/center.php -->
