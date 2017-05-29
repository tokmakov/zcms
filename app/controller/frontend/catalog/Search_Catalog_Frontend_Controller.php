<?php
/**
 * Класс Search_Catalog_Frontend_Controller формирует страницу поиска по каталогу,
 * получает данные от модели Search_Catalog_Frontend_Model, общедоступная часть
 * сайта
 */
class Search_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * результатов поиска по каталогу
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Search_Catalog_Frontend_Controller
         */
        parent::input();

        /*
         * если данные формы были отправлены, перенаправляем пользователя на эту
         * же страницу, только поисковый запрос (например «ABC-123») будет частью
         * URL страницы: www.server.com/catalog/search/query/ABC-123
         */
        if ($this->isPostMethod()) {
            if ( ! empty($_POST['query'])) {
                /*
                 * если строка поиска содержит «/» (слэш), например «ABC-123/45»,
                 * то $_SERVER['REQUEST_URI'] = /catalog/search/query/ABC-123/45,
                 * роутер не сможет правильно разобрать $_SERVER['REQUEST_URI'];
                 * см. файл app/include/Router.php
                 */
                $query = trim(iconv_substr(str_replace('/', '|', $_POST['query']), 0, 64));
                $temp = 'frontend/catalog/search/query/' . rawurlencode($query);
                $this->redirect($this->searchCatalogFrontendModel->getURL($temp));
            } else {
                // пустой поисковый запрос, просто показываем страницу поиска по каталогу
                $this->redirect($this->searchCatalogFrontendModel->getURL('frontend/catalog/search'));
            }
        }

        $this->title = 'Поиск по каталогу. ' . $this->title;

        $this->keywords = 'поиск ' . $this->keywords;
        $this->description = 'Поиск по каталогу. ' . $this->description;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->searchCatalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->searchCatalogFrontendModel->getURL('frontend/catalog/index')
            )
        );

        // представление списка товаров: линейный или плитка
        $view = 'line';
        if (isset($_COOKIE['view']) && $_COOKIE['view'] == 'grid') {
            $view = 'grid';
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php, если поисковый запрос пустой
         */
        if (empty($this->params['query'])) {
            $this->centerVars['action'] = $this->searchCatalogFrontendModel->getURL('frontend/catalog/search');
            $this->centerVars['breadcrumbs'] = $breadcrumbs;
            $this->centerVars['view'] = $view;
            return;
        }

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = (int)$this->params['page'];
        }
        // общее кол-во результатов поиска
        $totalProducts = $this->searchCatalogFrontendModel->getCountSearchResults($this->params['query']);
        // базовый URL страницы поиска
        $thisPageUrl = $this->searchCatalogFrontendModel->getURL(
            'frontend/catalog/search/query/' . rawurlencode($this->params['query'])
        );
        $temp = new Pager(
            $thisPageUrl,                                       // базовый URL страницы поиска
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во результатов поиска
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

        /*
         * см. причины такой замены в комментариях выше, в начале метода
         */
        $this->params['query'] = str_replace('|', '/', $this->params['query']);

        // получаем от модели массив результатов поиска
        $results = $this->searchCatalogFrontendModel->getSearchResults(
            $this->params['query'],
            $start,
            false
        );

        // единицы измерения товара
        $units = $this->searchCatalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->searchCatalogFrontendModel->getURL('frontend/catalog/search'),
            // представление списка товаров
            'view'        => $view,
            // поисковый запрос
            'query'       => $this->params['query'],
            // массив результатов поиска
            'results'     => $results,
            // массив единиц измерения товара
            'units'       => $units,
            // постраничная навигация
            'pager'       => $pager,
            // текущая страница
            'page'        => $page,
        );

    }

}
