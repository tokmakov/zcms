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
        parent::__construct($params);
        // экземпляр класса модели для работы с вакансиями компании
        $this->vacancyFrontendModel =
            isset($this->register->vacancyFrontendModel) ? $this->register->vacancyFrontendModel : new Vacancy_Frontend_Model();
    }

}
