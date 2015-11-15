<?php
/**
 * Класс Ajax_Basket_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет товар в корзину (или удаляет из корзины), работает с моделью
 * Basket_Frontend_Model, общедоступная часть сайта
 */
class Ajax_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
        // не использовать кэширование шаблона корзины в правой колонке,
        // потому как вероятность, что у двух покупателей совпадут
        // товары в корзинах, довольно мала
        $this->notUseCache = true;
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // добавляем товар в корзину или удаляем?
        if ( ! (isset($this->params['action']) && in_array($this->params['action'], array('addprd', 'rmvprd'))) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        if ($this->params['action'] == 'addprd') {
            // добавляем товар в корзину
            $count = 1; // кол-во товара
            if (isset($_POST['count']) && ctype_digit($_POST['count'])) {
                $count = $_POST['count'];
            }
            $this->basketFrontendModel->addToBasket($_POST['product_id'], $count);
        } else {
            // удаляем товар из корзины
            $this->basketFrontendModel->RemoveFromBasket($_POST['product_id']);
        }

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине (для правой колонки)
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // получаем html-код товаров в корзине (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/basket/ajax/basket.php',
            array(
                'sideBasketProducts'  => $sideBasketProducts,
                'sideBasketTotalCost' => $sideBasketTotalCost,
                'basketUrl'           => $this->basketFrontendModel->getURL('frontend/basket/index'),
                'checkoutUrl'         => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            )
        );
    }

}