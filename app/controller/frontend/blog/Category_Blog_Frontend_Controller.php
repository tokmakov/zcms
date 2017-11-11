<?php
/**
 * Класс Category_Blog_Frontend_Controller формирует страницу списка постов
 * выбранной категории блога, получает данные от модели Blog_Frontend_Model,
 * общедоступная часть сайта
 */
class Category_Blog_Frontend_Controller extends Blog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка постов выбранной категории блога
     */
    protected function input() {

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о категории
        $category = $this->blogFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * сначала обращаемся к родительскому классу Blog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Category_Blog_Frontend_Controller
         */
        parent::input();

        /*
         * заголовок страницы (тег <title>), мета-теги keywords и description
         */
        $this->title = $category['name'];
        if ( ! empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if ( ! empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Новости',
                'url'  => $this->blogFrontendModel->getURL('frontend/blog/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во постов в категории
        $totalPosts = $this->blogFrontendModel->getCountCategoryPosts($this->params['id']);
        // URL ссылки на эту страницу
        $thisPageURL = $this->blogFrontendModel->getURL('frontend/blog/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageURL,                                   // URL этой страницы
            $page,                                          // текущая страница
            $totalPosts,                                    // общее кол-во постов в категории
            $this->config->pager->frontend->blog->perpage,  // постов на страницу
            $this->config->pager->frontend->blog->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->blog->perpage;

        /*
         * получаем от модели массив постов выбранной категории
         */
        $posts = $this->blogFrontendModel->getCategoryPosts($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории
            'name'        => $category['name'],
            // массив постов выбранной категории
            'posts'       => $posts,
            // постраничная навигация
            'pager'       => $pager,
        );

    }

}
