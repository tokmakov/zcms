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

    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Partner_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Partner_Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения для мета-тегов
        $this->title = $this->config->meta->partner->title;
        $this->keywords = $this->config->meta->partner->keywords;
        $this->description = $this->config->meta->partner->description;

    }

}
