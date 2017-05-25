<?php
/**
 * Абстрактный класс Blog_Frontend_Controller, родительский для всех
 * контроллеров, работающих с блогом, общедоступная часть сайта
 */
abstract class Blog_Frontend_Controller extends Frontend_Controller {

    /**
     * экземпляр класса модели для работы с блогом
     */
    protected $blogFrontendModel;

    public function __construct($params = null) {
        parent::__construct($params);
        // экземпляр класса модели для работы с блогом
        $this->blogFrontendModel =
            isset($this->register->blogFrontendModel) ? $this->register->blogFrontendModel : new Blog_Frontend_Model();
    }

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Blog_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Blog_Frontend_Controller
         */
        parent::input();

        $this->title = $this->config->meta->blog->title;
        $this->keywords = $this->config->meta->blog->keywords;
        $this->description = $this->config->meta->blog->description;

    }

}