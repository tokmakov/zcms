<?php
/**
 * Абстрактный класс Viewed_Frontend_Controller, родительский для всех
 * контроллеров, работающих с историей просмотров, общедоступная часть
 * сайта
 */
abstract class Viewed_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

}
