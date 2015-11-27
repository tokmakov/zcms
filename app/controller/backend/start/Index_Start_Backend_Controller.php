<?php
/**
 * Класс Index_Start_Backend_Controller формирует страницу управления витриной
 * сайта (главная страница общедоступной части сайта), получает данные от модели
 * Start_Backend_Model, административная часть сайта
 */
class Index_Start_Backend_Controller extends Start_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления витриной сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Start_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Start_Backend_Controller
         */
        parent::input();

        $this->title = 'Витрина. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем массив всех баннеров на витрине
        $banners = $this->startBackendModel->getAllBanners();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'  => $breadcrumbs,
            // URL страницы с формой для редактирования витрины
            'editStartUrl' => $this->startBackendModel->getURL('backend/start/edit'),
            // массив всех баннеров на витрине
            'banners'   => $banners,
            // URL страницы с формой для добавления баннера
            'addBannerUrl' => $this->startBackendModel->getURL('backend/start/addbnr'),
        );

    }

}