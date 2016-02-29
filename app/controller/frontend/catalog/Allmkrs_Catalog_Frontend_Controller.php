<?php
/**
 * Класс Allmkrs_Catalog_Frontend_Controller формирует страницу со списком всех
 * производителей, получает данные от модели Catalog_Frontend_Model, общедоступная
 * часть сайта
 */
class Allmkrs_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех производителей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allmkrs_Catalog_Frontend_Controller
         */
        parent::input();

        $this->title = 'Производители. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->catalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url' => $this->catalogFrontendModel->getURL('frontend/catalog/index')
            ),
        );

        // получаем от модели массив всех производителей
        $makers = $this->catalogFrontendModel->getAllMakers(0);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs, // хлебные крошки
            'makers'      => $makers,      // массив всех производителей
        );

    }

}
