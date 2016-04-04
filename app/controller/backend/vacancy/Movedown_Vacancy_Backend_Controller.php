<?php
/**
 * Класс Movedown_Vacancy_Backend_Controller опускает вакансию в списке вниз,
 * взаимодействует с моделью Vacancy_Backend_Model, административная часть сайта
 */
class Movedown_Vacancy_Backend_Controller extends Vacancy_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только опустить вакансию в списке вниз и сделать редирект.
     */
    protected function input() {
        // если передан id вакансии и id вакансии целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->vacancyBackendModel->moveVacancyDown($this->params['id']);
        }
        $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/index'));
    }
}