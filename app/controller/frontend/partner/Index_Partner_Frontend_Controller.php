<?php
/**
 * Класс Index_Partner_Frontend_Controller формирует список партнеров компании,
 * получает данные от модели Partner_Frontend_Model, общедоступная часть сайта
 */
class Index_Partner_Frontend_Controller extends Partner_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком партнеров компании
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Partner_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Partner_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/partner/index');

        // получаем от модели массив всех партнеров компании
        $partners = $this->partnerFrontendModel->getAllPartners();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех партнеров компании
            'partners'    => $partners,
        );

    }

}