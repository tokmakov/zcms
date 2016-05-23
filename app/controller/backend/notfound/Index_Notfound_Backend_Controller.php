<?php
/**
 * Класс Index_Notfound_Backend_Controller формирует страницу 404 Not Found,
 * административная часть сайта
 */
class Index_Notfound_Backend_Controller extends Backend_Controller {

    /**
     * экземпляр класса модели; класс-пустышка, предоставляющий доступ
     * к родительским свойствам и методам
     */
    protected $notfoundBackendModel;


    public function __construct($params = null) {
        $this->notFound = true;
        parent::__construct($params);
        // экземпляр класса модели
        $this->notfoundBackendModel = new Notfound_Backend_Model();
    }

    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Backend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Notfound_Backend_Controller
         */
        parent::input();

        $this->title  = 'Страница не найдена. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->notfoundBackendModel->getURL('backend/index/index')
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
