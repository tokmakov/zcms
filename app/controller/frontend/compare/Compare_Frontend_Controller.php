<?php
/**
 * Абстрактный класс Compare_Frontend_Controller, родительский для всех
 * контроллеров, работающих с товарами для сравнения, общедоступная часть
 * сайта
 */
abstract class Compare_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

}
