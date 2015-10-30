<?php
/**
 * Класс Itemdown_Menu_Backend_Controller опускает пункт меню вниз,
 * взаимодействует с моделью Menu_Backend_Model, административная часть сайта
 */
class Itemdown_Menu_Backend_Controller extends Menu_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только опустить пункт меню вниз и сделать редирект.
     */
    protected function input() {
        // если передан id пункта меню и id пункта меню целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->menuBackendModel->moveMenuItemDown($this->params['id']);
        }
        $this->redirect($this->menuBackendModel->getURL('backend/menu/index'));
    }
}