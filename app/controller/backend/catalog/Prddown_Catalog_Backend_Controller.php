<?php
/**
 * Класс Prddown_Catalog_Backend_Controller опускает товар вниз в списке,
 * взаимодействует с моделью Catalog_Backend_Model, административная часть
 * сайта
 */
class Prddown_Catalog_Backend_Controller extends Catalog_Backend_Controller {




    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только опустить товар вниз в списке и сделать редирект.
     */
    protected function input() {
        // идентификатор категории, в которую вернется администратор после
        // успешного выполнения операции смещения товара вниз и редиректа
        $return = 0;
        // если передан id категории и id категории целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->catalogBackendModel->moveproductDown($this->params['id']);
            // идентификатор категории, в которую вернется администратор после редиректа
            $return = $this->catalogBackendModel->getProductParent($this->params['id']);
        }
        if ($return) {
            $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $return));
        } else {
            $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
        }
    }
}