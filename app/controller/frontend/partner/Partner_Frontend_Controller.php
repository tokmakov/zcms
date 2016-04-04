<?php
/**
 * Абстрактный класс Partner_Frontend_Controller, родительский для всех
 * контроллеров, работающих с партнерами компании, общедоступная часть
 * сайта
 */
abstract class Partner_Frontend_Controller extends Frontend_Controller {
    
    /**
     * экземпляр класса модели для работы с партнерами компании
     */
    protected $partnerFrontendModel;

    public function __construct($params = null) {
        parent::__construct($params);
        // экземпляр класса модели для работы с партнерами компании
        $this->partnerFrontendModel =
            isset($this->register->partnerFrontendModel) ? $this->register->partnerFrontendModel : new Partner_Frontend_Model();
    }

}
