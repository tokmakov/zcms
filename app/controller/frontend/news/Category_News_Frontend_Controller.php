<?php
/**
 * Класс Category_News_Frontend_Controller формирует страницу списка новостей
 * выбранной категории, получает данные от модели News_Frontend_Model,
 * общедоступная часть сайта
 */
class Category_News_Frontend_Controller extends News_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка новостей выбранной категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_News_Frontend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели данные о категории
        $category = $this->newsFrontendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'];
        if (!empty($category['keywords'])) {
            $this->keywords = $category['keywords'];
        }
        if (!empty($category['description'])) {
            $this->description = $category['description'];
        }

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsFrontendModel->getURL('frontend/news/index'), 'name' => 'Новости'),
        );

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во новостей категории
        $totalNews = $this->newsFrontendModel->getCountCategoryNews($this->params['id']);

        $temp = new Pager(
            $page,                                          // текущая страница
            $totalNews,                                     // общее кол-во новостей категории
            $this->config->pager->frontend->news->perpage,  // новостей на страницу
            $this->config->pager->frontend->news->leftright // кол-во ссылок слева и справа
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
        $start = ($page - 1) * $this->config->pager->frontend->news->perpage;

        // получаем от модели массив новостей текущей категории
        $news = $this->newsFrontendModel->getCategoryNews($this->params['id'], $start);

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
            // массив новостей выбранной категории
            'news'        => $news,
            // URL ссылки на эту страницу
            'thisPageUrl' => $this->newsFrontendModel->getURL('frontend/news/category/id/' . $this->params['id']),
            // постраничная навигация
            'pager'       => $pager,
        );

    }

}
