<?php
/**
 * Абстрактный класс Basket_Frontend_Controller, родительский для всех контроллеров,
 * работающих с покупательской корзиной, общедоступная часть сайта
 */
abstract class Basket_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Basket_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Basket_Frontend_Controller
         */
        parent::input();

        $this->title = 'Корзина. ' . $this->title;
        $this->keywords = 'корзина ' . $this->keywords;
        $this->description = 'Корзина. ' . $this->description;

    }

}