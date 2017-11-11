<?php
/**
 * Класс Index_Sitemap_Frontend_Controller фомирует карту сайта, получает данные
 * от модели Sitemap_Frontend_Model, общедоступная часть сайта
 */
class Index_Sitemap_Frontend_Controller extends Sitemap_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования карты сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sitemap_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Sitemap_Frontend_Controller
         */
        parent::input();

        /*
         * заголовок страницы (тег <title>), мета-теги keywords и description
         */
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
