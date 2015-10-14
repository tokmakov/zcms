<?php
abstract class News_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от моделей и из настроект данные, необходимые для
     * работы всех потомков класса News_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков News_Frontend_Controller
         */
        parent::input();

        $this->title = $this->config->meta->news->title;
        $this->keywords = $this->config->meta->news->keywords;
        $this->description = $this->config->meta->news->description;

    }

}