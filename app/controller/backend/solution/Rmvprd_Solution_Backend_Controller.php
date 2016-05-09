<?php
/**
 * Класс Rmvprd_Solution_Backend_Controller отвечает за удаление товара
 * из типового решения, взаимодействует с моделью Solution_Backend_Model,
 * административная часть сайта
 */
class Rmvprd_Solution_Backend_Controller extends Solution_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление товара и редирект.
     */
    protected function input() {
        // идентификатор типового решения, чтобы перенаправить администратора на
        // страницу со списом товаров этого типового решения после удаления товара
        $return = 0;
        // если передан id товара и id товара целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            // идентификатор типового решения, куда вернется администратор после редиректа
            $return = $this->solutionBackendModel->getProductParent($this->params['id']);
            // удаляем товар из типового решения
            $this->solutionBackendModel->removeSolutionProduct($this->params['id']);
        }
        if ($return) {
            $this->redirect($this->solutionBackendModel->getURL('backend/solution/show/id/' . $return));
        } else {
            $this->redirect($this->solutionBackendModel->getURL('backend/solution/index'));
        }
    }
}