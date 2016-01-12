<?php
/**
 * Класс Index_User_Frontend_Controller формирует страницу личного кабинета
 * пользователя (если он авторизован) или перенаправляет на страницу авторизации,
 * получает данные от модели User_Frontend_Model, общедоступная часть сайта
 */
class Index_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * личного кабинета пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if ( ! $this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        $this->title = 'Личный кабинет. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index')
            ),
        );

        // новый пользователь?
        $newUser = $this->userFrontendModel->isNewUser();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'     => $breadcrumbs,
            // ссылка на страницу с формой для редактирования личных данных
            'userEditUrl'     => $this->userFrontendModel->getURL('frontend/user/edit'),
            // ссылка на страницу со списком всех профилей
            'userProfilesUrl' => $this->userFrontendModel->getURL('frontend/user/allprof'),
            // ссылка на страницу со списком всех заказов
            'userOrdersUrl'   => $this->userFrontendModel->getURL('frontend/user/allorders'),
            // ссылка на страницу корзины
            'basketUrl'       => $this->userFrontendModel->getURL('frontend/basket/index'),
            // ссылка на страницу со списком всех отложенных товаров
            'userWishedUrl'   => $this->userFrontendModel->getURL('frontend/wished/index'),
            // ссылка на страницу со списком всех просмотренных товаров
            'userViewedUrl'   => $this->userFrontendModel->getURL('frontend/viewed/index'),
            // ссылка для выхода из личного кабинета
            'userLogoutUrl'   => $this->userFrontendModel->getURL('frontend/user/logout'),
            // новый пользователь?
            'newUser'         => $newUser,
        );

    }

}
