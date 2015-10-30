<?php
/**
 * Класс Bannerdown_Start_Backend_Controller опускает баннер в списке вниз,
 * взаимодействует с моделью Start_Backend_Model, административная часть сайта
 */
class Bannerdown_Start_Backend_Controller extends Start_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только опустить баннер в списке вниз и сделать редирект.
     */
    protected function input() {
        // если передан id баннера и id баннера целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->startBackendModel->moveBannerDown($this->params['id']);
        }
        $this->redirect($this->startBackendModel->getURL('backend/start/index'));
    }
}