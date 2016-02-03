<?php
/**
 * Класс Allctgs_Article_Backend_Controller формирует страницу со списком всех категорий
 * статей, получает данные от модели Article_Backend_Model, административная часть сайта
 */
class Allctgs_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех категорий статей
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Allctgs_Article_Backend_Controller
         */
        parent::input();

        $this->title = 'Категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url' => $this->articleBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url' => $this->articleBackendModel->getURL('backend/article/index')
            ),
        );

        // получаем от модели массив всех категорий
        $categories = $this->articleBackendModel->getAllCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // массив всех категорий статей
            'categories'  => $categories,
            // URL ссылки на страницу с формой для добавления категории
            'addCtgUrl'   => $this->articleBackendModel->getURL('backend/article/addctg'),
        );

    }

}