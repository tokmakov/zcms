<?php
/**
 * Класс Index_Page_Backend_Controller формирует страницу со списком всех
 * страниц сайта, получает данные от модели Page_Backend_Model
 */
class Index_Page_Backend_Controller extends Page_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком всех страниц сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Page_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Index_Page_Backend_Controller
         */
        parent::input();

        $this->title = 'Страницы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->pageBackendModel->getURL('backend/index/index')
            ),
        );

        // получаем от модели массив всех страниц сайта
        $pages = $this->pageBackendModel->getAllPages();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // URL ссылки на страницу с формой для добавления страницы
            'addPageUrl'  => $this->pageBackendModel->getURL('backend/page/add'),
            // массив всех страниц сайта
            'pages'       => $pages,
        );

    }

}