<?php
/**
 * Класс Banner_Frontend_Model для работы с баннерами в правой колонке;
 * общедоступная часть сайта
 */
class Banner_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив баннеров для показа в правой колонке;
     * результат работы кэшируется
     */
    public function getBanners() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->banners();
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает массив баннеров для показа в правой колонке
     */
    protected function banners() {
        $query = "SELECT
                      `id`, `url`, `alttext`
                  FROM
                      `banners`
                  WHERE
                      `visible` = 1
                  ORDER BY
                        `sortorder`";
        $banners = $this->database->fetchAll($query);
        foreach ($banners as $key => $value) {
            $banners[$key]['image'] = $this->config->site->url . 'files/banner/' . $value['id'] . '.jpg';
        }
        return $banners;
    }

}
