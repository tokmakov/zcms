<?php
/**
 * Класс Category_Blog_Backend_Controller формирует страницу со списком постов
 * выбранной категории, получает данные от модели Blog_Backend_Model,
 * административная часть сайта
 */
class Category_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком постов выбранной категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Category_Blog_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели информацию о категории
        $category = $this->blogBackendModel->getCategory($this->params['id']);
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
                'url'  => $this->blogBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Блог',
                'url'  => $this->blogBackendModel->getURL('backend/blog/index')
            ),
            array(
                'name' => 'Категории',
                'url'  => $this->blogBackendModel->getURL('backend/blog/allctgs')
            )
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во новостей категории
        $totalPosts = $this->blogBackendModel->getCountCategoryPosts($this->params['id']);
        // URL этой страницы
        $thisPageUrl = $this->blogBackendModel->getURL('backend/blog/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageUrl,                                  // URL этой страницы
            $page,                                         // текущая страница
            $totalPosts,                                   // общее кол-во постов категории
            $this->config->pager->backend->blog->perpage,  // кол-во новостей на страницу
            $this->config->pager->backend->blog->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->backend->blog->perpage;

        // получаем от модели массив постов текущей категории
        $posts = $this->blogBackendModel->getCategoryPosts($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,        // хлебные крошки
            'id'          => $this->params['id'], // уникальный идентификатор категории
            'name'        => $category['name'],   // наименование категории
            'posts'       => $posts,              // массив постов категории
            'pager'       => $pager,              // постраничная навигация
        );

    }

}
