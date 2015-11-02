<?php
/**
 * Класс Rmvctg_Solutions_Backend_Controller отвечает за удаление категории,
 * взаимодействует с моделью Solutions_Backend_Model, административная часть
 * сайта
 */
class Rmvctg_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление категории и редирект.
     */
    protected function input() {
        // если передан id категории и id категории целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->solutionsBackendModel->removeCategory($this->params['id']);
        }
        $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/allctgs'));
    }
}