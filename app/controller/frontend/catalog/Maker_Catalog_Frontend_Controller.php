<?php
/**
 * Класс maker_Catalog_Frontend_Controller формирует страницу со списком
 * всех товаров выбранного производителя, получает данные от модели
 * Catalog_Frontend_Model, общедоступная часть сайта
 */
class Maker_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

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
         * Maker_Catalog_Frontend_Controller
         */
        parent::input();

        // если не передан id производителя или id производителя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о производителе
        $maker = $this->catalogFrontendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $maker['name'] . '. Все товары производителя.';

        if ( ! empty($maker['keywords'])) {
            $this->keywords = $maker['keywords'];
        }
        if ( ! empty($maker['description'])) {
            $this->description = $maker['description'];
        }

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
                'name' => 'Производители',
                'url'  => $this->catalogFrontendModel->getURL('frontend/catalog/allmkrs')
            ),
        );
        
        // включен фильтр по функциональной группе?
        $group = 0;
        if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
            $group = (int)$this->params['group'];
        }
        
        // включен фильтр по параметрам?
        $param = array();
        if ($group && isset($this->params['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $this->params['param'])) {
            $temp = explode('-', $this->params['param']);
            foreach ($temp as $item) {
                $tmp = explode('.', $item);
                $key = (int)$tmp[0];
                $value = (int)$tmp[1];
                $param[$key] = $value;
            }
        }
        
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
        if ($group || $hit || $new || $sort) {
            $this->robots = false;
        }
        
        // получаем от модели массив функциональных групп
        $groups = $this->catalogFrontendModel->getMakerGroups(
            $this->params['id'],
            $hit,
            $new
        );
        
        // получаем от модели массив всех параметров подбора
        $params = $this->catalogFrontendModel->getMakerGroupParams(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );
        
        // получаем от модели количество лидеров продаж
        $countHit = $this->catalogFrontendModel->getCountMakerHit(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->catalogFrontendModel->getCountMakerNew(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров производителя с учетом фильтров по функционалу,
        // лидерам продаж и новинкам
        $totalProducts = $this->catalogFrontendModel->getCountMakerProducts(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageURL = $this->catalogFrontendModel->getMakerURL(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort
        );
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров производителя
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

        // получаем от модели массив всех товаров производителя
        $products = $this->catalogFrontendModel->getMakerProducts(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort,
            $start
        );

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();
        
        // ссылки для сортировки товаров по цене, наменованию, коду
        $sortorders = $this->catalogFrontendModel->getMakerSortOrders(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );
        
        // атрибут action тега form
        $action = $this->catalogFrontendModel->getURL('frontend/catalog/maker/id/' . $this->params['id']);

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/maker/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        $clearFilterURL = $this->catalogFrontendModel->getURL($url);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'    => $breadcrumbs,
            // уникальный идентификатор производителя
            'id'             => $this->params['id'],
            // название производителя
            'name'           => $maker['name'],
            // атрибут action тега форм
            'action'         => $action,
            // id выбранной функциональной группы или ноль
            'group'          => $group,
            // показывать только лидеров продаж?
            'hit'            => $hit,
            // количество лидеров продаж
            'countHit'       => $countHit,
            // показывать только новинки?
            'new'            => $new,
            // количество новинок
            'countNew'       => $countNew,
            // массив выбранных параметров подбора
            'param'           => $param,
            // массив функциональных групп
            'groups'         => $groups,
            // массив всех параметров подбора 
            'params'         => $params,          
            // массив товаров производителя
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
