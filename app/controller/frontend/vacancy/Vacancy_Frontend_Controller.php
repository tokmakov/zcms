<?php
/**
 * Абстрактный класс Vacancy_Frontend_Controller, родительский для всех
 * контроллеров, работающих с вакансиями компании, общедоступная часть
 * сайта
 */
abstract class Vacancy_Frontend_Controller extends Frontend_Controller {
    
    /**
     * экземпляр класса модели для работы с вакансиями компании
     */
    protected $vacancyFrontendModel;

    public function __construct($params = null) {
        
        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Vacancy_Frontend_Controller
         */
        parent::__construct($params);

        // экземпляр класса модели для работы с вакансиями компании
        $this->vacancyFrontendModel =
            isset($this->register->vacancyFrontendModel) ? $this->register->vacancyFrontendModel : new Vacancy_Frontend_Model();

    }

}
