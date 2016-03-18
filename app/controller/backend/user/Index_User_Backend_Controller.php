<?php
/**
 * Класс Index_User_Backend_Controller формирует страницу со списком всех
 * зарегистрированных пользователей сайта, получает данные от модели
 * User_Backend_Model, административная часть сайта
 */
class Index_User_Backend_Controller extends User_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех страниц зарегистрированных пользователей сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_User_Backend_Controller
         */
        parent::input();

        $this->title = 'Пользователи. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogBackendModel->getURL('backend/index/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1; // текущая старница
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во пользователей
        $totalUsers = $this->userBackendModel->getCountAllUsers();
        // URL этой страницы
        $thisPageURL = $this->userBackendModel->getURL('backend/user/index');
        $temp = new Pager(
            $thisPageURL,                                   // URL этой страницы
            $page,                                          // текущая старница
            $totalUsers,                                    // общее кол-во пользователей
            $this->config->pager->backend->users->perpage,  // кол-во пользователей на страницу
            $this->config->pager->backend->users->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->backend->users->perpage;

        // получаем от модели массив всех пользователей
        $users = $this->userBackendModel->getAllUsers($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех пользователей
            'users'       => $users,
            // постраничная навигация
            'pager'       => $pager,
            // URL ссылки на страницу с формой для добавления пользователя
            'addUserUrl'  => $this->userBackendModel->getURL('backend/user/add'),
        );

    }

}