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

    /**
     * Переопределяем метод Base_Controller::request(), потому что здесь нам не
     * нужен сложный алгоритм формирования страницы категории каталога: получение
     * данных в методе input(), подключение js и css файлов, формирование отдельных
     * фрагментов HTML-кода, сборка страницы из фрагментов. Здесь просто запрашиваем
     * данные у модели и прогоняем их через шаблон, чтобы получить три фрагмента
     * html-кода.
     */
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
            /*
             * если данные отправлены методом POST, получаем данные из формы: фильтр
             * по функционалу, производителю, лидерам продаж, новинкам, параметрам и
             * сортировка
             */
            list($group, $maker, $hit, $new, $filter, $sort, $perpage) = $this->processFormData();
        } else {
            /*
             * если данные отправлены методом GET, получаем данные из URL: фильтр
             * по функционалу, производителю, лидерам продаж, новинкам, параметрам и
             * сортировка
             */
            list($group, $maker, $hit, $new, $filter, $sort, $perpage) = $this->processUrlData();
        }

        // получаем от модели массив дочерних категорий
        $childs = $this->categoryCatalogFrontendModel->getChildCategories(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter,
            $sort,
            $perpage
        );

        // получаем от модели массив функциональных групп
        $groups = $this->categoryCatalogFrontendModel->getCategoryGroups(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter
        );

        // получаем от модели массив производителей
        $makers = $this->categoryCatalogFrontendModel->getCategoryMakers(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $filter
        );

        // получаем от модели массив параметров подбора
        $filters = $this->categoryCatalogFrontendModel->getCategoryGroupParams(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->categoryCatalogFrontendModel->getCountCategoryHit(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter
        );

        // получаем от модели количество новинок
        $countNew = $this->categoryCatalogFrontendModel->getCountCategoryNew(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во товаров категории с учетом фильтров по функционалу,
        // производителю, параметрам подбора, лидерам продаж и новинкам
        $totalProducts = $this->categoryCatalogFrontendModel->getCountCategoryProducts( // общее кол-во товаров
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter
        );
        // URL этой страницы
        $thisPageURL = $this->categoryCatalogFrontendModel->getCategoryURL(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter,
            $sort,
            $perpage
        );
        $slice = $perpage ? $perpage : $this->config->pager->frontend->products->perpage;
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $slice,                                             // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $slice;

        /*
         * получаем от модели массив товаров категории в кол-ве $perpage, начиная с
         * позации $start с учетом фильтров по функционалу, производителю, параметрам
         * подбора, лидерам продаж и новинкам
         */
        $products = $this->categoryCatalogFrontendModel->getCategoryProducts(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter,
            $sort,
            $start,
            $perpage
        );

        // ссылки для сортировки товаров по цене, наименованию, коду
        $sortorders = $this->categoryCatalogFrontendModel->getCategorySortOrders(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter,
            $perpage
        );

        // ссылки для переключения на показ 10,20,50,100 товаров на страницу
        $perpages = $this->categoryCatalogFrontendModel->getOthersPerPage(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $filter,
            $sort,
            $perpage
        );

        // единицы измерения товара
        $units = $this->categoryCatalogFrontendModel->getUnits();

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
                'groups'      => $groups,             // массив функциональных групп выбранной категории
                'maker'       => $maker,              // id выбранного производителя или ноль
                'makers'      => $makers,             // массив производителей выбранной категории
                'filter'      => $filter,             // массив выбранных параметров подбора
                'filters'     => $filters,            // массив всех параметров подбора
                'hit'         => $hit,                // показывать только лидеров продаж?
                'countHit'    => $countHit,           // количество лидеров продаж
                'new'         => $new,                // показывать только новинки?
                'countNew'    => $countNew,           // количество новинок
                'sort'        => $sort,               // выбранная сортировка или ноль
                'sortorders'  => $sortorders,         // массив всех вариантов сортировки
                'perpage'     => $perpage,            // выбранный вариант кол-ва товаров на странице или ноль
                'perpages'    => $perpages,           // массив всех вариантов кол-ва товаров на страницу
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
     * Вспомогательная функция, получает необходимые данные из отправленных данных формы фильтра
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

        $filter = array(); // параметры подбора
        if ($group && isset($_POST['filter'])) {
            foreach ($_POST['filter'] as $key => $value) {
                if ($key > 0 && ctype_digit($value) && $value > 0) {
                    $filter[$key] = (int)$value;
                }
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->categoryCatalogFrontendModel->getCheckFilters($filter)) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                die();
            }
        }
        // если была выбрана новая функциональная группа, переданные параметры
        // подбора учитывать не надо, потому как у новой группы они будут другие
        if (isset($_POST['change']) && $_POST['change'] == 1) {
            $filter = array();
        }

        // пользователь выбрал сортировку товаров?
        $sort = 0;
        if (isset($_POST['sort']) && in_array($_POST['sort'], array(1,2,3,4,5,6))) {
            $sort = (int)$_POST['sort'];
        }

        // пользователь выбрал кол-во товаров на странице?
        $perpage = 0;
        $others = $this->config->pager->frontend->products->getValue('others');
        if (isset($_POST['perpage']) && in_array($_POST['perpage'], $others)) {
            $perpage = (int)$_POST['perpage'];
        }

        return array($group, $maker, $hit, $new, $filter, $sort, $perpage);

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

        $filter = array(); // параметры подбора
        if ($group && isset($this->params['filter']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $this->params['filter'])) {
            $temp = explode('-', $this->params['filter']);
            foreach ($temp as $item) {
                $tmp = explode('.', $item);
                $key = (int)$tmp[0];
                $value = (int)$tmp[1];
                $filter[$key] = $value;
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->categoryCatalogFrontendModel->getCheckFilters($filter)) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                die();
            }
        }

        // пользователь выбрал сортировку товаров?
        $sort = 0;
        if (isset($this->params['sort']) && in_array($this->params['sort'], array(1,2,3,4,5,6))) {
            $sort = (int)$this->params['sort'];
        }

        // пользователь выбрал кол-во товаров на странице?
        $perpage = 0;
        $others = $this->config->pager->frontend->products->getValue('others');
        if (isset($this->params['perpage']) && in_array($this->params['perpage'], $others)) {
            $perpage = (int)$this->params['perpage'];
        }

        return array($group, $maker, $hit, $new, $filter, $sort, $perpage);

    }

}