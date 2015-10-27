<?php
/**
 * Класс Rmvparam_Filter_Backend_Controller отвечает за удаление параметра
 * подбора, взаимодействует с моделью Filter_Backend_Model, административная
 * часть сайта
 */
class Rmvparam_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление параметра подбора и редирект.
     */
    protected function input() {
        // если передан id параметра и id параметра целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->filterBackendModel->removeParam($this->params['id']);
        }
        $this->redirect($this->filterBackendModel->getURL('backend/filter/allparams'));
    }
}