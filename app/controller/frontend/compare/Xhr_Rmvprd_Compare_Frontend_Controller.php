<?php
/**
 * Класс Xhr Rmvdprd_Compare_Frontend_Controller принимает запрос XmlHttpRequest,
 * удаляет товар из списка сравнения, работает с моделью Compare_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Rmvprd_Compare_Frontend_Controller extends Compare_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
        // не использовать кэширование шаблона списка товаров для сравнения
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

        // удаляем товар из списка сравнения
        $this->compareFrontendModel->removeFromCompare($product_id);

        // получаем от модели массив товаров для сравнения (для правой колонки)
        $sideCompareProducts = $this->compareFrontendModel->getSideCompareProducts();

        // получаем html-код списка товаров, отложенных для сравнения (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/compare/xhr/compare.php',
            array(
                'sideCompareProducts' => $sideCompareProducts,
                'indexCompareURL'     => $this->compareFrontendModel->getURL('frontend/compare/index'),
                'clearCompareURL'     => $this->compareFrontendModel->getURL('frontend/compare/clear'),
            )
        );
    }

}