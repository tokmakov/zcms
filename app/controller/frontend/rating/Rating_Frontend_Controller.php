<?php
/**
 * Абстрактный класс Rating_Frontend_Controller, родительский для всех
 * контроллеров, работающих с рейтингом продаж, общедоступная часть сайта
 */
abstract class Rating_Frontend_Controller extends Frontend_Controller {
    
    /**
     * экземпляр класса модели для работы с рейтингом продаж
     */
    protected $ratingFrontendModel;

    public function __construct($params = null) {

        parent::__construct($params);

        // экземпляр класса модели для работы с рейтингом продаж
        $this->ratingFrontendModel =
            isset($this->register->ratingFrontendModel) ? $this->register->ratingFrontendModel : new Rating_Frontend_Model();

    }
    
    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Rating_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Rating_Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения для мета-тегов
        $this->title = $this->config->meta->rating->title;
        $this->keywords = $this->config->meta->rating->keywords;
        $this->description = $this->config->meta->rating->description;
        
    }

}