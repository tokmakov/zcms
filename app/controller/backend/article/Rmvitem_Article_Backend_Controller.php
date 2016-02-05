<?php
/**
 * Класс Rmvitem_Article_Backend_Controller отвечает за удаление статьи,
 * взаимодействует с моделью Article_Backend_Model, административная часть
 * сайта
 */
class Rmvitem_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы. В
     * данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только удаление статьи и редирект.
     */
    protected function input() {
        // если передан id статьи и id статьи целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->articleBackendModel->removeArticle($this->params['id']);
        }
        $this->redirect($this->articleBackendModel->getURL('backend/article/index'));
    }
}