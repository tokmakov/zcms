<?php
/**
 * Класс Index_News_Backend_Controller формирует страницу со списком всех
 * товаров по сниженным ценам, получает данные от модели Sale_Backend_Model
 */
class Index_Sale_Backend_Controller extends Sale_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех товаров по сниженным ценам
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sale_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Sale_Backend_Controller
         */
        parent::input();

        $this->title = 'Распродажа. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->saleBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив всех товаров по сниженным ценам
        $products = $this->saleBackendModel->getAllProducts();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех товаров
            'products'    => $products,
            // URL ссылки на страницу с формой для добавления товара
            'addNewsUrl'  => $this->saleBackendModel->getURL('backend/sale/addprd'),
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->saleBackendModel->getURL('backend/sale/addctg'),
        );

    }

}