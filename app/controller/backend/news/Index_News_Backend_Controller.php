<?php
/**
 * Класс Index_News_Backend_Controller формирует страницу со списком всех
 * новостей, получает данные от модели News_Backend_Model
 */
class Index_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех новостей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу News_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_News_Backend_Controller
         */
        parent::input();

        $this->title = 'Новости. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogBackendModel->getURL('backend/index/index')
            ),
        );

        // постраничная навигация
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во новостей
        $totalNews = $this->newsBackendModel->getCountAllNews();
        // URL этой страницы
        $thisPageUrl = $this->newsBackendModel->getURL('backend/news/index');
        $temp = new Pager(
            $thisPageUrl,                                  // URL этой страницы
            $page,                                         // текущая страница
            $totalNews,                                    // общее кол-во новостей
            $this->config->pager->backend->news->perpage,  // новостей на страницу
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

        // получаем от модели массив всех новостей
        $news = $this->newsBackendModel->getAllNews($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех новостей
            'news'        => $news,
            // постраничная навигация
            'pager'       => $pager,
            // URL ссылки на страницу с формой для добавления новости
            'addNewsUrl'  => $this->newsBackendModel->getURL('backend/news/addnews'),
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->newsBackendModel->getURL('backend/news/addctg'),
            // URL ссылки на страницу со списком всех категорий
            'allCtgsUrl'  => $this->newsBackendModel->getURL('backend/news/allctgs')
        );

    }

}