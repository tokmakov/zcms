<?php
/**
 * Абстрактный класс Wished_Frontend_Controller, родительский для всех контроллеров,
 * работающих с отложенными товарами, общедоступная часть сайта
 */
abstract class Wished_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

}
