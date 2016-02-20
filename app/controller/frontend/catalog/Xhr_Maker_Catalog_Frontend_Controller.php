<?php
/**
 * Класс Xhr_Maker_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранного производителя
 */
class Xhr_Maker_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * результат фильтрации товаров в формате JSON
     */
    private $output;


    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // если не передан id производителя или id производителя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }
        
        // получаем от модели информацию о производителе
        $maker = $this->catalogFrontendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // обрабатываем данные формы: фильтр по функционалу, лидерам продаж,
        // новинкам, параметрам; сортировка
        list($group, $hit, $new, $param, $sort) = $this->processFormData();

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
        $totalProducts = $this->catalogFrontendModel->getCountMakerProducts( // общее кол-во товаров
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
            1,                                                  // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }

        // получаем от модели массив товаров производителя
        $products = $this->catalogFrontendModel->getMakerProducts(
            $this->params['id'],
            $group,
            $hit,
            $new,
            $param,
            $sort
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

        // формируем HTML результатов фильтрации товаров
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/maker.php',
            array(
                'id'          => $this->params['id'], // id производителя
                'name'        => $maker['name'],      // название производителя
                'group'       => $group,              // id выбранной функциональной группы или ноль
                'hit'         => $hit,                // показывать только лидеров продаж?
                'countHit'    => $countHit,           // количество лидеров продаж
                'new'         => $new,                // показывать только новинки?
                'countNew'    => $countNew,           // количество новинок
                'param'       => $param,              // массив выбранных параметров подбора
                'groups'      => $groups,             // массив функциональных групп
                'params'      => $params,             // массив всех параметров подбора
                'sort'        => $sort,               // выбранная сортировка
                'sortorders'  => $sortorders,         // массив вариантов сортировки
                'units'       => $units,              // массив единиц измерения товара
                'products'    => $products,           // массив товаров категории
                'pager'       => $pager,              // постраничная навигация
                'page'        => 1,                   // текущая страница
            )
        );
        $output = explode('¤', $output);
        // пусто, подбор по параметрам, список товаров
        $result = array('childs' => $output[0], 'filters' => $output[1], 'products' => $output[2]);
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
     * Вспомогательная функция, проводит первичную обработку данных формы
     */
    private function processFormData() {

        $group = 0; // функционал
        if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
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

        return array($group, $hit, $new, $param, $sort);

    }

}