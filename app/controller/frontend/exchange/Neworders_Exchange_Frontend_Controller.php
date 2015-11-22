<?php
/**
 * Класс Neworders_Exchange_Frontend_Controller формирует список новых заказов в магазине в
 * формате XML, получает данные от модели Exchange_Frontend_Model, общедоступная часть сайта
 */
class Neworders_Exchange_Frontend_Controller extends Exchange_Frontend_Controller {

    /**
     * список новых заказов в магазине в формате XML
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // получаем от модели массив новых заказов в магазине
        $orders = $this->exchangeFrontendModel->getNewOrders();

        /*
         * <orders>
         *   <order id="34"/>
         *   <order id="35"/>
         *   <order id="39"/>
         *   <order id="40"/>
         *   <order id="41"/>
         * </orders>
         */

        // создаём XML-документ
        $dom = new DOMDocument('1.0', 'utf-8');
        // создаём корневой элемент <orders>
        $root = $dom->createElement('orders');
        $dom->appendChild($root);
        foreach ($orders as $value) {
            // создаём узел <order>
            $order = $dom->createElement('order');
            // добавляем дочерний элемент для <orders>
            $root->appendChild($order);
            // устанавливаем атрибут id для узла <order>
            $order->setAttribute('id', $value['id']);
        }
        $this->output = $dom->saveXML();

    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-Type: text/xml; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}