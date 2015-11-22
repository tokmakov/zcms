<?php
/**
 * Класс Setstatus_Exchange_Frontend_Controller устанавливает статус заказа, работает
 * с моделью Exchange_Frontend_Model, общедоступная часть сайта
 */
class Setstatus_Exchange_Frontend_Controller extends Exchange_Frontend_Controller {

    private $output = 'OK';


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['order']) && ctype_digit($this->params['order'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['order'] = (int)$this->params['order'];
        }

        // если не передано новое значение статуса заказа или значение статуса не число
        if ( ! (isset($this->params['status']) && ctype_digit($this->params['status'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['status'] = (int)$this->params['status'];
        }

        // проверяем существование заказа
        if ( ! $this->exchangeFrontendModel->isOrderExists($this->params['order'])) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // изменяем статус заказа
        $this->exchangeFrontendModel->setOrderStatus($this->params['order'], $this->params['status']);
    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}
