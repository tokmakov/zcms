<?php
/**
 * Класс Index_Order_Backend_Controller формирует страницу со списком всех
 * заказов в магазине, получает данные от модели Order_Backend_Model,
 * административная часть сайта
 */
class Index_Order_Backend_Controller extends Order_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех заказов в магазине
     */
    protected function input() {

        // сначала обращаемся к родительскому классу Order_Backend_Controller,
        // чтобы установить значения переменных, которые нужны для работы всех его
        // потомков, потом переопределяем эти переменные (если необходимо) и
        // устанавливаем значения перменных, которые нужны для работы только
        // Index_Order_Backend_Controller
        parent::input();

        $this->title = 'Заказы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->orderBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
        );

        /*
         * постраничная навигация
         */
        $currentPage = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $currentPage = $this->params['page'];
        }
        // общее кол-во заказов
        $totalOrders = $this->orderBackendModel->getCountOrders();

        $temp = new Pager(
            $currentPage,
            $totalOrders,
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

        // получаем от модели массив заказов в магазине
        $orders = $this->orderBackendModel->getAllOrders($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив заказов в магазине
            'orders' => $orders,
            // постраничная навигация
            'pager' => $pager,
            // URL ссылки на эту страницу
            'thisPageUrl' => $this->orderBackendModel->getURL('backend/order/index'),
        );

    }

}