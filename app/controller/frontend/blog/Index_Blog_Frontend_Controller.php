<?php
/**
 * Класс Index_Blog_Frontend_Controller формирует страницу списка постов блога
 * (всех категорий), получает данные от модели Blog_Frontend_Model, общедоступная
 * часть сайта
 */
class Index_Blog_Frontend_Controller extends Blog_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * списка постов блога всех категорий
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Blog_Frontend_Controller
         */
        parent::input();

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogFrontendModel->getURL('frontend/index/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во постов блога всех категорий
        $totalPosts = $this->blogFrontendModel->getCountAllPosts();
        // URL этой страницы
        $thisPageURL = $this->blogFrontendModel->getURL('frontend/blog/index');
        $temp = new Pager(
            $thisPageURL,                                   // URL этой страницы
            $page,                                          // текущая страница
            $totalPosts,                                    // общее кол-во постов блога
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
         * получаем от модели массив всех постов блога
         */
        $posts = $this->blogFrontendModel->getAllPosts($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив постов блога всех категорий
            'posts'       => $posts,
            // постраничная навигация
            'pager'       => $pager
        );

    }

}
