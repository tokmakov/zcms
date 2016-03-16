<?php
/**
 * Класс Index_Catalog_Frontend_Model для работы с главной страницей каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Index_Catalog_Frontend_Model extends Catalog_Frontend_Model {
    
    /*
     * public function getRootCategories()
     * protected function rootCategories()
     */

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает корневые категории; результат работы кэшируется
     */
    public function getRootCategories() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->rootCategories();
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
     * Функция возвращает корневые категории
     */
    protected function rootCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `categories`
                  WHERE
                      `parent` = 0
                  ORDER BY
                      `sortorder`";
        $root = $this->database->fetchAll($query);
        // добавляем в массив информацию об URL категорий
        foreach($root as $key => $value) {
            $root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
        }
        return $root;
    }

}
