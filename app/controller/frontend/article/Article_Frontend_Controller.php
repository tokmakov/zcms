<?php
/**
 * Абстрактный класс Article_Frontend_Controller, родительский для всех
 * контроллеров, работающих со статьями, общедоступная часть сайта
 */
abstract class Article_Frontend_Controller extends Frontend_Controller {

    /**
     * экземпляр класса модели для работы со статьями
     */
    protected $articleFrontendModel;

    public function __construct($params = null) {
        parent::__construct($params);
        // экземпляр класса модели для работы со статьями
        $this->articleFrontendModel =
            isset($this->register->articleFrontendModel) ? $this->register->articleFrontendModel : new Article_Frontend_Model();
    }

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Article_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Article_Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения для мета-тегов
        $this->title = $this->config->meta->article->title;
        $this->keywords = $this->config->meta->article->keywords;
        $this->description = $this->config->meta->article->description;

    }

}