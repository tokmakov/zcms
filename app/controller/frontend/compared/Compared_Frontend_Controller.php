<?php
/**
 * Абстрактный класс Compared_Frontend_Controller, родительский для всех контроллеров,
 * работающих с отложенными для сравнения товарами, общедоступная часть сайта
 */
abstract class Compared_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

}
