<?php
/**
 * Класс Index_Compare_Frontend_Controller формирует страницу всех товаров для
 * сравнения в виде таблицы, получает данные от модели Compare_Frontend_Model,
 * общедоступная часть сайта
 */
class Index_Compare_Frontend_Controller extends Compare_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех товаров для сравнения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Compare_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Compare_Frontend_Controller
         */
        parent::input();

        $this->title = 'Сравнение товаров. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->compareFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->compareFrontendModel->getURL('frontend/catalog/index')
            ),
        );
        
        // получаем от модели наимнование функциональной группы
        $name = $this->compareFrontendModel->getGroupName();
        
        // получаем от модели массив параметров, привязанных к группе
        $params = $this->compareFrontendModel->getGroupParams();

        // получаем от модели массив товаров для сравнения
        $products = $this->compareFrontendModel->getCompareProducts();

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'     => $breadcrumbs,
            // URL ссылки на эту страницу
            'thisPageUrl'     => $this->compareFrontendModel->getURL('frontend/compare/index'),
            // ссылка для удаления всех товаров из сравнения
            'clearCompareURL' => $this->basketFrontendModel->getURL('frontend/compare/clear'),
            // наимнование функциональной группы
            'name'            => $name,
            // массив параметров, привязанных к группе
            'params'          => $params,
            // массив товаров для сравнения
            'products'        => $products,
            // массив единиц измерения товара
            'units'           => $units,
        );

    }

}
