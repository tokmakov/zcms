<?php
/**
 * Класс Logout_Admin_Backend_Controller отвечает за выход администратора сайта
 * из административной части, работает с моделью Admin_Backend_Model,
 * административная часть сайта
 */
class Logout_Admin_Backend_Controller extends Admin_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только выход и редирект.
     */
    protected function input() {

        // если администратор авторизован, выходим из админки
        if ($this->authAdmin) {
            $this->adminBackendModel->logoutAdmin();
        }
        // перенаправляем на страницу авторизации администратора
        $this->redirect($this->adminBackendModel->getURL('backend/admin/login'));

    }
}
