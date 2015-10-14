<?php
/**
 * Класс Index_Cache_Backend_Controller формирует страницу управления кэшем
 * (создание и удаление кэша), административная часть сайта
 */
class Index_Cache_Backend_Controller extends Cache_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * управления кэшем
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Cache_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Index_Cache_Backend_Controller
         */
        parent::input();

        $this->title = 'Управление кэшем. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->cacheBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // URL ссылки «Очистить кэш»
            'clearCacheUrl' => $this->cacheBackendModel->getURL('backend/cache/clear'),
            // URL ссылки «Создать кэш»
            'makeCacheUrl'  => $this->cacheBackendModel->getURL('backend/cache/make'),
        );

    }

}