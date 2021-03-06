<?php
/**
 * Класс Xhr_Basket_Solution_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет все товары типового решения в корзину, работает с моделью
 * Solution_Frontend_Model, общедоступная часть сайта; ответ содержит HTML
 * содержимого корзины в правой колонке
 */
class Xhr_Basket_Solution_Frontend_Controller extends Solution_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // добавляем товары в корзину
        $this->solutionFrontendModel->AddSolutionToBasket($this->params['id']);

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // получаем html-код товаров в корзине (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/basket/xhr/side-basket.php',
            array(
                'sideBasketProducts'  => $sideBasketProducts,
                'sideBasketTotalCost' => $sideBasketTotalCost,
                'basketUrl'           => $this->solutionFrontendModel->getURL('frontend/basket/index'),
                'checkoutUrl'         => $this->solutionFrontendModel->getURL('frontend/basket/checkout'),
            )
        );

    }

}