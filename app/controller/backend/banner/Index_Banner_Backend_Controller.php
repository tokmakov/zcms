<?php
/**
 * Класс Index_Banner_Backend_Controller формирует страницу управления баннерами,
 * получает данные от модели Banner_Backend_Model, административная часть сайта
 */
class Index_Banner_Backend_Controller extends Banner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления баннерами
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Banner_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Banner_Backend_Controller
         */
        parent::input();

        $this->title = 'Баннеры. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->bannerBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем массив всех баннеров
        $banners = $this->bannerBackendModel->getAllBanners();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'  => $breadcrumbs,
            // массив всех баннеров
            'banners'      => $banners,
            // URL страницы с формой для добавления баннера
            'addBannerUrl' => $this->bannerBackendModel->getURL('backend/banner/add'),
        );

    }

}