<?php
/**
 * Абстрактный класс Sale_Frontend_Controller, родительский для всех
 * контроллеров, работающих с товарами по сниженным ценам, общедоступная
 * часть сайта
 */
abstract class Sale_Frontend_Controller extends Frontend_Controller {
    
    /**
     * экземпляр класса модели для работы с товарами по сниженным ценам
     */
    protected $saleFrontendModel;

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Sale_Frontend_Controller
     */
    public function __construct($params = null) {
        
        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Sale_Frontend_Controller
         */
        parent::__construct($params);
        
        // экземпляр класса модели для работы с товарами по сниженным ценам
        $this->saleFrontendModel =
            isset($this->register->saleFrontendModel) ? $this->register->saleFrontendModel : new Sale_Frontend_Model();
           
        // получаем из настроек значения для мета-тегов 
        $this->title = $this->config->meta->sale->title;
        $this->keywords = $this->config->meta->sale->keywords;
        $this->description = $this->config->meta->sale->description;

    }

}
