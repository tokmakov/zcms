<?php
/**
 * Абстрактный класс Admin_Backend_Controller, родительский для всех контроллеров,
 * работающих с администратором сайта, административная часть сайта
 */
abstract class Admin_Backend_Controller extends Base_Controller {

    /**
     * экземпляр класса модели для работы с администратором сайта
     */
    protected $adminBackendModel;

    /**
     * администратор сайта авторизован?
     */
    protected $authAdmin = false;


    public function __construct($params = null) {

        $this->backend = true;
        parent::__construct($params);

        // экземпляр класса модели для работы с администратором сайта
        $this->adminBackendModel = new Admin_Backend_Model();

        // администратор сайта авторизован?
        $this->authAdmin = $this->adminBackendModel->isAuthAdmin();

    }

    protected function input() {
        /*
         * сначала обращаемся к родительскому классу Backend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Admin_Backend_Controller
         */
        parent::input();
    }

    /**
     * Функция формирует html-код отдельных частей страницы (меню,
     * основной контент, левая и правая колонка, подвал сайта и т.п.)
     */
    protected function output() {

        // получаем html-код тега <head>
        $this->headContent = $this->render(
            $this->headTemplateFile,
            array(
                'title' => $this->title,
                'cssFiles' => $this->cssFiles,
                'jsFiles' => $this->jsFiles,
            )
        );

        // получаем html-код шапки страницы
        $this->headerContent = $this->render(
            $this->headerTemplateFile,
            array()
        );

        // получаем html-код центральной колонки (основной контент)
        $this->centerContent = $this->render(
            $this->centerTemplateFile,
            $this->centerVars
        );

        // получаем html-код подвала страницы
        $this->footerContent = $this->render(
            $this->footerTemplateFile,
            array()
        );

        // html-код отдельных частей страницы получен, теперь формируем
        // всю страницу целиком
        $this->pageContent = $this->render(
            $this->wrapperTemplateFile,
            array(
                'headerContent' => $this->headerContent,
                'headContent'   => $this->headContent,
                'centerContent' => $this->centerContent,
                'footerContent' => $this->footerContent,
            )
        );

    }

}
