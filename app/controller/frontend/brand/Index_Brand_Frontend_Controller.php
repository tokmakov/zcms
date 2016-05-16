<?php
/**
 * Класс Index_Brand_Frontend_Controller формирует список брендов, получает
 * данные от модели Brand_Frontend_Model, общедоступная часть сайта
 */
class Index_Brand_Frontend_Controller extends Brand_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком брендов
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Brand_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Brand_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/brand/index');
        
        // получаем от модели алфавит
        $alphabet = array(
            // английский
            'A-Z' => $this->brandFrontendModel->getLatinLetters(),
            // русский
            'А-Я' => $this->brandFrontendModel->getCyrillicLetters()
        );

        // получаем от модели массив популярных брендов
        $popular = $this->brandFrontendModel->getPopularBrands();

        // получаем от модели массив всех брендов
        $brands = $this->brandFrontendModel->getAllBrands();

        /*
         * переменные, которые будут переданы в шаблон center.php
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