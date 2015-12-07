<?php
/**
 * Класс Prdup_Sale_Backend_Controller поднимает товар вверх в списке,
 * взаимодействует с моделью Sale_Backend_Model, административная
 * часть сайта
 */
class Prdup_Sale_Backend_Controller extends Sale_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только поднять товар вверх в списке и сделать редирект.
     */
    protected function input() {
        // если передан id товара и id товара целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->saleBackendModel->moveProductUp($this->params['id']);
        }
        $this->redirect($this->saleBackendModel->getURL('backend/sale/index'));
    }
}