<?php
/**
 * Абстрактный класс Exchange_Frontend_Controller, родительский для всех контроллеров,
 * осуществляющих обмен данными с 1С, общедоступная часть сайта
 */
abstract class Exchange_Frontend_Controller extends Base {

    /**
     *  параметры, передаваемые контроллеру
     */
    protected $params;

    /**
     * экземпляр класса модели для обмена данными с 1С
     */
    protected $exchangeFrontendModel;


    public function __construct($params = null) {
        parent::__construct();
        // параметры, передававаемые контроллеру
        $this->params = $params;
        if ( ! (isset($this->params['access']) && preg_match('~^[a-f0-9]{32}$~', $this->params['access']))) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        if ( ! is_file('files/exchange/access.txt')) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        $access = file_get_contents('files/exchange/access.txt');
        if ($access !== $this->params['access']) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        // экземпляр класса модели для обмена данными с 1С
        $this->exchangeFrontendModel = new Exchange_Frontend_Model();
    }

    public function isNotFoundRecord() {
        return false;
    }

}