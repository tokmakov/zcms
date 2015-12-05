<?php
/**
 * Класс Rmvprd_Rating_Backend_Controller отвечает за удаление товара рейтинга,
 * взаимодействует с моделью Rating_Backend_Model, административная часть сайта
 */
class Rmvprd_Rating_Backend_Controller extends Rating_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только удаление товара и редирект.
     */
    protected function input() {
        // идентификатор категории верхнего уровня, в которую вернется администратор
        // после успешного выполнения операции удаления товара и редиректа
        $return = 0;
        // если передан id товара и id товара целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $return = $this->ratingBackendModel->getProductRootCategory($this->params['id']);
            $this->ratingBackendModel->removeProduct($this->params['id']);
        }
        if ($return) {
            $this->redirect($this->ratingBackendModel->getURL('backend/rating/root/id/' . $return));
        } else {
            $this->redirect($this->ratingBackendModel->getURL('backend/rating/index'));
        }
    }
}