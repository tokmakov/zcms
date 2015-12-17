<?php
/**
 * Класс Rmvpost_Blog_Backend_Controller отвечает за удаление поста блога,
 * взаимодействует с моделью Blog_Backend_Model, административная часть сайта
 */
class Rmvpost_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление поста и редирект.
     */
    protected function input() {
        // если передан id поста и id поста целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->blogBackendModel->removePost($this->params['id']);
        }
        $this->redirect($this->blogBackendModel->getURL('backend/blog/index'));
    }
}