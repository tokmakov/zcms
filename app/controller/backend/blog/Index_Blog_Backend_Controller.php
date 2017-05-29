<?php
/**
 * Класс Index_Blog_Backend_Controller формирует страницу со списком всех
 * постов блога, получает данные от модели Blog_Backend_Model, административная
 * часть сайта
 */
class Index_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы со списком всех постов блога
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Blog_Backend_Controller
         */
        parent::input();

        $this->title = 'Посты. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogBackendModel->getURL('backend/index/index')
            ),
        );

        /*
         * постраничная навигация
         */
        $page = 1;
        if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
            $page = (int)$this->params['page'];
        }
        // общее кол-во постов
        $totalPosts = $this->blogBackendModel->getCountAllPosts();
        // URL этой страницы
        $thisPageUrl = $this->blogBackendModel->getURL('backend/blog/index');
        $temp = new Pager(
            $thisPageUrl,                                  // URL этой страницы
            $page,                                         // текущая страница
            $totalPosts,                                   // общее кол-во постов
            $this->config->pager->backend->blog->perpage,  // постов на страницу
            $this->config->pager->backend->blog->leftright // кол-во ссылок слева и справа
        );
        $pager = $temp->getNavigation();
        if (false === $pager) { // недопустимое значение $page (за границей диапазона)
            $this->notFoundRecord = true;
            return;
        }
        // стартовая позиция для SQL-запроса
        $start = ($page - 1) * $this->config->pager->backend->blog->perpage;

        // получаем от модели массив всех постов
        $posts = $this->blogBackendModel->getAllPosts($start);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех постов
            'posts'       => $posts,
            // постраничная навигация
            'pager'       => $pager,
            // URL ссылки на страницу с формой для добавления поста
            'addPostUrl'  => $this->blogBackendModel->getURL('backend/blog/addpost'),
            // URL ссылки на страницу со списком всех файлов
            'allFilesUrl' => $this->blogBackendModel->getURL('backend/blog/files'),
            // URL ссылки на страницу со списком всех категорий
            'allCtgsUrl'  => $this->blogBackendModel->getURL('backend/blog/allctgs')
        );

    }

}