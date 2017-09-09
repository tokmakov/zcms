<?php
/**
 * Класс Xhr_Maker_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Maker_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранного производителя
 */
class Xhr_Maker_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результат фильтрации товаров в формате JSON, три фрагмента html-кода:
     * пустая строка, подбор по параметрам, список товаров производителя с
     * учетом фильтров
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Переопределяем метод Base_Controller::request(), потому что здесь нам не
     * нужен сложный алгоритм формирования страницы производителя каталога: получение
     * данных в методе input(), подключение js и css файлов, формирование отдельных
     * фрагментов HTML-кода, сборка страницы из фрагментов. Здесь просто запрашиваем
     * данные у модели и прогоняем их через шаблон, чтобы получить три фрагмента
     * html-кода.
     */
    public function request() {

        // если не передан id производителя или id производителя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о производителе
        $maker = $this->makerCatalogFrontendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
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
        if ($this->isPostMethod()) { // TODO: проверить, нужна здесь $page?
            // если данные отправлены методом POST, получаем данные из формы: фильтр
            // по функционалу, лидерам продаж, новинкам, параметрам и сортировка
            list($group, $hit, $new, $param, $sort, $perpage) = $this->processFormData();
        } else {
            // если данные отправлены методом GET, получаем данные из URL: фильтр
            // по функционалу, лидерам продаж, новинкам, параметрам и сортировка
            list($group, $hit, $new, $param, $sort, $perpage) = $this->processUrlData();
        }

        // получаем от модели массив функциональных групп
        $groups = $this->makerCatalogFrontendModel->getMakerGroups(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели массив всех параметров подбора
        $params = $this->makerCatalogFrontendModel->getMakerGroupParams(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->makerCatalogFrontendModel->getCountMakerHit(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->makerCatalogFrontendModel->getCountMakerNew(
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
        // параметрам подбора, лидерам продаж и новинкам
        $totalProducts = $this->makerCatalogFrontendModel->getCountMakerProducts(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param
        );
        // URL этой страницы
        $thisPageURL = $this->makerCatalogFrontendModel->getMakerURL(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort,
            $perpage
        );
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $perpage,                                           // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->products->perpage;

        // получаем от модели массив товаров производителя в кол-ве $perpage,
        // начиная с позации $start, с учетом фильтров по производителю,
        // новинкам, лидерам продаж и параметрам подбора
        $products = $this->makerCatalogFrontendModel->getMakerProducts(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort,
            $start,
            $perpage
        );

        // ссылки для сортировки товаров по цене, наименованию, коду
        $sortorders = $this->makerCatalogFrontendModel->getMakerSortOrders(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $perpage
        );

        // ссылки для переключения на показ 10,20,50,100 товаров на страницу
        $perpages = $this->makerCatalogFrontendModel->getOthersPerPage(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort,
            $perpage
        );

        // единицы измерения товара
        $units = $this->makerCatalogFrontendModel->getUnits();

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        // выбранный вариант кол-ва товаров на странице или ноль — если значение по умолчанию
        $perpage = ($perpage === $this->config->pager->frontend->products->perpage) ? 0 : $perpage;

        /*
         * Получаем три фрагмента html-кода, разделенные символом ¤:
         * пустая строка, подбор по параметрам, список товаров
         */
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/maker.php',
            array(
                'id'          => $this->params['id'], // уникальный идентификатор производителя
                'name'        => $maker['name'],      // название производителя
                'view'        => $view,               // представление списка товаров: линейный или плитка
                'group'       => $group,              // id выбранной функциональной группы или ноль
                'hit'         => $hit,                // показывать только лидеров продаж?
                'countHit'    => $countHit,           // количество лидеров продаж
                'new'         => $new,                // показывать только новинки?
                'countNew'    => $countNew,           // количество новинок
                'param'       => $param,              // массив выбранных параметров подбора
                'groups'      => $groups,             // массив функциональных групп
                'params'      => $params,             // массив всех параметров подбора
                'sort'        => $sort,               // выбранная сортировка или ноль
                'sortorders'  => $sortorders,         // массив вариантов сортировки
                'perpage'     => $perpage,            // выбранный вариант кол-ва товаров на странице или ноль
                'perpages'    => $perpages,           // массив всех вариантов кол-ва товаров на страницу
                'units'       => $units,              // массив единиц измерения товара
                'products'    => $products,           // массив товаров производителя с учетом фильтров
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

        $group = 0; // функционал
        if (isset($_POST['group']) && ctype_digit($_POST['group'])) {
            $group = (int)$_POST['group'];
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
            if ( ! $this->makerCatalogFrontendModel->getCheckParams($param)) {
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

        // кол-во товаров на странице
        $perpage = $this->config->pager->frontend->products->perpage;
        if (isset($_POST['perpage']) && ctype_digit($_POST['perpage'])) { // TODO: in_array 20, 50, 100
            $perpage = (int)$_POST['perpage'];
        }

        return array($group, $hit, $new, $param, $sort, $perpage);

    }

    /**
     * Вспомогательная функция, получает необходимые данные из URL
     */
    private function processUrlData() {

        $group = 0; // функционал
        if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
            $group = (int)$this->params['group'];
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
            if ( ! $this->makerCatalogFrontendModel->getCheckParams($param)) {
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

        // кол-во товаров на странице
        $perpage = $this->config->pager->frontend->products->perpage; // TODO: ноль или десять?
        if (isset($this->params['perpage']) && ctype_digit($this->params['perpage'])) {
            $perpage = (int)$this->params['perpage'];
        }

        return array($group, $hit, $new, $param, $sort, $perpage);

    }

}