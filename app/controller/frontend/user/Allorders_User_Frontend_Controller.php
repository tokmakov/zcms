<?php
/**
 * Класс Allorders_User_Frontend_Controller формирует страницу со списком заказов
 * зарегистрированного (и авторизованного) пользователя, получает данные от
 * модели User_Frontend_Model, общедоступная часть сайта
 */
class Allorders_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком заказов зарегистрированного (и авторизованного) пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allorders_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        $this->title = 'История заказов. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->userFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->userFrontendModel->getURL('frontend/user/index'), 'name' => 'Личный кабинет')
        );

        /*
         * постраничная навигация
         */
        $currentPage = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $currentPage = $this->params['page'];
        }
        // общее кол-во заказов пользователя
        $totalUsers = $this->userFrontendModel->getCountAllOrders();

        $temp = new Pager(
            $currentPage,
            $totalUsers,
            Config::getInstance()->pager->frontend->orders->perpage,
            Config::getInstance()->pager->frontend->orders->leftright
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
        $start = ($currentPage - 1) * Config::getInstance()->pager->frontend->orders->perpage;

        // получаем от модели массив всех заказов пользователя
        $orders = $this->userFrontendModel->getAllOrders($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив заказов пользователя
            'orders'      => $orders,
            // URL этой страницы
            'thisPageUrl' => $this->userFrontendModel->getURL('frontend/user/allorders'),
            // постраничная навигация
            'pager'       => $pager,
            // текущая страница
            'page'        => $currentPage,
        );

    }

}
