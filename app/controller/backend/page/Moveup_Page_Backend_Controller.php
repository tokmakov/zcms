<?php
/**
 * Класс Moveup_Page_Backend_Controller поднимает страницу в списке вверх,
 * взаимодействует с моделью Page_Backend_Model, административная часть сайта
 */
class Moveup_Page_Backend_Controller extends Page_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только поднять страницу в списке вверх и сделать редирект.
     */
    protected function input() {
        // если передан id страницы и id страницы целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->pageBackendModel->movePageUp($this->params['id']);
        }
        $this->redirect($this->pageBackendModel->getURL('backend/page/index'));
    }
}