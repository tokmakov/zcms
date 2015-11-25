<?php
/**
 * Класс Index_News_Frontend_Controller формирует страницу списка новостей
 * (всех категорий), получает данные от модели News_Frontend_Model, общедоступная
 * часть сайта
 */
class Index_News_Frontend_Controller extends News_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка новостей всех категорий
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_News_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во новостей всех категорий
        $totalNews = $this->newsFrontendModel->getCountAllNews();
        // URL этой страницы
        $thisPageURL = $this->newsFrontendModel->getURL('frontend/news/index');
        $temp = new Pager(
            $thisPageURL,                                   // URL этой страницы
            $page,                                          // текущая страница
            $totalNews,                                     // общее кол-во новостей
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

        // получаем от модели массив новостей
        $news = $this->newsFrontendModel->getAllNews($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив новостей всех категорий
            'news' => $news,
            // постраничная навигация
            'pager' => $pager
        );

    }

}
