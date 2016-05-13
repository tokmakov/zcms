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

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Brand_Frontend_Controller
     */
    public function __construct($params = null) {
        
        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Brand_Frontend_Controller
         */
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
