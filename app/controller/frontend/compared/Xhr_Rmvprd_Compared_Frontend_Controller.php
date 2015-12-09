<?php
/**
 * Класс Xhr Rmvdprd_Compared_Frontend_Controller принимает запрос XmlHttpRequest,
 * удаляет товар из списка сравнения, работает с моделью Compared_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Rmvprd_Compared_Frontend_Controller extends Compared_Frontend_Controller {

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

        // удаляем товар из списка сравнения
        $this->comparedFrontendModel->removeFromCompared($product_id);

        // получаем от модели массив товаров для сравнения (для правой колонки)
        $sideComparedProducts = $this->comparedFrontendModel->getSideComparedProducts();

        // получаем html-код списка товаров, отложенных для сравнения (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/compared/xhr/compared.php',
            array(
                'sideComparedProducts'  => $sideComparedProducts,
                'comparedUrl'           => $this->comparedFrontendModel->getURL('frontend/compared/index'),
            )
        );
    }

}