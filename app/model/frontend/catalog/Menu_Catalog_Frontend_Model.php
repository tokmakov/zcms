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
    public function getCatalogMenu($id, $sort, $perpage) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->catalogMenu($id, $sort, $perpage);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-sort-' . $sort . '-perpage-' . $perpage;
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
    protected function catalogMenu($id, $sort, $perpage) {

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
            $url = 'frontend/catalog/category/id/' . $value['id'];
            // сразу включаем фильтр по функционалу, если у текущей категории все товары
            // принадлежат одной функциональной группе, чтобы при переходе в эту категорию
            // стали доступны параметры подбора
            $filter = $this->getIsOnlyCategoryGroup($value['id']);
            if ($filter) {
                $url = $url . '/group/' . $filter;
            }
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) {
                $url = $url . '/perpage/' . $perpage;
            }
            $categories[$key]['url'] = $this->getURL($url);
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
    public function getCategoryChilds($id, $sort, $perpage) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryChilds($id, $sort, $perpage);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-sort-' . $sort . '-perpage-' . $perpage;
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
    protected function categoryChilds($id, $sort, $perpage) {
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
            $url = 'frontend/catalog/category/id/' . $value['id'];
            // сразу включаем фильтр по функционалу, если у текущей категории все товары
            // принадлежат одной функциональной группе, чтобы при переходе в эту категорию
            // стали доступны параметры подбора
            $filter = $this->getIsOnlyCategoryGroup($value['id']);
            if ($filter) {
                $url = $url . '/group/' . $filter;
            }
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) {
                $url = $url . '/perpage/' . $perpage;
            }
            $childs[$key]['url'] = $this->getURL($url);
        }
        return $childs;
    }

}
