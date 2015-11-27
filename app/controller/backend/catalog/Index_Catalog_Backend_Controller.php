<?php
/**
 * Класс Index_Catalog_Backend_Controller формирует страницу со списком категорий
 * верхнего уровня каталога товаров, получает данные от модели Catalog_Backend_Model,
 * административная часть сайта
 */
class Index_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования главной
     * страницы каталога со списком категорий верхнего уровня
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Catalog_Backend_Controller
         */
        parent::input();

        $this->title = 'Каталог. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив категорий верхнего уровня
        $root = $this->catalogBackendModel->getChildCategories(0);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // URL страницы с формой для добавления категории
            'addCtgUrl'   => $this->catalogBackendModel->getURL('backend/catalog/addctg'),
            // URL страницы с формой для добавления товара
            'addPrdUrl'   => $this->catalogBackendModel->getURL('backend/catalog/addprd'),
            // URL страницы со списком всех производителей
            'allMkrUrl'   => $this->catalogBackendModel->getURL('backend/catalog/allmkrs'),
            // массив категорий верхнего уровня
            'root'        => $root,
        );

    }

}