<?php
/**
 * Класс Index_Sitemap_Backend_Controller формирует страницу со списком всех элементов
 * карты сайта, получает данные от модели Sitemap_Backend_Model, административная часть
 * сайта
 */
class Index_Sitemap_Backend_Controller extends Sitemap_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sitemap_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Sitemap_Backend_Controller
         */
        parent::input();

        $this->title = 'Карта сайта. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->sitemapBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив все элементов карты сайта
        $sitemapItems = $this->sitemapBackendModel->getAllSitemapItems();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'  => $breadcrumbs,
            // URL ссылки на страницу с формой для добавления элемента карты сайта
            'addItemURL'   => $this->sitemapBackendModel->getURL('backend/sitemap/additem'),
            // массив всех элементов карты сайта
            'sitemapItems' => $sitemapItems,
        );

    }

}