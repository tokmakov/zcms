<?php
/**
 * Класс Index_Rating_Frontend_Controller формирует список товаров и категорий
 * рейтинга продаж, получает данные от модели Rating_Frontend_Model,
 * общедоступная часть сайта
 */
class Index_Rating_Frontend_Controller extends Rating_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком товаров и категорий рейтинга продаж
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Rating_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Rating_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = $this->sitemapFrontendModel->getBreadcrumbs('frontend/rating/index');

        // получаем от модели массив всех категорий и товаров рейтинга
        $rating = $this->ratingFrontendModel->getRating();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех категорий и товаров рейтинга
            'rating'      => $rating,
        );

    }

}