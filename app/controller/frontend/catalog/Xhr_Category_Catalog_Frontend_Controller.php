<?php
/**
 * Класс Xhr_Category_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Category_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранной категории
 */
class Xhr_Category_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результат фильтрации товаров в формате JSON, три фрагмента html-кода:
     * дочерние категории, подбор по параметрам, список товаров категории с
     * учетом фильтров
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        /*
         * Когда пользователь выбирает функциональную группу, производителя, параметры
         * подбора, включает фильтр по новинкам или лидерам продаж, данные отправляются
         * методом POST по событию change элементов формы.
         * Когда пользователь нажимает кнопки «Назад» и «Вперед» в браузере, данные
         * отправляются методом GET по событию popstate, см. описание window.history.
         */
        if ($this->isPostMethod()) {
            // если данные отправлены методом POST, получаем данные из формы: фильтр
            // по функционалу, производителю, лидерам продаж, новинкам, параметрам и
            // сортировка
            list($group, $maker, $hit, $new, $param, $sort) = $this->processFormData();
        } else {
            // если данные отправлены методом GET, получаем данные из URL: фильтр
            // по функционалу, производителю, лидерам продаж, новинкам, параметрам и
            // сортировка
            list($group, $maker, $hit, $new, $param, $sort) = $this->processUrlData();
        }

        // получаем от модели массив дочерних категорий
        $childs = $this->categoryCatalogFrontendModel->getChildCategories(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param,
            $sort
        );

        // получаем от модели массив функциональных групп
        $groups = $this->categoryCatalogFrontendModel->getCategoryGroups(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели массив производителей
        $makers = $this->categoryCatalogFrontendModel->getCategoryMakers(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели массив параметров подбора
        $params = $this->categoryCatalogFrontendModel->getCategoryGroupParams(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->categoryCatalogFrontendModel->getCountCategoryHit(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->categoryCatalogFrontendModel->getCountCategoryNew(
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
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров категории с учетом фильтров по функционалу, производителю,
        // параметрам подбора, лидерам продаж и новинкам
        $totalProducts = $this->categoryCatalogFrontendModel->getCountCategoryProducts( // общее кол-во товаров
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageURL = $this->categoryCatalogFrontendModel->getCategoryURL(
            $this->params['id'],
            $group,
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

        // получаем от модели массив товаров категории с учетом фильтров по функционалу,
        // производителю, параметрам подбора, лидерам продаж и новинкам
        $products = $this->categoryCatalogFrontendModel->getCategoryProducts(
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
        $units = $this->categoryCatalogFrontendModel->getUnits();

        // ссылки для сортировки товаров по цене, наименованию, коду
        $sortorders = $this->categoryCatalogFrontendModel->getCategorySortOrders(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        /*
         * Получаем три фрагмента html-кода, разделенные символом ¤:
         * дочерние категории, подбор по параметрам, список товаров
         */
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/category.php',
            array(
                'id'          => $this->params['id'], // уникальный идентификатор категории
                'view'        => $view,               // представление списка товаров: линейный или плитка
                'childs'      => $childs,             // массив дочерних категорий
                'group'       => $group,              // id выбранной функциональной группы или ноль
                'maker'       => $maker,              // id выбранного производителя или ноль
                'hit'         => $hit,                // показывать только лидеров продаж?
                'countHit'    => $countHit,           // количество лидеров продаж
                'new'         => $new,                // показывать только новинки?
                'countNew'    => $countNew,           // количество новинок
                'param'       => $param,              // массив выбранных параметров подбора
                'groups'      => $groups,             // массив функциональных групп выбранной категории
                'makers'      => $makers,             // массив производителей выбранной категории
                'params'      => $params,             // массив всех параметров подбора
                'sort'        => $sort,               // выбранная сортировка или ноль
                'sortorders'  => $sortorders,         // массив всех вариантов сортировки
                'units'       => $units,              // массив единиц измерения товара
                'products'    => $products,           // массив товаров категории с учетом фильтров
                'pager'       => $pager,              // постраничная навигация
                'page'        => $page,               // текущая страница
            )
        );
        // разделяем три фрагмента html-кода по символу ¤
        $output = explode('¤', $output);
        // дочерние категории, подбор по параметрам, список товаров
        $result = array('childs' => $output[0], 'filter' => $output[1], 'products' => $output[2]);
        // преобразуем массив в формат JSON
        $this->output = json_encode($result);

    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-type: application/json; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

    /**
     * Вспомогательная функция, получает необходимые данные из формы
     */
    private function processFormData() {

        $group = 0; // функционал
        if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
            $group = (int)$_POST['group'];
        }
        $maker = 0; // производитель
        if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
            $maker = (int)$_POST['maker'];
        }
        $hit = 0; // лидер продаж
        if (isset($_POST['hit'])) {
            $hit = 1;
        }
        $new = 0; // новинка
        if (isset($_POST['new'])) {
            $new = 1;
        }

        $param = array(); // параметры подбора
        if ($group && isset($_POST['param'])) {
            foreach ($_POST['param'] as $key => $value) {
                if ($key > 0 && ctype_digit($value) && $value > 0) {
                    $param[$key] = (int)$value;
                }
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->categoryCatalogFrontendModel->getCheckParams($param)) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                die();
            }
        }
        // если была выбрана новая функциональная группа, переданные параметры
        // подбора учитывать не надо, потому как у новой группы они будут другие
        if (isset($_POST['change']) && $_POST['change'] == 1) {
            $param = array();
        }

        $sort = 0; // сортировка
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = (int)$_POST['sort'];
        }

        return array($group, $maker, $hit, $new, $param, $sort);

    }

    /**
     * Вспомогательная функция, получает необходимые данные из URL
     */
    private function processUrlData() {

        $group = 0; // функционал
        if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
            $group = (int)$this->params['group'];
        }
        $maker = 0; // производитель
        if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
            $maker = (int)$this->params['maker'];
        }
        $hit = 0; // лидер продаж
        if (isset($this->params['hit']) && 1 == $this->params['hit']) {
            $hit = 1;
        }
        $new = 0; // новинка
        if (isset($this->params['new']) && 1 == $this->params['new']) {
            $new = 1;
        }

        $param = array(); // параметры подбора
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
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                die();
            }
        }

        $sort = 0; // сортировка
        if (isset($this->params['sort'])
            && ctype_digit($this->params['sort'])
            && in_array($this->params['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = (int)$this->params['sort'];
        }

        return array($group, $maker, $hit, $new, $param, $sort);

    }

}