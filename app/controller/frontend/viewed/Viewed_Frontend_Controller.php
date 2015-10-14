<?php
/**
 * Класс Viewed_Frontend_Controller формирует страницу со списком всех
 * просмотренных посетителем товаров, получает данные от модели
 * Viewed_Frontend_Model, общедоступная часть сайта
 */
class Viewed_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех просмотренных посетителем товаров
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Viewed_Frontend_Controller
         */
        parent::input();

        $this->title = 'Вы уже смотрели. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->viewedFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->viewedFrontendModel->getURL('frontend/catalog/index'), 'name' => 'Каталог'),
        );

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во просмотренных товаров
        $totalProducts = $this->viewedFrontendModel->getCountViewedProducts();

        $temp = new Pager(
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

        // получаем от модели массив просмотренных посетителем товаров
        $viewedProducts = $this->viewedFrontendModel->getViewedProducts($start);

        // единицы измерения товара
        $units = $this->catalogFrontendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'    => $breadcrumbs,
            // URL ссылки на эту страницу
            'thisPageUrl'    => $this->viewedFrontendModel->getURL('frontend/viewed/index'),
            // массив просмотренных товаров
            'viewedProducts' => $viewedProducts,
            // массив единиц измерения товара
            'units'          => $units,
            // постраничная навигация
            'pager'          => $pager,
            // текущая страница
            'page'           => $page,
        );

    }

}
