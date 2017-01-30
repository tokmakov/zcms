<?php
/**
 * Абстрактный класс Menu_Backend_Controller, родительский для всех контроллеров,
 * работающих с пользователями сайта, общедоступная часть сайта
 */
abstract class User_Frontend_Controller extends Frontend_Controller {

    /**
     * информация об авторизованном пользователе
     */
    protected $user;


    public function __construct($params = null) {
        parent::__construct($params);
        // запрещаем индексацию роботами поисковых систем
        $this->robots = false;
    }

    /**
     * Функция получает от моделей и из настроект данные, необходимые для
     * работы всех потомков класса User_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы
         * всех его потомков, потом переопределяем эти переменные (если
         * необходимо) и устанавливаем значения перменных, которые нужны
         * для работы всех потомков User_Frontend_Controller
         */
        parent::input();

        // информация об авторизованном пользователе
        if ($this->authUser) {
            $this->user = $this->userFrontendModel->getUser();
        }

        $this->title = 'Личный кабинет';
        $this->keywords = 'личный кабинет, ' . $this->keywords;
        $this->description = 'Личный кабинет. ' . $this->description;

    }

}
