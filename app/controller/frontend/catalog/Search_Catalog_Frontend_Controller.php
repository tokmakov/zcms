<?php
/**
 * Класс Search_Catalog_Frontend_Controller формирует страницу поиска по каталогу,
 * получает данные от модели Catalog_Frontend_Model
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
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Search_Catalog_Frontend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!empty($_POST['query'])) {
                $_POST['query'] = trim(utf8_substr(str_replace('/', '|', $_POST['query']), 0, 64));
                $this->redirect($this->catalogFrontendModel->getURL('frontend/catalog/search/query/' . rawurlencode($_POST['query'])));
            } else {
                $this->redirect($this->catalogFrontendModel->getURL('frontend/catalog/search'));
            }
        }

        $this->title = 'Поиск по каталогу. ' . $this->title;

        $this->keywords = 'поиск ' . $this->keywords;
        $this->description = 'Поиск по каталогу. ' . $this->description;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->catalogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url' => $this->catalogFrontendModel->getURL('frontend/catalog/index')
            )
        );

        if (empty($this->params['query'])) {
            $this->centerVars['action'] = $this->catalogFrontendModel->getURL('frontend/catalog/search');
            $this->centerVars['breadcrumbs'] = $breadcrumbs;
            return;
        }

        $this->params['query'] = str_replace('|', '/', $this->params['query']);

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) { // текущая страница
            $page = $this->params['page'];
        }
        // общее кол-во результатов поиска
        $totalProducts = $this->catalogFrontendModel->getCountSearchResults($this->params['query']);
        $temp = new Pager(
            $page,                                              // текущая страница
            $totalProducts,                                     // общее кол-во результатов поиска
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

        // получаем от модели массив результатов поиска
        $results = $this->catalogFrontendModel->getSearchResults($this->params['query'], $start);

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->catalogFrontendModel->getURL('frontend/catalog/search'),
            // URL этой страницы
            'thisPageUrl' => $this->catalogFrontendModel->getURL('frontend/catalog/search/query/' . rawurlencode($this->params['query'])),
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
