<?php
/**
 * Абстрактный класс Page_Frontend_Controller, родительский для всех
 * контроллеров, работающих со страницами, общедоступная часть сайта
 */
abstract class Page_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }
    
    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Page_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Page_Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения для мета-тегов 
        $this->title = $this->config->meta->page->title;
        $this->keywords = $this->config->meta->page->keywords;
        $this->description = $this->config->meta->page->description;
        
    }

}
