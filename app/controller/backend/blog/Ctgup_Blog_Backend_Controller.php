<?php
/**
 * Класс Ctgup_Blog_Backend_Controller поднимает категорию вверх в списке,
 * взаимодействует с моделью Blog_Backend_Model, административная часть
 * сайта
 */
class Ctgup_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только поднять категорию вверх в списке и редирект.
     */
    protected function input() {
        // если передан id категории и id категории целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->blogBackendModel->moveCategoryUp($this->params['id']);
        }
        $this->redirect($this->blogBackendModel->getURL('backend/blog/allctgs'));
    }

}