<?php
/**
 * Класс Index_Brand_Backend_Controller формирует страницу управления брендами,
 * получает данные от модели Brand_Backend_Model, административная часть сайта
 */
class Index_Brand_Backend_Controller extends Brand_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления брендами
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Brand_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Brand_Backend_Controller
         */
        parent::input();

        $this->title = 'Бренды. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->brandBackendModel->getURL('backend/index/index')
            ),
        );
        
        // получаем массив популярных брендов
        $popular = $this->brandBackendModel->getPopularBrands();

        // получаем массив всех брендов
        $brands = $this->brandBackendModel->getAllBrands();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив популярных брендов
            'popular'     => $popular,
            // массив всех брендов
            'brands'      => $brands,
            // URL страницы с формой для добавления бренда
            'addBrandURL' => $this->brandBackendModel->getURL('backend/brand/add'),
        );

    }

}