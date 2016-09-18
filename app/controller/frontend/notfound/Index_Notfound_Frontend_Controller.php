<?php
/**
 * Класс Index_Notfound_Frontend_Controller формирует страницу 404 Not Found,
 * общедоступная часть сайта
 */
class Index_Notfound_Frontend_Controller extends Frontend_Controller {

    /**
     * экземпляр класса модели; класс-пустышка, предоставляющий доступ
     * к родительским свойствам и методам
     */
    protected $notfoundFrontendModel;


    public function __construct($params = null) {
        $this->notFound = true;
        parent::__construct($params);
        // экземпляр класса модели
        $this->notfoundFrontendModel = new Notfound_Frontend_Model();
    }

    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Notfound_Frontend_Controller
         */
        parent::input();

        $this->title = 'Страница не найдена';

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->notfoundFrontendModel->getURL('frontend/index/index')
            ),
        );

        /*
         * переменные, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            'breadcrumbs' => $breadcrumbs,  // хлебные крошки
        );
    }

}
