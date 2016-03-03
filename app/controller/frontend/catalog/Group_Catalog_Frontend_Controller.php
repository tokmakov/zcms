<?php
/**
 * Класс Group_Catalog_Frontend_Controller формирует страницу со списком
 * всех товаров выбранной функциональной группы, получает данные от модели
 * Catalog_Frontend_Model, общедоступная часть сайта
 */
class Group_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех товаров выбранного производителя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Group_Catalog_Frontend_Controller
         */
        parent::input();

        // если не передан id функциональной группы или id группы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о функциональной группе
        $name = $this->catalogFrontendModel->getGroupName($this->params['id']);
        // если запрошенная функциональная группа не найдена в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $name . '. Все товары.';

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->catalogFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Функциональные группы',
                'url'  => $this->catalogFrontendModel->getURL('frontend/catalog/groups')
            ),
        );

        // включен фильтр по производителю?
        $maker = 0;
        if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
            $maker = (int)$this->params['maker'];
        }

        // включен фильтр по параметрам?
        $param = array();
        
        // включен фильтр по лидерам продаж?
        $hit = 0;
        if (isset($this->params['hit']) && $this->params['hit'] == 1) {
            $hit = 1;
        }

        // включен фильтр по новинкам?
        $new = 0;
        if (isset($this->params['new']) && $this->params['new'] == 1) {
            $new = 1;
        }

        // включена сортировка?
        $sort = 0;
        if (isset($this->params['sort'])
            && ctype_digit($this->params['sort'])
            && in_array($this->params['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = (int)$this->params['sort'];
        }
        
        // мета-тег robots
        if ($maker || $hit || $new || $sort) {
            $this->robots = false;
        }

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров производителя с учетом фильтров по функционалу,
        // лидерам продаж и новинкам
        $totalProducts = $this->catalogFrontendModel->getCountGroupProducts(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageURL = $this->catalogFrontendModel->getGroupURL(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param,
            $sort
        );
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (is_null($pager)) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->products->perpage;

        // получаем от модели массив всех товаров функциональной группы
        $products = $this->catalogFrontendModel->getGroupProducts(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param,
            $sort,
            $start
        );

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();
        
        // ссылки для сортировки товаров по цене, наменованию, коду
        $sortorders = $this->catalogFrontendModel->getGroupSortOrders(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );
        
        // атрибут action тега form
        $action = $this->catalogFrontendModel->getURL('frontend/catalog/group/id/' . $this->params['id']);

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/group/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        $clearFilterURL = $this->catalogFrontendModel->getURL($url);

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'    => $breadcrumbs,
            // уникальный идентификатор группы
            'id'             => $this->params['id'],
            // наименование функционально группы
            'name'           => $name,
            // атрибут action тега форм
            'action'         => $action,
            // представление списка товаров
            'view'           => $view,
            // id выбранного производителя или ноль
            'maker'          => $maker,
            // показывать только лидеров продаж?
            'hit'            => $hit,
            // показывать только новинки?
            'new'            => $new,
            // массив выбранных параметров подбора
            'param'          => $param,
            // массив товаров функциональной группы
            'products'       => $products,
            // выбранная сортировка
            'sort'           => $sort,
            // массив вариантов сортировки
            'sortorders'     => $sortorders,
            // массив единиц измерения товара
            'units'          => $units,
            // URL ссылки для сборса фильтра
            'clearFilterURL' => $clearFilterURL,
            // постраничная навигация
            'pager'          => $pager,
            // текущая страница
            'page'           => $page,
        );

    }

}
