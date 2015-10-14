<?php
/**
 * Класс Allctgs_News_Backend_Controller формирует страницу со списком всех категорий
 * новостей, получает данные от модели News_Backend_Model, административная часть сайта
 */
class Allctgs_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        // сначала обращаемся к родительскому классу News_Backend_Controller,
        // чтобы установить значения переменных, которые нужны для работы всех его
        // потомков, потом переопределяем эти переменные (если необходимо) и
        // устанавливаем значения перменных, которые нужны для работы только
        // Allctgs_News_Backend_Controller
        parent::input();

        $this->title = 'Категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->newsBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->newsBackendModel->getURL('backend/news/index'), 'name' => 'Новости'),
        );

        // получаем от модели массив категорий новостей
        $categories = $this->newsBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех категорий новостей
            'categories'  => $categories,
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->newsBackendModel->getURL('backend/news/addctg'),
        );

    }

}