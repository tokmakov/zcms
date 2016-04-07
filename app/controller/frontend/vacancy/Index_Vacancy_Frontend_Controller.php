<?php
/**
 * Класс Index_Vacancy_Frontend_Controller формирует список партнеров компании,
 * получает данные от модели Vacancy_Frontend_Model, общедоступная часть сайта
 */
class Index_Vacancy_Frontend_Controller extends Vacancy_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком вакансий компании
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Vacancy_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Vacancy_Frontend_Controller
         */
        parent::input();
        
        $this->title = 'Вакансии. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/vacancy/index');

        // получаем от модели массив всех вакансий компании
        $vacancies = $this->vacancyFrontendModel->getAllVacancies();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех вакансий компании
            'vacancies'   => $vacancies,
        );

    }

}