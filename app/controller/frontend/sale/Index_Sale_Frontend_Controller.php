<?php
/**
 * Класс Index_Sale_Frontend_Controller формирует список товаров по сниженным
 * ценам, получает данные от модели Sale_Frontend_Model, общедоступная часть
 * сайта
 */
class Index_Sale_Frontend_Controller extends Sale_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком товаров по сниженным ценам
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sale_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Sale_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/sale/index');

        // получаем от модели массив всех товаров по сниженным ценам
        $sale = $this->saleFrontendModel->getAllProducts();

        // единицы измерения товара
        $units = $this->saleFrontendModel->getUnits();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех товаров и категорий
            'sale'        => $sale,
            // единицы измерения товара
            'units'       => $units,
        );

    }

}