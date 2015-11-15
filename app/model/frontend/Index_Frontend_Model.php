<?php
/**
 * Класс Page_Frontend_Model для показа гланой страницы,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Index_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    public function getIndexPage() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->indexPage();
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()';
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    protected function indexPage() {
        $query = "SELECT
                      `name`, `title`, `description`, `keywords`, `body`
                  FROM
                      `start`
                  WHERE
                      `id` = 1";
        return $this->database->fetch($query);
    }

    /**
     * Возвращает массив всех баннеров для главной (стартовой) страницы сата
     */
    public function getAllBanners() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allBanners();
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()';
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив всех баннеров для главной (стартовой) страницы сата
     */
    protected function allBanners() {
        $query = "SELECT
                      `id`, `name`, `url`, `alttext`
                  FROM
                      `slider`
                  WHERE
                      `visible` = 1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

}
