<?php
/**
 * Класс Rmvprof_User_Frontend_Controller отвечает за удаление профиля пользователя,
 * взаимодействует с моделью User_Frontend_Model, общедоступная часть сайта
 */
class Rmvprof_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление профиля и редирект.
     */
    protected function input() {

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // если передан id профиля или id профиля целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->userFrontendModel->removeProfile($this->params['id']);
        }
        $this->redirect($this->userFrontendModel->getURL('frontend/user/allprof'));

    }
}