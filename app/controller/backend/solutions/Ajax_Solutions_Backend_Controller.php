<?php
/**
 * Класс Ajax_Solutions_Backend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Solutions_Backend_Model, административная
 * часть сайта. Ответ содержит информацию о товаре каталога
 */
class Ajax_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    /**
     * информация о товаре каталога
     */
    private $product = array();

    public function __construct($params = null) {
        if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // если не передан код товара или код товара не число
        if ( ! (isset($this->params['code']) && preg_match('~^\d{6}$~', $this->params['code'])) ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            die();
        }

        $product = $this->solutionsBackendModel->getProductByCode($this->params['code']);
        if (!empty($product)) {
            $this->product = $product;
        }

    }

    public function sendHeaders() {
        header('Content-type: application/json; charset=utf-8');
    }

    public function getPageContent() {
        return json_encode($this->product);
    }

}