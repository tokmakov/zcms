<?php
/**
 * Класс Index_Index_Frontend_Controller фомирует главную страницу
 * общедоступной части сайта
 */
class Index_Index_Frontend_Controller extends Index_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования главной
     * страницы сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Index_Frontend_Controller
         */
        parent::input();

        /*
         * получаем от модели все данные для формирования главной страницы сайта:
         * $index - данные о главной странице
         * $banners - массив баннеров слайдера
         * $companyNews - массив новостей компании
         * $generalNews - массив событий отрасли
         * $hitProducts - массив лидеров продаж
         * $newProducts - массив новых товаров
         */
        list($index, $banners, $companyNews, $generalNews, $hitProducts, $newProducts) =
            $this->indexFrontendModel->getAllIndexData();

        $this->title = $index['title'];
        if ( ! empty($index['keywords'])) {
            $this->keywords = $index['keywords'];
        }
        if ( ! empty($index['description'])) {
            $this->description = $index['description'];
        }

        // единицы измерения товара
        $units = $this->indexFrontendModel->getUnits();

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // заголовок h1 главной страницы
            'name'        => $index['name'],
            // текст главной страницы в формате html
            'text'        => $index['body'],
            // массив баннеров слайдера
            'banners'     => $banners,
            // массив лидеров продаж
            'hitProducts' => $hitProducts,
            // массив новых товаров
            'newProducts' => $newProducts,
            // массив единиц измерения товара
            'units'       => $units,
            // массив новостей компании
            'companyNews' => $companyNews,
            // массив событий отрасли
            'generalNews' => $generalNews,
        );

    }

}
