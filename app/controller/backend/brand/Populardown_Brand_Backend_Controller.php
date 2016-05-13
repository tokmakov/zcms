<?php
/**
 * Класс Populardown_Brand_Backend_Controller опускает популярный бренд в списке вниз,
 * взаимодействует с моделью Brand_Backend_Model, административная часть сайта
 */
class Populardown_Brand_Backend_Controller extends Brand_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только опустить популярный бренд в списке вниз и сделать редирект.
     */
    protected function input() {
        // если передан id бренда и id бренда целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->brandBackendModel->movePopularDown($this->params['id']);
        }
        $this->redirect($this->brandBackendModel->getURL('backend/brand/index'));
    }
}