<?php
/**
 * Класс Category_News_Backend_Controller формирует страницу со списком новостей
 * выбранной категории, получает данные от модели News_Backend_Model,
 * административная часть сайта
 */
class Category_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка новостей выбранной категории
     */
    protected function input() {

        // сначала обращаемся к родительскому классу News_Backend_Controller,
        // чтобы установить значения переменных, которые нужны для работы всех его
        // потомков, потом переопределяем эти переменные (если необходимо) и
        // устанавливаем значения перменных, которые нужны для работы только
        // Category_News_Backend_Controller
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели информацию о категории
        $category = $this->newsBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $category['name'] . '. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsBackendModel->getURL('backend/news/index'), 'name' => 'Новости'),
            array('url' => $this->newsBackendModel->getURL('backend/news/allctgs'), 'name' => 'Категории'),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во новостей категории
        $totalNews = $this->newsBackendModel->getCountCategoryNews($this->params['id']);
        // URL этой страницы
        $thisPageUrl = $this->newsBackendModel->getURL('backend/news/category/id/' . $this->params['id']);
        $temp = new Pager(
            $thisPageUrl,                                  // URL этой страницы
            $page,                                         // текущая страница
            $totalNews,                                    // общее кол-во новостей категории
            $this->config->pager->backend->news->perpage,  // кол-во новостей на страницу
            $this->config->pager->backend->news->leftright // кол-во ссылок слева и справа
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
        $start = ($page - 1) * $this->config->pager->backend->news->perpage;

        // получаем от модели массив новостей текущей категории
        $news = $this->newsBackendModel->getCategoryNews($this->params['id'], $start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,        // хлебные крошки
            'id'          => $this->params['id'], // уникальный идентификатор категории
            'name'        => $category['name'],   // наименование категории
            'news'        => $news,               // массив новостей категории
            'pager'       => $pager,              // постраничная навигация
        );

    }

}
