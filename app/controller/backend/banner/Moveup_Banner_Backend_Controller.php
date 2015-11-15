<?php
/**
 * Класс Moveup_Banner_Backend_Controller поднимает баннер в списке вверх,
 * взаимодействует с моделью Banner_Backend_Model, административная часть сайта
 */
class Moveup_Banner_Backend_Controller extends Banner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только поднять баннер в списке вверх и сделать редирект.
     */
    protected function input() {
        // если передан id баннера и id баннера целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->bannerBackendModel->moveBannerUp($this->params['id']);
        }
        $this->redirect($this->bannerBackendModel->getURL('backend/banner/index'));
    }
}