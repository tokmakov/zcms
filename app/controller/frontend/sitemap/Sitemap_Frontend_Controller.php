<?php
/**
 * Класс Sitemap_Frontend_Controller фомирует карту сайта, получает данные от
 * моделей Page_Frontend_Model, News_Frontend_Model и Catalog_Frontend_Model,
 * общедоступная часть сайта
 */
class Sitemap_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования карты сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения по умолчанию для всех переменных, необходимых для
         * формирования карты сайта, потом переопределяем их значения, если
         * необходимо
         */
        parent::input();

        $this->title = 'Карта сайта. ' . $this->title;
        $this->keywords = 'карта сайта ' . $this->keywords;
        $this->description = 'Карта сайта. ' . $this->description;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->sitemapFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
        );

        // получаем от модели массив всех страниц верхнего уровня и их детей
        $pages = $this->pageFrontendModel->getAllPages();

        // получаем от модели массив категорий новостей
        $newsCategories = $this->newsFrontendModel->getCategories();

        // получаем от модели массив корневых категорий и их детей
        $root = $this->catalogFrontendModel->getRootAndChilds();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs'    => $breadcrumbs,    // хлебные крошки
            'pages'          => $pages,          // массив страниц сайта
            'newsCategories' => $newsCategories, // массив категорий новостей
            'root'           => $root,           // массив категорий каталога
        );

    }

}
