<?php
/**
 * Класс Movedown_Partner_Backend_Controller опускает партнера в списке вниз,
 * взаимодействует с моделью Partner_Backend_Model, административная часть сайта
 */
class Movedown_Partner_Backend_Controller extends Partner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только опустить партнера в списке вниз и сделать редирект.
     */
    protected function input() {
        // если передан id партнера и id партнера целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->partnerBackendModel->movePartnerDown($this->params['id']);
        }
        $this->redirect($this->partnerBackendModel->getURL('backend/partner/index'));
    }
}