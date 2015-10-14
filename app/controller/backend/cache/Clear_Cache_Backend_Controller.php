<?php
/**
 * Класс Clear_Cache_Backend_Controller отвечает за удаление кэша,
 * административная часть сайта
 */
class Clear_Cache_Backend_Controller extends Cache_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }


    /**
     * Функция удаляет кэш и делает редирект на страницу управления кэшем
     */
    protected function input() {
        // удаляем кэш
        $this->cache->clearCache();
        // редирект на страницу управления кэшем
        $this->redirect($this->cacheBackendModel->getURL('backend/cache/index'));
    }

}