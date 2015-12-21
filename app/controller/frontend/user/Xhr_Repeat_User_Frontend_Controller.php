<?php
/**
 * Класс Xhr_Repeat_User_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет товары ранее сделанного заказа в корзину, работает с моделью
 * User_Frontend_Model, общедоступная часть сайта
 */
class Xhr_Repeat_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {
        
        // если пользователь не авторизован
        if (!$this->authUser) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // добавляем товары в корзину
        $this->userFrontendModel->repeatOrder($this->params['id']);

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // получаем html-код товаров в корзине (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/basket/xhr/basket.php',
            array(
                'sideBasketProducts'  => $sideBasketProducts,
                'sideBasketTotalCost' => $sideBasketTotalCost,
                'basketUrl'           => $this->basketFrontendModel->getURL('frontend/basket/index'),
                'checkoutUrl'         => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            )
        );

    }

}