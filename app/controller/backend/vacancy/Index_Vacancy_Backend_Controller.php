<?php
/**
 * Класс Index_Vacancy_Backend_Controller формирует страницу управления вакансиями,
 * получает данные от модели Vacancy_Backend_Model, административная часть сайта
 */
class Index_Vacancy_Backend_Controller extends Vacancy_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления вакансиями
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Vacancy_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Vacancy_Backend_Controller
         */
        parent::input();

        $this->title = 'Вакансии. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->vacancyBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем массив всех вакансий
        $vacancies = $this->vacancyBackendModel->getAllVacancies();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // массив всех вакансий
            'vacancies'     => $vacancies,
            // URL страницы с формой для добавления вакансии
            'addVacancyURL' => $this->vacancyBackendModel->getURL('backend/vacancy/add'),
        );

    }

}