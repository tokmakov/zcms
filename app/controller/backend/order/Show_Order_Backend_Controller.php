<?php
/**
 * Класс Show_Order_Backend_Controller формирует страницу с подробной информацией о
 * заказе, получает данные от модели Order_Backend_Model, административная часть сайта
 */
class Show_Order_Backend_Controller extends Order_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с подробной информацией о заказе
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Order_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Show_Order_Backend_Controller
         */
        parent::input();

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = 'Заказ. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->orderBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->orderBackendModel->getURL('backend/order/index'), 'name' => 'Заказы'),
        );

        // получаем от модели подробную информацию о заказе
        $order = $this->orderBackendModel->getOrder($this->params['id']);
        // если запрошенный заказ не найден в таблице orders БД
        if (empty($order)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs, // хлебные крошки
            'order'       => $order,       // подробная информация о заказе
        );

    }

}