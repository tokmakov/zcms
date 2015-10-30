<?php
/**
 * Класс Remove_User_Backend_Controller отвечает за удаление пользователя,
 * взаимодействует с моделью User_Backend_Model, административная часть сайта
 */
class Remove_User_Backend_Controller extends User_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только удаление пользователя и редирект.
     */
    protected function input() {
        // если передан id пользователя и id пользователя целое положетельное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->userBackendModel->removeUser($this->params['id']);
        }
        $this->redirect($this->userBackendModel->getURL('backend/user/index'));
    }

}