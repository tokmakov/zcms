<?php
/**
 * Класс Category_Article_Frontend_Controller формирует страницу списка статей
 * выбранной категории, получает данные от модели Article__Frontend_Model,
 * общедоступная часть сайта
 */
class Category_Article_Frontend_Controller extends Article_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка статей выбранной категории
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
        $category = $this->articleFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * Сначала обращаемся к родительскому классу Article_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Article_Frontend_Controller
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
                'url'  => $this->articleFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleFrontendModel->getURL('frontend/article/index')
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
        $totalArticles = $this->articleFrontendModel->getCountCategoryArticles($this->params['id']);
        // URL ссылки на эту страницу
        $thisPageURL = $this->articleFrontendModel->getURL('frontend/article/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageURL,                                      // URL этой страницы
            $page,                                             // текущая страница
            $totalArticles,                                    // общее кол-во статей категории
            $this->config->pager->frontend->article->perpage,  // статей на страницу
            $this->config->pager->frontend->article->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->frontend->article->perpage;

        /*
         * получаем от модели массив статей текущей категории
         */
        $articles = $this->articleFrontendModel->getCategoryArticles($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории (загловок <h1>)
            'name'        => $category['name'],
            // массив статей выбранной категории
            'articles'    => $articles,
            // постраничная навигация
            'pager'       => $pager,
        );

    }

}
