<?php
/**
 * Класс Xhr_Addprd_Wished_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет товар в список избанных, работает с моделью Wished_Frontend_Model,
 * общедоступная часть сайта
 */
class Xhr_Addprd_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // не использовать кэширование шаблона списка отложенных товаров
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

        // добавляем товар в список отложенных
        $this->wishedFrontendModel->addToWished($product_id);

        // получаем от модели массив избанных товаров (для правой колонки)
        $sideWishedProducts = $this->wishedFrontendModel->getSideWishedProducts();

        // получаем html-код избранных товаров (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/wished/xhr/wished.php',
            array(
                'sideWishedProducts'  => $sideWishedProducts,
                'wishedUrl'           => $this->wishedFrontendModel->getURL('frontend/wished/index'),
            )
        );

    }

}