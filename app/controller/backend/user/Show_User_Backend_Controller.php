<?php
/**
 * Класс Show_User_Backend_Controller формирует страницу с информацией о пользователе,
 * получает данные от модели Order_Backend_Model, административная часть сайта
 */
class Show_User_Backend_Controller extends User_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с информацией о зарегистрированном пользователе сайта
     */
    protected function input() {

        // сначала обращаемся к родительскому классу User_Backend_Controller,
        // чтобы установить значения переменных, которые нужны для работы всех его
        // потомков, потом переопределяем эти переменные (если необходимо) и
        // устанавливаем значения перменных, которые нужны для работы только
        // Show_User_Backend_Controller
        parent::input();

        // если не передан id пользователя или id пользователя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = 'Информация о пользователе. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->userBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->userBackendModel->getURL('backend/user/index'), 'name' => 'Пользователи'),
        );

        // получаем от модели подробную информацию о пользователе
        $user = $this->userBackendModel->getUser($this->params['id']);
        // если запрошенный пользователь не найден в БД
        if (empty($user)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * постраничная навигация для списка товаров пользователя
         */
        $currentPage = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $currentPage = $this->params['page'];
        }
        // общее кол-во заказов пользователя
        $totalUsers = $this->userBackendModel->getCountUserOrders($this->params['id']);

        $temp = new Pager(
            $currentPage,
            $totalUsers,
            Config::getInstance()->pager->backend->orders->perpage,
            Config::getInstance()->pager->backend->orders->leftright
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $currentPage (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($currentPage - 1) * Config::getInstance()->pager->backend->orders->perpage;

        // получаем от модели массив заказов пользователя
        $orders = $this->userBackendModel->getUserOrders($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // подробная информация о пользователе
            'user' => $user,
            // массив заказов пользователя
            'orders' => $orders,
            // URL этой страницы
            'thisPageUrl' => $this->userBackendModel->getURL('backend/user/show/id/' . $this->params['id']),
            // постраничная навигация
            'pager' => $pager,
        );

    }

}