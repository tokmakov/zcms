<?php
/**
 * Класс Category_Catalog_Frontend_Controller формирует страницу категории каталога,
 * т.е. список дочерних категорий и список товаров категории, получает данные от
 * модели Category_Catalog_Frontend_Model, общедоступная часть сайта
 */
class Category_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * категории каталога, т.е. список дочерних категорий и список товаров категории
     */
    protected function input() {

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены: выбор функциональной группы, производителя,
        // параметров; для пользователей, у которых отключен JavaScript
        if ($this->isPostMethod()) {
            // обрабатываем отправленные данные формы, после чего делаем редирект
            // на страницу категории, но уже с параметрами формы в URL
            $this->processFormData();
        }

        /*
         * получаем от модели данные, необходимые для формирования страницы категории, и
         * записываем их в массив переменных, который будет передан в шаблон center.php
         */
        $this->getCategory();
        // товар не найден в таблице БД categories
        if ($this->notFoundRecord) {
            return;
        }

        // переопределяем переменную, которая будет передана в шаблон left.php,
        // чтобы раскрыть ветку текущей категории меню каталога в левой колонке
        $this->leftVars['catalogMenu'] = $this->menuCatalogFrontendModel->getCatalogMenu($this->params['id']);

    }

    /**
     * Функция получает от модели данные о категории и сохраняет их в массиве,
     * который будет передан в шаблон center.php
     */
    private function getCategory() {

        // получаем от модели информацию о категории
        $category = $this->categoryCatalogFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * обращаемся к родительскому классу Catalog_Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Catalog_Frontend_Controller
         */
        parent::input();

        $this->title = $category['name'];

        if ( ! empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if ( ! empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = $this->categoryCatalogFrontendModel->getCategoryPath($this->params['id']); // путь до категории
        array_pop($breadcrumbs); // последний элемент - текущая категория, нам она не нужна

        // включен фильтр по функциональной группе?
        $group = 0;
        if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
            $group = (int)$this->params['group'];
        }

        // включен фильтр по производителю?
        $maker = 0;
        if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
            $maker = (int)$this->params['maker'];
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
            // проверяем корректность переданных параметров и значений
            if ( ! $this->categoryCatalogFrontendModel->getCheckParams($param)) {
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
        if ($group || $maker || $hit || $new || $sort) {
            $this->robots = false;
        }

        // получаем от модели массив дочерних категорий
        $childCategories = $this->categoryCatalogFrontendModel->getChildCategories(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param,              // массив параметров подбора
            $sort                // сортировка
        );

        // получаем от модели массив функциональных групп
        $groups = $this->categoryCatalogFrontendModel->getCategoryGroups(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели массив производителей
        $makers = $this->categoryCatalogFrontendModel->getCategoryMakers(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели массив всех параметров подбора
        $params = $this->categoryCatalogFrontendModel->getCategoryGroupParams(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->categoryCatalogFrontendModel->getCountCategoryHit(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // получаем от модели количество новинок
        $countNew = $this->categoryCatalogFrontendModel->getCountCategoryNew(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров категории
        $totalProducts = $this->categoryCatalogFrontendModel->getCountCategoryProducts(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );
        $pager = null; // постраничная навигация
        $start = 0;    // стартовая позиция для SQL-запроса
        if ($totalProducts > $this->config->pager->frontend->products->perpage) { // постраничная навигация нужна?
            // URL этой страницы
            $thisPageURL = $this->categoryCatalogFrontendModel->getCategoryURL(
                $this->params['id'], // уникальный идентификатор категории
                $group,              // идентификатор функциональной группы или ноль
                $maker,              // идентификатор производителя или ноль
                $hit,                // включен или нет фильтр по лидерам продаж
                $new,                // включен или нет фильтр по новинкам
                $param,              // массив параметров подбора
                $sort                // сортировка
            );
            $temp = new Pager(
                $thisPageURL,                                       // URL этой страницы
                $page,                                              // текущая страница
                $totalProducts,                                     // общее кол-во товаров категории
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
        }

        // получаем от модели массив товаров категории
        $products = $this->categoryCatalogFrontendModel->getCategoryProducts(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param,              // массив параметров подбора
            $sort,               // сортировка
            $start               // стартовая позиция для SQL-запроса
        );

        // единицы измерения товара
        $units = $this->categoryCatalogFrontendModel->getUnits();

        // ссылки для сортировки товаров по цене, наменованию, коду
        $sortorders = $this->categoryCatalogFrontendModel->getCategorySortOrders(
            $this->params['id'], // уникальный идентификатор категории
            $group,              // идентификатор функциональной группы или ноль
            $maker,              // идентификатор производителя или ноль
            $hit,                // включен или нет фильтр по лидерам продаж
            $new,                // включен или нет фильтр по новинкам
            $param               // массив параметров подбора
        );

        // атрибут action тега form
        $action = $this->categoryCatalogFrontendModel->getURL('frontend/catalog/category/id/' . $this->params['id']);

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/category/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        $clearFilterURL = $this->categoryCatalogFrontendModel->getURL($url);

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs'     => $breadcrumbs,        // хлебные крошки
            'id'              => $this->params['id'], // уникальный идентификатор категории
            'name'            => $category['name'],   // наименование категории
            'childCategories' => $childCategories,    // массив дочерних категорий
            'action'          => $action,             // атрибут action тега форм
            'view'            => $view,               // представление списка товаров
            'group'           => $group,              // id выбранной функциональной группы или ноль
            'maker'           => $maker,              // id выбранного производителя или ноль
            'param'           => $param,              // массив выбранных параметров подбора
            'hit'             => $hit,                // показывать только лидеров продаж?
            'countHit'        => $countHit,           // количество лидеров продаж
            'new'             => $new,                // показывать только новинки?
            'countNew'        => $countNew,           // количество новинок
            'groups'          => $groups,             // массив функциональных групп
            'makers'          => $makers,             // массив производителей
            'params'          => $params,             // массив всех параметров подбора
            'sort'            => $sort,               // выбранная сортировка
            'sortorders'      => $sortorders,         // массив вариантов сортировки
            'units'           => $units,              // массив единиц измерения товара
            'products'        => $products,           // массив товаров категории
            'clearFilterURL'  => $clearFilterURL,     // URL ссылки для сборса фильтра
            'pager'           => $pager,              // постраничная навигация
            'page'            => $page,               // текущая страница
        );

    }

    /**
     * Вспомогательная функция, обрабатывает отправленные данные формы, если у посетителя отключен
     * JavaScript, после чего делает редирект на страницу категории, но уже с параметрами в URL
     */
    private function processFormData() {
        $url = 'frontend/catalog/category/id/' . $this->params['id'];
        $grp = false;
        if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
            $url = $url . '/group/' . $_POST['group'];
            $grp = true;
        }
        if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
            $url = $url . '/maker/' . $_POST['maker'];
        }
        if (isset($_POST['hit'])) {
            $url = $url . '/hit/1';
        }
        if (isset($_POST['new'])) {
            $url = $url . '/new/1';
        }
        if ($grp && isset($_POST['param']) && is_array($_POST['param'])) {
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
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $url = $url . '/sort/' . $_POST['sort'];
        }
        $this->redirect($this->categoryCatalogFrontendModel->getURL($url));
    }

}
