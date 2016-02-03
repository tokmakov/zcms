<?php
/**
 * Класс Rmvnews_News_Backend_Controller отвечает за удаление новости,
 * взаимодействует с моделью News_Backend_Model, административная часть сайта
 */
class Rmvnews_News_Backend_Controller extends News_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только удаление новости и редирект.
     */
    protected function input() {
        // если передан id новости и id новости целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->newsBackendModel->removeNewsItem($this->params['id']);
        }
        $this->redirect($this->newsBackendModel->getURL('backend/news/index'));
    }
}