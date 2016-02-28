<?php
/**
 * Класс Xhr_Category_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранной категории
 */
class Xhr_Category_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

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

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // обрабатываем данные формы: фильтр по функционалу, производителю, лидерам продаж,
        // новинкам, параметрам; сортировка
        list($group, $maker, $hit, $new, $param, $sort) = $this->processFormData();

        // получаем от модели массив дочерних категорий
        $childs = $this->catalogFrontendModel->getChildCategories(
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

        // получаем от модели массив параметров подбора
        $params = $this->catalogFrontendModel->getCategoryGroupParams(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество лидеров продаж
        $countHit = $this->catalogFrontendModel->getCountCategoryHit(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param
        );

        // получаем от модели количество новинок
        $countNew = $this->catalogFrontendModel->getCountCategoryNew(
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
        $totalProducts = $this->catalogFrontendModel->getCountCategoryProducts( // общее кол-во товаров
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
            1,                                                  // текущая страница
            $totalProducts,                                     // общее кол-во товаров
            $this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // постраничная навигация не нужна
            $pager = null;
        }

        // получаем от модели массив товаров категории
        $products = $this->catalogFrontendModel->getCategoryProducts(
            $this->params['id'],
            $group,
            $maker,
            $hit,
            $new,
            $param,
            $sort,
            0
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

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        // формируем HTML результатов фильтрации товаров
        $output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/category.php',
            array(
                'id'          => $this->params['id'],       // id категории
                'view'        => $view,                     // представление списка товаров
                'childs'      => $childs,                   // массив дочерних категорий
                'group'       => $group,                    // id выбранной функциональной группы или ноль
                'maker'       => $maker,                    // id выбранного производителя или ноль
                'hit'         => $hit,                      // показывать только лидеров продаж?
                'countHit'    => $countHit,                 // количество лидеров продаж
                'new'         => $new,                      // показывать только новинки?
                'countNew'    => $countNew,                 // количество новинок
                'param'       => $param,                    // массив выбранных параметров подбора
                'groups'      => $groups,                   // массив функциональных групп
                'makers'      => $makers,                   // массив производителей
                'params'      => $params,                   // массив всех параметров подбора
                'sort'        => $sort,                     // выбранная сортировка
                'sortorders'  => $sortorders,               // массив вариантов сортировки
                'units'       => $units,                    // массив единиц измерения товара
                'products'    => $products,                 // массив товаров категории
                'pager'       => $pager,                    // постраничная навигация
                'page'        => 1,                         // текущая страница
            )
        );
        $output = explode('¤', $output);
        // дочерние категории, подбор по параметрам, список товаров
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
            if ( ! $this->catalogFrontendModel->getCheckParams($param)) {
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

}