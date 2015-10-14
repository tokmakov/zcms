<?php
/**
 * Абстрактный класс Cache_Backend_Controller, родительский для всех контроллеров,
 * работающих с кэшем, административная часть сайта
 */
abstract class Cache_Backend_Controller extends Backend_Controller {

    /**
     * экземпляр класса для работы с кэшем
     */
    protected $cache;


    public function __construct($params = null) {
        parent::__construct($params);
        // экземпляр класса для работы с кэшем
        $this->cache = Cache::getInstance();
    }

    /**
     * Функция получает данные, необходимые для работы всех потомков класса
     * Cache_Backend_Controller
     */
    protected function input() {
        /*
         * сначала обращаемся к родительскому классу Backend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Cache_Backend_Controller
         */
        parent::input();
    }

}