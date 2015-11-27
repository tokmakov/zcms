<?php
/**
 * Класс Category_Catalog_Backend_Controller формирует страницу категории
 * каталога, т.е. список дочерних категорий и список товаров категории, получает
 * данные от модели Catalog_Backend_Model
 */
class Category_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * категории каталога (список дочерних категорий + список товаров категории
     * с постраничной навигацией)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Catalog_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели информацию о категории
        $category = $this->catalogBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'] . '. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = $this->catalogBackendModel->getCategoryPath($this->params['id']);
        // последний элемент - текущая категория, нам она не нужна
        array_pop($breadcrumbs);

        // получаем от модели массив дочерних категорий
        $childCategories = $this->catalogBackendModel->getChildCategories($this->params['id']);

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во товаров категории
        $totalProducts = $this->catalogBackendModel->getCountCategoryProducts($this->params['id']);
        // URL этой страницы
        $thisPageUrl = $this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageUrl,                                      // URL этой страницы
            $page,                                             // текущая страница
            $totalProducts,                                    // общее кол-во товаров категории
            $this->config->pager->backend->products->perpage,  // товаров на страницу
            $this->config->pager->backend->products->leftright // кол-во ссылок слева и справа
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
        $start = ($page - 1) * $this->config->pager->backend->products->perpage;

        // получаем от модели массив товаров категории
        $products = $this->catalogBackendModel->getCategoryProducts($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'     => $breadcrumbs,
            // уникальный идентификатор категории
            'id'              => $this->params['id'],
            // наименование категории
            'name'            => $category['name'],
            // URL ссылки для добавления категории
            'addCtgUrl'       => $this->catalogBackendModel->getURL('backend/catalog/addctg/parent/' . $this->params['id']),
            // URL ссылки для добавления товара
            'addPrdUrl'       => $this->catalogBackendModel->getURL('backend/catalog/addprd/category/' . $this->params['id']),
            // массив дочерних категорий
            'childCategories' => $childCategories,
            // массив товаров категории
            'products'        => $products,
            // постраничная навигация
            'pager'           => $pager,
        );

    }

}
