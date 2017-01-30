<?php
/**
 * Класс Login_Admin_Backend_Controller формирует страницу с формой для
 * авторизации администратора сайта или перенаправляет на главную страницу
 * панели управления сайта, получает данные от модели Admin_Backend_Model,
 * административная часть сайта
 */
class Login_Admin_Backend_Controller extends Admin_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы с формой
     * для авторизации администратора сайта (в данном случае никаких данных получать не надо)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Admin_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Login_Admin_Backend_Controller
         */
        parent::input();

        // если администратор авторизован, перенаправляем его на главную страницу
        if ($this->authAdmin) {
            $this->redirect($this->adminBackendModel->getURL('backend/index/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->loginAdmin()) { // авторизация прошла успешено
                // перенаправляем пользователя на главную страницу
                $this->redirect($this->adminBackendModel->getURL('backend/index/index'));
            } else { // неверный логин/пароль
                $this->redirect($this->adminBackendModel->getURL('backend/admin/login'));
            }
        }

        $this->title = 'Войти. ' . $this->title;

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // атрибут action тега form
            'action' => $this->adminBackendModel->getURL('backend/admin/login')
        );

    }

    protected function loginAdmin() {

        // обрабатываем данные, полученные из формы
        $name     = trim(iconv_substr($_POST['name'], 0, 32)); // имя
        $name     = preg_replace('~[^-_a-z0-9]~i', '', $name);
        $password = trim(iconv_substr($_POST['password'], 0, 32)); // пароль
        $password = preg_replace('~[^-_a-z0-9]~i', '', $password);

        // обращаемся к модели для авторизации администратора
        return $this->adminBackendModel->loginAdmin($name, $password);
    }
}