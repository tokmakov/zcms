<?php
/**
 * Класс Index_Article_Frontend_Controller формирует страницу списка всех статей,
 * получает данные от модели Article_Frontend_Model, общедоступная часть сайта
 */
class Index_Article_Frontend_Controller extends Article_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка статей всех категорий
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Article_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->articleFrontendModel->getURL('frontend/index/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = $this->params['page'];
        }
        // общее кол-во статей
        $totalArticles = $this->articleFrontendModel->getCountAllArticles();
        // URL этой страницы
        $thisPageURL = $this->articleFrontendModel->getURL('frontend/article/index');
        $temp = new Pager(
            $thisPageURL,                                      // URL этой страницы
            $page,                                             // текущая страница
            $totalArticles,                                    // общее кол-во статей
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

        // получаем от модели массив статей
        $articles = $this->articleFrontendModel->getAllArticles($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив статей всех категорий
            'articles'    => $articles,
            // постраничная навигация
            'pager'       => $pager
        );

    }

}
