<?php
/**
 * Правая колонка, файл view/example/frontend/template/right.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $basketProducts - массив товаров в корзине
 * $basketTotalCost - общая стоимость товаров в корзине
 * $basketUrl - URL страницы с корзиной
 * $checkoutUrl - URL страницы с формой для оформления заказа
 * $wishedProducts - массив последних 10 отложенных товаров
 * $wishedUrl - URL страницы со списком всех отложенных товаров
 * $comparedProducts - массив последних 10 товаров, отложенных для сравнения
 * $comparedUrl - URL страницы со списком всех товаров, отложенных для сравнения
 * $viewedProducts - массив последних 10 просмотренных товаров
 * $viewedUrl - URL страницы со списком всех просмотренных товаров
 *
 * $authUser - пользователь авторизован?
 *  - если авторизован, доступны переменные
 *     $userIndexUrl - URL ссылки на страницу личного кабинета
 *     $userEditUrl - URL ссылки на страницу с формой для редактирования личных данных
 *     $userProfilesUrl - URL ссылки на страницу со списком всех профилей
 *     $userOrdersUrl - URL ссылки на страницу со списком всех заказов
 *     $userLogoutUrl - URL ссылки для выхода из личного кабинета
 *  - если не авторизован, доступны переменные
 *     $action - атрибут action тега form формы для авторизации пользователя
 *     $regFormUrl - URL ссылки на страницу регистрации
 *     $forgotFormUrl - URL ссылки на страницу восстановления пароля
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/right.php -->

<div class="side-block">
    <div><i class="fa fa-user"></i>&nbsp;&nbsp;Личный кабинет</div>
    <?php if ($authUser): ?>
        <div>
            <ul id="logged-user-right">
                <li><a href="<?php echo $userIndexUrl; ?>">Личный кабинет</a></li>
                <li><a href="<?php echo $userEditUrl; ?>">Личные данные</a></li>
                <li><a href="<?php echo $userProfilesUrl; ?>">Ваши профили</a></li>
                <li><a href="<?php echo $userOrdersUrl; ?>">История заказов</a></li>
                <li><a href="<?php echo $userLogoutUrl; ?>">Выйти</a></li>
            </ul>
        </div>
    <?php else: ?>
        <div>
            <form action="<?php echo $action; ?>" method="post" id="login-user-right">
                <input type="text" name="email" maxlength="32" value="" placeholder="Введите e-mail" />
                <input type="text" name="password" maxlength="32" value="" placeholder="Введите пароль" />
                <label><input type="checkbox" name="remember" value="1" /> <span>запомнить меня</span></label>
                <input type="submit" name="submit" value="Войти" />
            </form>
            <ul id="reg-forgot-right">
                <li><a href="<?php echo $regFormUrl; ?>">Регистрация</a></li>
                <li><a href="<?php echo $forgotFormUrl; ?>">Забыли пароль?</a></li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<div class="side-block">
    <div><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;Ваша корзина</div>
    <div class="no-padding">
        <div id="side-basket">
        <?php if (!empty($basketProducts)): /* покупательская корзина */ ?>
            <table>
                <tr>
                    <th width="20%">Код</th>
                    <th width="65%">Наименование</th>
                    <th width="15%">Кол.</th>
                </tr>
                <?php foreach ($basketProducts as $item): ?>
                    <tr>
                        <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;">Итого: <span><?php echo number_format($basketTotalCost, 2, '.', ''); ?></span> руб.</td>
                </tr>
            </table>
            <ul id="goto-basket-checkout">
                <li><a href="<?php echo $basketUrl; ?>">Перейти в корзину</a></li>
                <li><a href="<?php echo $checkoutUrl; ?>">Оформить заказ</a></li>
            </ul>
        <?php else: ?>
            <p class="empty-list-right">Ваша корзина пуста</p>
        <?php endif; ?>
        </div>
    </div>
</div>

<div class="side-block">
    <div><i class="fa fa-balance-scale"></i>&nbsp;&nbsp;Сравнение товаров</div>
    <div class="no-padding">
        <div id="side-compared">
        <?php if (!empty($comparedProducts)): /* товары для сравнения */ ?>
            <table>
                <tr>
                    <th width="21%">Код</th>
                    <th width="70%">Наименование</th>
                    <th width="9%"></th>
                </tr>
                <?php foreach ($comparedProducts as $item): ?>
                    <tr>
                        <td><a href="<?php echo $item['url']; ?>"><?php echo $item['code']; ?></a></td>
                        <td><?php echo $item['name']; ?></td>
                        <td>
                            <form action="http://www.host2.ru/compared/rmvprd" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="return" value="compared">
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
        </div>
    </div>
</div>

<div style="margin-top: 20px;">
    <img src="/files/banner/side-banner.jpg" alt="" />
</div>

<!-- Конец шаблона view/example/frontend/template/right.php -->

