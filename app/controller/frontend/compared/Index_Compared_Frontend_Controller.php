<?php
/**
 * Класс Index_Compared_Frontend_Controller формирует страницу со списком всех
 * отложенных для сравнения товаров, получает данные от модели Compared_Frontend_Model,
 * общедоступная часть сайта
 */
class Index_Compared_Frontend_Controller extends Compared_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех отложенных для сравнения товаров
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Compared_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Compared_Frontend_Controller
         */
        parent::input();

        $this->title = 'Сравнение товаров. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->comparedFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->comparedFrontendModel->getURL('frontend/catalog/index'), 'name' => 'Каталог'),
        );

        // получаем от модели массив отложенных для сравнения товаров
        $comparedProducts = $this->comparedFrontendModel->getComparedProducts();

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'      => $breadcrumbs,
            // URL ссылки на эту страницу
            'thisPageUrl'      => $this->comparedFrontendModel->getURL('frontend/compared/index'),
            // массив отложенных для сравнения товаров
            'comparedProducts' => $comparedProducts,
            // массив единиц измерения товара
            'units'            => $units,
        );

    }

}
