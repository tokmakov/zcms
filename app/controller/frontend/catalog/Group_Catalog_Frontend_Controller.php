<?php
/**
 * Класс Group_Catalog_Frontend_Controller формирует страницу со списком
 * всех товаров выбранной функциональной группы, получает данные от модели
 * Group_Catalog_Frontend_Model, общедоступная часть сайта
 */
class Group_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы:
     * форма фильтров + список товаров выбранной функциональной группы
     */
    protected function input() {

        // если не передан id функциональной группы или id группы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        /*
         * Если у пользователя отключен JavaScript, данные формы фильтров отправляются без
         * использования XmlHttpRequest, пользователь просто выбирает нужные фильтры и жмет
         * кнопку «Применить». Здесь мы обрабатываем эти данные и делаем редирект на эту же
         * страницу, но уже с параметрами формы в URL.
         */
        if ($this->isPostMethod()) {
            $this->processFormData();
        }

        // получаем от модели информацию о функциональной группе
        $name = $this->groupCatalogFrontendModel->getGroupName($this->params['id']);
        // если запрошенная функциональная группа не найдена в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * обращаемся к родительскому классу Catalog_Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Group_Catalog_Frontend_Controller
         */
        parent::input();

        $this->title = $name . '. Все товары.';

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->groupCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->groupCatalogFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Функциональные группы',
                'url'  => $this->groupCatalogFrontendModel->getURL('frontend/catalog/groups')
            ),
        );

        // включен фильтр по производителю?
        $maker = 0;
        if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
            $maker = (int)$this->params['maker'];
        }

        // включен фильтр по параметрам (доп.фильтрам)?
        $param = array();
        if (isset($this->params['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $this->params['param'])) {
            $temp = explode('-', $this->params['param']);
            foreach ($temp as $item) {
                $tmp = explode('.', $item);
                $key = (int)$tmp[0];
                $value = (int)$tmp[1];
                $param[$key] = $value;
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->groupCatalogFrontendModel->getCheckParams($param)) {
                $this->notFoundRecord = true;
                return;
            }
        }

        // включен фильтр по лидерам продаж?
        $hit = 0;
        if (isset($this->params['hit']) && 1 == $this->params['hit']) {
            $hit = 1;
        }

        // включен фильтр по новинкам?
        $new = 0;
        if (isset($this->params['new']) && 1 == $this->params['new']) {
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

        // получаем от модели массив всех производителей
        $makers = $this->groupCatalogFrontendModel->getGroupMakers(
            $this->params['id'],
            $hit,
            $new,
            $param
        );

        // получаем от модели массив всех параметров подбора
        $params = $this->groupCatalogFrontendModel->getGroupParams(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->groupCatalogFrontendModel->getCountGroupHit(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->groupCatalogFrontendModel->getCountGroupNew(
            $this->params['id'],
            $maker,
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
        $totalProducts = $this->groupCatalogFrontendModel->getCountGroupProducts(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageURL = $this->groupCatalogFrontendModel->getGroupURL(
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
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->products->perpage;

        // получаем от модели массив всех товаров функциональной группы
        $products = $this->groupCatalogFrontendModel->getGroupProducts(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param,
            $sort,
            $start
        );

        // единицы измерения товара
        $units = $this->groupCatalogFrontendModel->getUnits();

        // ссылки для сортировки товаров по цене, наменованию, коду
        $sortorders = $this->groupCatalogFrontendModel->getGroupSortOrders(
            $this->params['id'],
            $maker,
            $hit,
            $new,
            $param
        );

        // атрибут action тега form
        $action = $this->groupCatalogFrontendModel->getURL('frontend/catalog/group/id/' . $this->params['id']);

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/group/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        $clearFilterURL = $this->groupCatalogFrontendModel->getURL($url);

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
            // массив всех производителей
            'makers'         => $makers,
            // показывать только лидеров продаж?
            'hit'            => $hit,
            // количество лидеров продаж
            'countHit'       => $countHit,
            // показывать только новинки?
            'new'            => $new,
            // количество новинок
            'countNew'       => $countNew,
            // массив выбранных параметров подбора
            'param'          => $param,
            // массив всех параметров подбора
            'params'         => $params,
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

    /**
     * Вспомогательная функция, обрабатывает отправленные данные формы фильтров в том
     * случае, если у посетителя отключен JavaScript, после чего делает редирект на
     * эту же страницу, но уже с фильтрами в URL.
     */
    private function processFormData() {

        // базовый URL функциональной группы, без фильтров и сортировки
        $url = 'frontend/catalog/group/id/' . $this->params['id'];
        // включен фильтр по производителю?
        if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
            $url = $url . '/maker/' . $_POST['maker'];
        }
        // включен фильтр по лидерам продаж?
        if (isset($_POST['hit'])) {
            $url = $url . '/hit/1';
        }
        // включен фильтр по новинкам?
        if (isset($_POST['new'])) {
            $url = $url . '/new/1';
        }
        // включены параметры подбора (доп.фильтры)?
        if (isset($_POST['param']) && is_array($_POST['param'])) {
            $param = array();
            foreach ($_POST['param'] as $key => $value) {
                if ($key > 0 && ctype_digit($value) && $value > 0) {
                    $param[] = $key . '.' . $value;
                }
            }
            if ( ! empty($param)) {
                $url = $url . '/param/' . implode('-', $param);
            }
        }
        // включена сортировка?
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $url = $url . '/sort/' . $_POST['sort'];
        }
        // выполняем редирект
        $this->redirect($this->groupCatalogFrontendModel->getURL($url));

    }

}
