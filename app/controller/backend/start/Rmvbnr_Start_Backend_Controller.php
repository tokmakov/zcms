<?php
/**
 * Класс Rmvbnr_Start_Backend_Controller отвечает за удаление баннера с витрины,
 * взаимодействует с моделью Start_Backend_Model, административная часть сайта
 */
class Rmvbnr_Start_Backend_Controller extends Start_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление баннера и редирект.
     */
    protected function input() {
        // если передан id баннера и id баннера целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->startBackendModel->removeBanner($this->params['id']);
        }
        $this->redirect($this->startBackendModel->getURL('backend/start/index'));
    }

}