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

    public function __construct($params = null) {

        parent::__construct($params);
        
        // экземпляр класса модели для работы с товарами по сниженным ценам
        $this->saleFrontendModel =
            isset($this->register->saleFrontendModel) ? $this->register->saleFrontendModel : new Sale_Frontend_Model();

    }
    
    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Sale_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Sale_Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения для мета-тегов 
        $this->title = $this->config->meta->sale->title;
        $this->keywords = $this->config->meta->sale->keywords;
        $this->description = $this->config->meta->sale->description;
        
    }

}
