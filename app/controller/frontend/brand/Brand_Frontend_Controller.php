<?php
/**
 * Абстрактный класс Brand_Frontend_Controller, родительский для всех
 * контроллеров, работающих с брендами, общедоступная часть сайта
 */
abstract class Brand_Frontend_Controller extends Frontend_Controller {
    
    /**
     * экземпляр класса модели для работы с брендами
     */
    protected $brandFrontendModel;

    public function __construct($params = null) {

        parent::__construct($params);
        
        // экземпляр класса модели для работы с брендами
        $this->brandFrontendModel =
            isset($this->register->brandFrontendModel) ? $this->register->brandFrontendModel : new Brand_Frontend_Model();
           
        // получаем из настроек значения для мета-тегов 
        $this->title = $this->config->meta->brand->title;
        $this->keywords = $this->config->meta->brand->keywords;
        $this->description = $this->config->meta->brand->description;

    }

}
