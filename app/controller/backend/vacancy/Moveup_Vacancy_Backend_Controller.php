<?php
/**
 * Класс Moveup_Vacancy_Backend_Controller поднимает вакансию в списке вверх,
 * взаимодействует с моделью Vacancy_Backend_Model, административная часть сайта
 */
class Moveup_Vacancy_Backend_Controller extends Vacancy_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только поднять вакансию в списке вверх и сделать редирект.
     */
    protected function input() {
        // если передан id вакансии и id вакансии целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->vacancyBackendModel->moveVacancyUp($this->params['id']);
        }
        $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/index'));
    }
}