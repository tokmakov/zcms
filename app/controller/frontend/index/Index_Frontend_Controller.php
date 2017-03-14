<?php
/**
 * Абстрактный класс Index_Frontend_Controller, родительский для контроллера
 * Index_Index_Frontend_Controller, фомирующего главную страницу сайта
 */
abstract class Index_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Index_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Index_Frontend_Controller
         */
        parent::input();

    }

}
