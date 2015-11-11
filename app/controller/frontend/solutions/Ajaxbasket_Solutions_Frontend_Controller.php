<?php
/**
 * Класс Ajaxbasket_Solutions_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет все товары типового решения в корзину, работает с моделью
 * Solutions_Frontend_Model, общедоступная часть сайта
 */
class Ajaxbasket_Solutions_Frontend_Controller extends Solutions_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // добавляем товары в корзину
        $this->solutionsFrontendModel->AddSolutionToBasket($this->params['id']);

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();

        // общая стоимость товаров в корзине
        $sideBasketTotalCost = $this->basketFrontendModel->getTotalCost();

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