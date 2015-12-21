<?php
/**
 * Класс Allctgs_Blog_Backend_Controller формирует страницу со списком всех категорий
 * блога, получает данные от модели Blog_Backend_Model, административная часть сайта
 */
class Allctgs_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех категорий
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allctgs_Blog_Backend_Controller
         */
        parent::input();

        $this->title = 'Категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->blogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->blogBackendModel->getURL('backend/blog/index'), 'name' => 'Блог'),
        );

        // получаем от модели массив категорий
        $categories = $this->blogBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех категорий
            'categories'  => $categories,
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->blogBackendModel->getURL('backend/blog/addctg'),
            // URL ссылки на страницу со списком всех файлов
            'allFilesUrl' => $this->blogBackendModel->getURL('backend/blog/files'),
            // URL ссылки на страницу со списком всех постов
            'allPostsUrl' => $this->blogBackendModel->getURL('backend/blog/index')
        );

    }

}