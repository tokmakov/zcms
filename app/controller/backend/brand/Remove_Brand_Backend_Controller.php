<?php
/**
 * Класс Remove_Brand_Backend_Controller отвечает за удаление бренда, взаимодействует
 * с моделью Brand_Backend_Model, административная часть сайта
 */
class Remove_Brand_Backend_Controller extends Brand_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление бренда и редирект.
     */
    protected function input() {
        // если передан id бренда и id бренда целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->brandBackendModel->removeBrand($this->params['id']);
        }
        $this->redirect($this->brandBackendModel->getURL('backend/brand/index'));
    }

}