<?php
/**
 * Класс Index_Article_Backend_Controller формирует страницу со списком всех
 * статей, получает данные от модели Article_Backend_Model
 */
class Index_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех статей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Article_Backend_Controller
         */
        parent::input();

        $this->title = 'Статьи. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->articleBackendModel->getURL('backend/index/index')
            ),
        );

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во статей
        $totalArticles = $this->articleBackendModel->getCountAllArticles();
        // URL этой страницы
        $thisPageURL = $this->articleBackendModel->getURL('backend/article/index');
        $temp = new Pager(
            $thisPageURL,                                     // URL этой страницы
            $page,                                            // текущая страница
            $totalArticles,                                   // общее кол-во статей
            $this->config->pager->backend->article->perpage,  // статей на страницу
            $this->config->pager->backend->article->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->backend->article->perpage;

        // получаем от модели массив всех статей
        $articles = $this->articleBackendModel->getAllArticles($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех статей
            'articles'    => $articles,
            // постраничная навигация
            'pager'       => $pager,
            // URL ссылки на страницу с формой для добавления статьи
            'addItemURL'  => $this->articleBackendModel->getURL('backend/article/additem'),
            // URL ссылки на страницу с формой для добавления категории
            'addCtgURL'   => $this->articleBackendModel->getURL('backend/article/addctg'),
            // URL ссылки на страницу со списком всех категорий
            'allCtgsURL'  => $this->articleBackendModel->getURL('backend/article/allctgs')
        );

    }

}