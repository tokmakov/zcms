<?php
/**
 * Класс Maker_Catalog_Frontend_Controller формирует страницу со списком
 * всех товаров выбранного производителя, получает данные от модели
 * Maker_Catalog_Frontend_Model, общедоступная часть сайта
 */
class Maker_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы:
     * форма фильтров + список товаров выбранного производителя
     */
    protected function input() {

        // если не передан id производителя или id производителя не число
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

        // получаем от модели информацию о производителе
        $maker = $this->makerCatalogFrontendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * обращаемся к родительскому классу Catalog_Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Maker_Catalog_Frontend_Controller
         */
        parent::input();

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
                'url'  => $this->makerCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->makerCatalogFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Производители',
                'url'  => $this->makerCatalogFrontendModel->getURL('frontend/catalog/makers')
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
            // проверяем корректность переданных параметров и значений
            if ( ! $this->makerCatalogFrontendModel->getCheckParams($param)) {
                $this->notFoundRecord = true;
                return;
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

        // запрещаем индексацию роботами поисковых систем, если
        // включен какой-нибудь фильтр или сортировка
        if ($group || $hit || $new || $sort) {
            $this->robots = false;
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
        // кол-во товаров на одной странице
        $perpage = $this->config->pager->frontend->products->perpage;
        $temp = $this->config->pager->frontend->products->others; // другие доступные варианты
        $others = array();
        foreach ($temp as $item) { $others[] = $item; }
        if (isset($this->params['perpage']) && in_array($this->params['perpage'], $others)) {
            $perpage = (int)$this->params['perpage'];
        }
        // общее кол-во товаров производителя с учетом фильтров по функционалу,
        // лидерам продаж и новинкам
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
        // постраничная навигация
        $temp = new Pager(
            $thisPageURL,                                       // URL этой страницы
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во товаров производителя
            $perpage,                                           // кол-во товаров на странице
            $this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $perpage;

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

        // ссылки для сортировки товаров по цене, наменованию, коду
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

        // атрибут action тега form
        $action = $this->makerCatalogFrontendModel->getURL(
            'frontend/catalog/maker/id/' . $this->params['id']
        );

        // URL ссылки для сборса фильтра
        $url = 'frontend/catalog/maker/id/' . $this->params['id'];
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        if ($perpage !== $this->config->pager->frontend->products->perpage) {
            $url = $url . '/perpage/' . $perpage;
        }
        $clearFilterURL = $this->makerCatalogFrontendModel->getURL($url);

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        // выбранный вариант кол-ва товаров на странице или ноль — если значение по умолчанию
        $perpage = ($perpage === $this->config->pager->frontend->products->perpage) ? 0 : $perpage;

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
            // представление списка товаров
            'view'           => $view,
            // id выбранной функциональной группы или ноль
            'group'          => $group,
            // массив функциональных групп
            'groups'         => $groups,
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
            // массив всех доступных параметров подбора
            'params'         => $params,
            // массив товаров производителя
            'products'       => $products,
            // выбранная сортировка или ноль
            'sort'           => $sort,
            // массив всех вариантов сортировки
            'sortorders'     => $sortorders,
            // выбранный вариант кол-ва товаров на странице или ноль
            'perpage'        => $perpage,
            // массив всех вариантов кол-ва товаров на страницу
            'perpages'       => $perpages,
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

        // базовый URL страницы производителя, без фильтров и сортировки
        $url = 'frontend/catalog/maker/id/' . $this->params['id'];
        // включен фильтр по функционалу (функциональной группе)?
        $grp = false;
        if (isset($_POST['group']) && ctype_digit($_POST['group'])&& $_POST['group'] > 0) {
            $url = $url . '/group/' . $_POST['group'];
            $grp = true;
        }
        // включен фильтр по лидерам продаж?
        if (isset($_POST['hit'])) {
            $url = $url . '/hit/1';
        }
        // включен фильтр по новинкам?
        if (isset($_POST['new'])) {
            $url = $url . '/new/1';
        }
        // включены доп.фильтры (параметры подбора для выбранного функционала)?
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
        // включена сортировка?
        if (isset($_POST['sort'])
            && ctype_digit($_POST['sort'])
            && in_array($_POST['sort'], array(1,2,3,4,5,6))
        ) {
            $url = $url . '/sort/' . $_POST['sort'];
        }
        // кол-во товаров на странице
        if (isset($_POST['perpage']) && ctype_digit($_POST['perpage'])) { // TODO: in_array 20, 50, 100
            $url = $url . '/perpage/' . $_POST['perpage'];
        }
        // выполняем редирект
        $this->redirect($this->makerCatalogFrontendModel->getURL($url));

    }

}
