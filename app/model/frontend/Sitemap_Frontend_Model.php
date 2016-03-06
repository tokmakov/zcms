<?php
/**
 * Класс Sitemap_Frontend_Model для формирования карты сайта, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Sitemap_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает массив корневых категорий и их детей в виде дерева;
     * результат работы кэшируется
     */
    public function getRootAndChilds() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->rootAndChilds();
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
     * Функция возвращает массив корневых категорий и их детей в виде дерева
     */
    protected function rootAndChilds() {
        // получаем корневые категории и их детей
        $query = "SELECT
                      `id`, `name`, `parent`
                  FROM
                      `categories`
                  WHERE
                      `parent` = 0 OR `parent` IN
                      (SELECT `id` FROM `categories` WHERE `parent` = 0)
                  ORDER BY
                      `sortorder`";
        $root = $this->database->fetchAll($query, array());
        // добавляем в массив информацию об URL категорий
        foreach($root as $key => $value) {
            $root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
        }
        // строим дерево
        $tree = $this->makeTree($root);
        return $tree;
    }

}
