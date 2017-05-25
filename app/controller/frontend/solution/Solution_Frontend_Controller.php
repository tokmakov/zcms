<?php
/**
 * Абстрактный класс Solution_Frontend_Controller, родительский для всех
 * контроллеров, работающих с типовыми решениями, общедоступная часть сайта
 */
abstract class Solution_Frontend_Controller extends Frontend_Controller {

    /**
     * экземпляр класса модели для работы с типовыми решениями
     */
    protected $solutionFrontendModel;


    public function __construct($params = null) {
        parent::__construct($params);
        // экземпляр класса модели для работы с типовыми решениями
        $this->solutionFrontendModel =
            isset($this->register->solutionFrontendModel) ? $this->register->solutionFrontendModel : new Solution_Frontend_Model();
    }

    /**
     * Функция получает от моделей и из настроек данные, необходимые для
     * работы всех потомков класса Solution_Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Frontend_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех потомков
         * Solution_Frontend_Controller
         */
        parent::input();

        $this->title = $this->config->meta->solution->title;
        $this->keywords = $this->config->meta->solution->keywords;
        $this->description = $this->config->meta->solution->description;

    }

}