<?php
/**
 * Класс Index_Sitemap_Frontend_Controller фомирует карту сайта, получает данные
 * от моделей Page_Frontend_Model, News_Frontend_Model и Catalog_Frontend_Model,
 * общедоступная часть сайта
 */
class Index_Sitemap_Frontend_Controller extends Frontend_Controller {

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
            array(
                'name' => 'Главная',
                'url'  => $this->sitemapFrontendModel->getURL('frontend/index/index')
            ),
        );

        // получаем от модели массив всех элементов карты сайта в виде дерева
        $sitemap = $this->sitemapFrontendModel->getSitemap();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех элементов карты сайта в виде дерева
            'sitemap'     => $sitemap,
        );

    }

}
