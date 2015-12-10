<?php
/**
 * Класс Xhr_Addprd_Compare_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет товар в список сравнения, работает с моделью Compare_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Addprd_Compare_Frontend_Controller extends Compare_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // не использовать кэширование шаблона списка товаров для сравнения
        // в правой колонке, потому как вероятность, что у двух пользователей
        // совпадут списки товаров для сравнения, довольно мала
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

        // добавляем товар в список сравнения
        $this->compareFrontendModel->addToCompare($product_id);

        // получаем от модели массив товаров для сравнения (для правой колонки)
        $sideCompareProducts = $this->compareFrontendModel->getSideCompareProducts();

        // получаем html-код списка товаров, отложенных для сравнения (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/compare/xhr/compare.php',
            array(
                'sideCompareProducts'  => $sideCompareProducts,
                'compareUrl'           => $this->compareFrontendModel->getURL('frontend/compare/index'),
            )
        );
    }

}