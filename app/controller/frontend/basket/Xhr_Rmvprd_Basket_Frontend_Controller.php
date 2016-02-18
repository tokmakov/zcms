<?php
/**
 * Класс Xhr_Rmvprd_Basket_Frontend_Controller принимает запрос XmlHttpRequest,
 * удаляет товар из корзины, работает с моделью Basket_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Rmvprd_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // не использовать кэширование шаблона списка товаров в корзине
        $this->notUseCache = true;
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $product_id = (int)$_POST['product_id'];
        }

        // удаляем товар из корзины
        $this->basketFrontendModel->RemoveFromBasket($product_id);

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине (для правой колонки)
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // получаем html-код товаров в корзине (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/basket/xhr/side-basket.php',
            array(
                'sideBasketProducts'  => $sideBasketProducts,
                'sideBasketTotalCost' => $sideBasketTotalCost,
                'basketUrl'           => $this->basketFrontendModel->getURL('frontend/basket/index'),
                'checkoutUrl'         => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            )
        );
    }

}