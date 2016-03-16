<?php
/**
 * Класс Menu_Catalog_Frontend_Model для работы с навигационным меню каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Menu_Catalog_Frontend_Model extends Catalog_Frontend_Model {
    
    /*
     * public function getCatalogMenu(...)
     * protected function catalogMenu(...)
     * public function getCategoryChilds(...)
     * protected function categoryChilds(...)
     */

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает массив категорий каталога для построения навигационной
     * панели (дерево каталога + путь до текущей категории); результат работы
     * кэшируется
     */
    public function getCatalogMenu($id = 0) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->catalogMenu($id);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив категорий каталога для построения навигационной
     * панели (дерево каталога + путь до текущей категории)
     */
    protected function catalogMenu($id = 0) {

        $parents = $this->getAllCategoryParents($id);
        $ids = implode(',', $parents);
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`parent` AS `parent`, `a`.`name` AS `name`,
                      (SELECT COUNT(*) FROM `categories` `b` WHERE `a`.`id` = `b`.`parent`) AS `count`
                  FROM
                      `categories` `a`
                  WHERE
                      `a`.`parent` IN (SELECT `b`.`id` FROM `categories` `b` WHERE `b`.`parent` = 0)
                      OR `a`.`parent` IN (" . $ids . ")
                  ORDER BY
                      `a`.`sortorder`";
        $categories = $this->database->fetchAll($query);

        // добавляем в массив информацию об URL категорий
        foreach ($categories as $key => $value) {
            $categories[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
            if (in_array($value['id'], $parents)) {
                $categories[$key]['opened'] = true;
            }
            if ($value['id'] == $id) {
                $categories[$key]['current'] = true;
            }
        }

        // строим дерево
        $tree = $this->makeTree($categories);
        return $tree;

    }
    
    /**
     * Функция возвращает дочерние категории категории с уникальным идентификатором $id,
     * результат работы кашируется
     */
    public function getCategoryChilds($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryChilds($id);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }
    
    /**
     * Функция возвращает дочерние категории категории с уникальным идентификатором $id
     */
    public function categoryChilds($id) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`,
                      (SELECT COUNT(*) FROM `categories` `b` WHERE `a`.`id` = `b`.`parent`) AS `count`
                  FROM
                      `categories` `a`
                  WHERE
                      `a`.`parent` = :parent
                  ORDER BY
                      `a`.`sortorder`";
        $childs = $this->database->fetchAll($query, array('parent' => $id));
        // добавляем в массив информацию об URL категорий
        foreach($childs as $key => $value) {
            $childs[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
        }
        return $childs;
    }

}
