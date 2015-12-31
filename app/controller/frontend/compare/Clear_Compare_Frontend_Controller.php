<?php
/**
 * Класс Clear_Compare_Frontend_Controller отвечает за удаление всех товаров из
 * сравнения, взаимодействует с моделью Compare_Frontend_Model, общедоступная
 * часть сайта
 */
class Clear_Compare_Frontend_Controller extends Compare_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление всех товаров из списка сравнения и
     * редирект на страницу со списком товаров для сравнения.
     */
    protected function input() {

        // удаляем все товары из списка отложенных для сравнения
        $this->compareFrontendModel->clearCompareList();

        // редирект на страницу со списком товаров для сравнения
        $this->redirect($this->compareFrontendModel->getURL('frontend/compare/index'));
    }
}