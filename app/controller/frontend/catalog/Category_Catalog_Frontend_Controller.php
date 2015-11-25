<?php
/**
 * Класс Category_Catalog_Frontend_Controller формирует страницу категории каталога,
 * т.е. список дочерних категорий и список товаров категории, получает данные от
 * модели Catalog_Frontend_Model, общедоступная часть сайта
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

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Catalog_Frontend_Controller
         */
        parent::input();

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
        // чтобы раскрыть ветку текущей категории
        $this->leftVars['catalogMenu'] = $this->catalogFrontendModel->getCatalogMenu($this->params['id']);

    }

    /**
     * Функция получает от модели данные о категории и сохраняет их в массиве,
     * который будет передан в шаблон center.php
     */
    private function getCategory() {

        // получаем от модели информацию о категории
        $category = $this->catalogFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'];

        if ( ! empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if ( ! empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = $this->catalogFrontendModel->getCategoryPath($this->params['id']); // путь до категории
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
            /*
            if ( ! $this->catalogFrontendModel->isValidParams($param)) {
                $this->notFoundRecord = true;
                return;
            }
            */
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
        if ($group || $maker || $hit || $new || $sort) {
            $this->robots = false;
        }

        // получаем от модели массив дочерних категорий
        $childCategories = $this->catalogFrontendModel->getChildCategories(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param,
            $sort
        );

        // получаем от модели массив функциональных групп
        $groups = $this->catalogFrontendModel->getCategoryGroups(
            $this->params['id'],
            $maker,
            $hit,
            $new
        );

        // получаем от модели массив производителей
        $makers = $this->catalogFrontendModel->getCategoryMakers(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели массив всех параметров подбора
        $params = $this->catalogFrontendModel->getGroupParams(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->catalogFrontendModel->getCountHit(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->catalogFrontendModel->getCountNew(
            $this->params['id'],
            $group,
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
            $page = $this->params['page'];
        }
        // общее кол-во товаров категории
        $totalProducts = $this->catalogFrontendModel->getCountCategoryProducts(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageUrl = $this->catalogFrontendModel->getCategoryURL(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param,
            $sort
        );
        $temp = new Pager(
            $thisPageUrl,                                       // URL этой страницы
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

        // получаем от модели массив товаров категории
        $products = $this->catalogFrontendModel->getCategoryProducts(
            $this->params['id'],
            $group,
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
        $sortorders = $this->catalogFrontendModel->getCategorySortOrders(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // атрибут action тега form
        $action = $this->catalogFrontendModel->getURL('frontend/catalog/category/id/' . $this->params['id']);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs'     => $breadcrumbs,        // хлебные крошки
            'id'              => $this->params['id'], // уникальный идентификатор категории
            'name'            => $category['name'],   // наименование категории
            'childCategories' => $childCategories,    // массив дочерних категорий
            'action'          => $action,             // атрибут action тега форм
            'group'           => $group,              // id выбранной функциональной группы или ноль
            'maker'           => $maker,              // id выбранного производителя или ноль
            'hit'             => $hit,                // показывать только лидеров продаж?
            'countHit'        => $countHit,           // количество лидеров продаж
            'new'             => $new,                // показывать только новинки?
            'countNew'        => $countNew,           // количество новинок
            'param'           => $param,              // массив выбранных параметров подбора
            'groups'          => $groups,             // массив функциональных групп
            'makers'          => $makers,             // массив производителей
            'params'          => $params,             // массив всех параметров подбора
            'sort'            => $sort,               // выбранная сортировка
            'sortorders'      => $sortorders,         // массив вариантов сортировки
            'units'           => $units,              // массив единиц измерения товара
            'products'        => $products,           // массив товаров категории
            'pager'           => $pager,              // постраничная навигация
            'page'            => $page,               // текущая страница
        );

    }

    /**
     * Вспомогательная функция, обрабатывает отправленные данные формы, если у посетителя отключен
     * JavaScript, после чего делает редирект на страницу категории, но уже с параметрами в URL
     *
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
        $this->redirect($this->catalogFrontendModel->getURL($url));
    }

}
