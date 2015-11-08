<?php
/**
 * Класс Rmvsltn_Solutions_Backend_Controller отвечает за удаление типового решения,
 * взаимодействует с моделью Solutions_Backend_Model, административная часть сайта
 */
class Rmvsltn_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление типового решения и редирект.
     */
    protected function input() {
        // идентификатор категории, куда будет перенаправлен администратор
        // после выполнения удаления типового решения
        $return = 0;
        // если передан id типового решения и id типового решения целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            // идентификатор категории, куда вернется администратор после редиректа
            $return = $this->solutionsBackendModel->getSolutionCategory($this->params['id']);
            // удаляем типовое решение
            $this->solutionsBackendModel->removeSolution($this->params['id']);
        }
        if ($return) {
            $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/category/id/' . $return));
        } else {
            $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/index'));
        }
    }
}