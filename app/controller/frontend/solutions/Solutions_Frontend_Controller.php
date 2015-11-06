<?php
/**
 * Абстрактный класс Solutions_Frontend_Controller, родительский для всех
 * контроллеров, работающих с типовыми решениями, общедоступная часть сайта
 */
abstract class Solutions_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Catalog_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех потомков
         * Solutions_Frontend_Controller
         */
        parent::input();

        //$this->title = $this->config->meta->catalog->title;
        //$this->keywords = $this->config->meta->catalog->keywords;
        //$this->description = $this->config->meta->catalog->description;

    }

}