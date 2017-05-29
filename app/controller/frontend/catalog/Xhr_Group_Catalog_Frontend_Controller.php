<?php
/**
 * Класс Xhr_Group_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Group_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранной функциональной
 * группы
 */
class Xhr_Group_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результат фильтрации товаров в формате JSON, три фрагмента html-кода:
     * пустая строка, подбор по параметрам, список товаров функциональной
     * группы с учетом фильтров
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если не передан id функциональной группы или id группы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о функциональной группе
        $name = $this->groupCatalogFrontendModel->getGroupName($this->params['id']);
        // если запрошенная функциональная группа не найдена в БД
        if (empty($name)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        /*
         * Когда пользователь выбирает производителя, параметры подбора, включает
         * фильтр по новинкам или лидерам продаж, данные отправляются методом POST
         * по событию change элементов формы.
         * Когда пользователь нажимает кнопки «Назад» и «Вперед» в браузере, данные
         * отправляются методом GET по событию popstate, см. описание window.history.
         */
        if ($this->isPostMethod()) {
            // если данные отправлены методом POST, получаем данные из формы: фильтр
            // по производителю, лидерам продаж, новинкам, параметрам и сортировка
            list($maker, $hit, $new, $param, $sort) = $this->processFormData();
        } else {
            // если данные отправлены методом GET, получаем данные из URL: фильтр
            // по производителю, лидерам продаж, новинкам, параметрам и сортировка
            list($maker, $hit, $new, $param, $sort) = $this->processUrlData();
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
        // общее кол-во товаров функциональной группы с учетом фильтров по производителю,
        // параметрам подбора, лидерам продаж и новинкам
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

        // получаем от модели массив всех товаров функциональной группы с учетом
        // фильтров по производителю, параметрам подбора, лидерам продаж и новинкам
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

        // ссылки для сортировки товаров по цене, наименованию, коду
        $sortorders = $this->groupCatalogFrontendModel->getGroupSortOrders(
            $this->params['id'],
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
         * пустая строка, подбор по параметрам, список товаров
         */
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/group.php',
            array(
                'id'          => $this->params['id'], // уникальны идентификатор функциональной группы
                'name'        => $maker['name'],      // название функциональной группы: линейный или плитка
                'view'        => $view,               // представление списка товаров
                'maker'       => $maker,              // id выбранного производителя или ноль
                'makers'      => $makers,             // массив всех производителей
                'hit'         => $hit,                // показывать только лидеров продаж?
                'countHit'    => $countHit,           // количество лидеров продаж
                'new'         => $new,                // показывать только новинки?
                'countNew'    => $countNew,           // количество новинок
                'param'       => $param,              // массив выбранных параметров подбора
                'params'      => $params,             // массив всех параметров подбора
                'sort'        => $sort,               // выбранная сортировка или ноль
                'sortorders'  => $sortorders,         // массив вариантов сортировки
                'units'       => $units,              // массив единиц измерения товара
                'products'    => $products,           // массив товаров функциональной группы с учетом фильтров
                'pager'       => $pager,              // постраничная навигация
                'page'        => $page,               // текущая страница
            )
        );
        // разделяем три фрагмента html-кода по символу ¤
        $output = explode('¤', $output);
        // пусто, подбор по параметрам, список товаров
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
        if (isset($_POST['param'])) {
            foreach ($_POST['param'] as $key => $value) {
                if ($key > 0 && ctype_digit($value) && $value > 0) {
                    $param[$key] = (int)$value;
                }
            }
            // проверяем корректность переданных параметров и значений
            if ( ! $this->groupCatalogFrontendModel->getCheckParams($param)) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                die();
            }
        }

        $sort = 0; // сортировка
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $sort = (int)$_POST['sort'];
        }

        return array($maker, $hit, $new, $param, $sort);

    }

    /**
     * Вспомогательная функция, получает необходимые данные из URL
     */
    private function processUrlData() {

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

        return array($maker, $hit, $new, $param, $sort);

    }

}