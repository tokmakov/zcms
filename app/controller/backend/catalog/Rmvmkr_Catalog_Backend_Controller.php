<?php
/**
 * Класс Rmvmkr_Catalog_Backend_Controller отвечает за удаление производителя,
 * взаимодействует с моделью Catalog_Backend_Model, административная часть сайта
 */
class Rmvmkr_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление производителя и редирект.
     */
    protected function input() {
        // если передан id производителя и id производителя число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->catalogBackendModel->removeMaker($this->params['id']);
        }
        $this->redirect($this->catalogBackendModel->getURL('backend/catalog/allmkrs'));
    }
}