<?php
/**
 * Класс Ajax_Wished_Frontend_Controller принимает запрос XmlHttpRequest,
 * добавляет товар в список отложенных (или удаляет из списка), работает
 * с моделью Wished_Frontend_Model, общедоступная часть сайта
 */
class Ajax_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
        // не использовать кэширование шаблона списка отложенных товаров в
        // правой колонке, потому как вероятность, что у двух пользователей
        // совпадут списки отложенных товаров, довольно мала
        $this->notUseCache = true;
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // добавляем товар в список отложенных или удаляем?
        if ( ! (isset($this->params['action']) && in_array($this->params['action'], array('addprd', 'rmvprd'))) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        if ('addprd' == $this->params['action']) {
            // добавляем товар в список отложенных
            $this->wishedFrontendModel->addToWished($_POST['product_id']);
        } else {
            // удаляем товар из списка отложенных
            $this->wishedFrontendModel->removeFromWished($_POST['product_id']);
        }

        // получаем от модели массив отложенных товаров (для правой колонки)
        $sideWishedProducts = $this->wishedFrontendModel->getSideWishedProducts();

        // получаем html-код отложенных товаров (для правой колонки)
        $this->pageContent = $this->render(
            $this->config->site->theme . '/frontend/template/wished/ajax/wished.php',
            array(
                'sideWishedProducts'  => $sideWishedProducts,
                'wishedUrl'           => $this->wishedFrontendModel->getURL('frontend/wished/index'),
            )
        );
    }

}