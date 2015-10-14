<?php
/**
 * Класс Logout_User_Frontend_Controller отвечает за выход из личного кабинета,
 * работает с моделью User_Frontend_Model, общедоступная часть сайта
 */
class Logout_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только выход и редирект.
     */
    protected function input() {

        // если пользователь авторизован, выходим из личного кабинета
        if ($this->authUser) {
            $this->userFrontendModel->logoutUser();
        }
        // перенаправляем пользователя на страницу авторизации
        $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));

    }
}
