<?php
/**
 * Класс Brands_Catalog_Frontend_Controller формирует страницу со списком всех
 * брендов (производителей, отмеченных в админке как бренд), получает данные от
 * модели Brands_Catalog_Frontend_Model, общедоступная часть сайта
 */
class Brands_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * экземпляр класса модели для работы с брендами
     */
    protected $brandFrontendModel;


    public function __construct($params = null) {
        parent::__construct($params);

        // экземпляр класса модели для работы с брендами
        $this->brandCatalogFrontendModel =
            isset($this->register->brandCatalogFrontendModel) ? $this->register->brandCatalogFrontendModel : new Brand_Catalog_Frontend_Model();
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех брендов (производителей, отмеченных в админке как бренд)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Brands_Catalog_Frontend_Controller
         */
        parent::input();

        $this->title = 'Бренды. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->brandCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->brandCatalogFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Производители',
                'url'  => $this->brandCatalogFrontendModel->getURL('frontend/catalog/makers')
            ),
        );

        // получаем от модели алфавит
        $alphabet = array(
            // английский
            'A-Z' => $this->brandCatalogFrontendModel->getLatinLetters(),
            // русский
            'А-Я' => $this->brandCatalogFrontendModel->getCyrillicLetters()
        );

        // получаем от модели массив популярных брендов
        $popular = $this->brandCatalogFrontendModel->getPopularBrands();

        // получаем от модели массив всех брендов
        $brands = $this->brandCatalogFrontendModel->getAllBrands();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // алфавит
            'alphabet'    => $alphabet,
            // массив популярных брендов
            'popular'     => $popular,
            // массив всех брендов
            'brands'      => $brands,
        );

    }

}
