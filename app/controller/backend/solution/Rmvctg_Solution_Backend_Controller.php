<?php
/**
 * Класс Rmvctg_Solution_Backend_Controller отвечает за удаление категории,
 * взаимодействует с моделью Solution_Backend_Model, административная часть
 * сайта
 */
class Rmvctg_Solution_Backend_Controller extends Solution_Backend_Controller {

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
            $this->solutionBackendModel->removeCategory($this->params['id']);
        }
        // перенаправляем администратора обратно на страницу со списком категорий
        $this->redirect($this->solutionBackendModel->getURL('backend/solution/allctgs'));
    }

}