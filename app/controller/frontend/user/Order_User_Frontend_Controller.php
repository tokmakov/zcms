<?php
/**
 * Класс Order_User_Frontend_Controller формирует страницу с подробной
 * информацией о заказе зарегистрированного (и авторизованного) пользователя,
 * получает данные от модели User_Frontend_Model, общедоступная часть сайта
 */
class Order_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * информацией о заказе зарегистрированного (и авторизованного) пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Order_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        $this->title = 'Информация о заказе. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Личный кабинет',
                'url'  => $this->userFrontendModel->getURL('frontend/user/index')
            ),
            array(
                'name' => 'История заказов',
                'url'  => $this->userFrontendModel->getURL('frontend/user/allorders')
            ),
        );

        // получаем от модели информацию о заказе
        $order = $this->userFrontendModel->getOrder($this->params['id']);
        if (empty($order)) {
            $this->notFoundRecord = true;
            return;
        }
        
        // получаем от модели список офисов для самовывоза товара со склада
        $offices = $this->userFrontendModel->getOffices();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // информация о заказе
            'order'       => $order,
            // список офисов для самовывоза
            'offices'     => $offices,
        );

    }

}
