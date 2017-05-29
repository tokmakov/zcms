<?php
/**
 * Класс Category_Article_Backend_Controller формирует страницу со списком статей
 * выбранной категории, получает данные от модели Article_Backend_Model,
 * административная часть сайта
 */
class Category_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка статей выбранной категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Article_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели информацию о категории
        $category = $this->articleBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'] . '. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->articleBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleBackendModel->getURL('backend/article/index')
            ),
            array(
                'name' => 'Категории',
                'url'  => $this->articleBackendModel->getURL('backend/article/allctgs')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во статей категории
        $totalArticles = $this->articleBackendModel->getCountCategoryArticles($this->params['id']);
        // URL этой страницы
        $thisPageUrl = $this->articleBackendModel->getURL('backend/article/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageUrl,                                     // URL этой страницы
            $page,                                            // текущая страница
            $totalArticles,                                   // общее кол-во статей категории
            $this->config->pager->backend->article->perpage,  // кол-во статей на страницу
            $this->config->pager->backend->article->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->backend->article->perpage;

        // получаем от модели массив статей текущей категории
        $articles = $this->articleBackendModel->getCategoryArticles($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,        // хлебные крошки
            'id'          => $this->params['id'], // уникальный идентификатор категории
            'name'        => $category['name'],   // наименование категории
            'articles'    => $articles,           // массив статей категории
            'pager'       => $pager,              // постраничная навигация
        );

    }

}
