<?php
/**
 * Класс Xhr_Customer_User_Frontend_Controller формирует ответ на запрос XmlHttpRequest в
 * формате JSON, получает данные от модели User_Frontend_Model, общедоступная часть сайта.
 * Ответ содержит данные последнего заказа не зарегистрированного пользователя, чтобы
 * использовать их для заполнения формы офрмления нового заказа
 */
class Xhr_Customer_User_Frontend_Controller extends User_Frontend_Controller {

    /**
     * данные последнего заказа пользователя
     */
    private $profile = array();

    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {
        $this->profile = $this->userFrontendModel->getLastOrderData();
    }

    public function sendHeaders() {
        header('Content-type: application/json; charset=utf-8');
    }

    public function getPageContent() {
        return json_encode($this->profile);
    }

}