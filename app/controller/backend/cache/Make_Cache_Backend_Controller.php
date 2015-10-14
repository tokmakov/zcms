<?php
/**
 * Класс Make_Cache_Backend_Controller отвечает за формирование кэша,
 * административная часть сайта
 */
class Make_Cache_Backend_Controller extends Cache_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }


    /**
     * Функция формирует кэш и делает редирект на страницу управления кэшем
     */
    protected function input() {
        // формируем кэш
        /*
         * ..........
         */
        // редирект на страницу управления кэшем
        $this->redirect($this->cacheBackendModel->getURL('backend/cache/index'));
    }

}