<?php
/**
 * Абстрактный класс Rating_Backend_Controller, родительский для всех контроллеров,
 * работающих с товарами рейтинга продаж, административная часть сайта
 */
abstract class Rating_Backend_Controller extends Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от моделей данные, необходимые для работы всех
     * потомков класса Rating_Backend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Backend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех потомков
         * Rating_Backend_Controller
         */
        parent::input();

    }

}