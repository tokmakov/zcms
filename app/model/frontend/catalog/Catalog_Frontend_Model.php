<?php
/**
 * Абстрактный класс Catalog_Frontend_Model, родительский для всех моделей, работающих
 * с каталогом товаров, общедоступная часть сайта
 */
abstract class Catalog_Frontend_Model extends Frontend_Model {

    /*
     * protected function getAllChildIds(...)
     * protected function allChildIds(...)
     * protected function childIds(...)
     * protected function getProductsByParam(...)
     * public function getCheckParams(...)
     * protected function checkParams(...)
     * public function getCategoryPath(...)
     * protected function categoryPath(...)
     * protected function getAllCategoryParents(...)
     * protected function allCategoryParents(...)
     * protected function cleanSearchString(...)
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив идентификаторов всех потомков категории $id, т.е.
     * дочерние, дочерние дочерних и так далее; результат работы кэшируется
     */
    protected function getAllChildIds($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allChildIds($id);
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
     * Возвращает массив идентификаторов всех потомков категории $id, т.е.
     * дочерние, дочерние дочерних и так далее
     */
    protected function allChildIds($id) {
        $childs = array();
        $ids = $this->childIds($id);
        foreach ($ids as $item) {
            $childs[] = $item;
            $c = $this->allChildIds($item);
            foreach ($c as $v) {
                $childs[] = $v;
            }
        }
        return $childs;
    }


    /**
     * Возвращает массив идентификаторов дочерних категорий (прямых потомков)
     * категории с уникальным идентификатором $id
     */
    protected function childIds($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  ORDER BY
                      `sortorder`";
        $res = $this->database->fetchAll($query, array('id' => $id), $this->enableDataCache);
        $ids = array();
        foreach ($res as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

    /**
     * Вспомогательная функция, возвращает массив идентификаторов товаров,
     * которые входят в функциональную группу $group и  подходят под параметры
     * подбора $param; результат работы кэшируется
     */
    protected function getProductsByParam($group, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->productsByParam($group, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-group-' . $group . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Вспомогательная функция, возвращает массив идентификаторов товаров,
     * которые входят в функциональную группу $group и  подходят под параметры
     * подбора $param
     */
    protected function productsByParam($group, $param) {

        if (empty($group)) {
            return array();
        }

        if (empty($param)) {
            return array();
        }

        $ids = array();
        foreach ($param as $key => $value) {
            $query = "SELECT
                          `a`.`id` AS `id`
                      FROM
                          `products` `a` INNER JOIN `product_param_value` `b`
                          ON `a`.`id` = `b`.`product_id`
                      WHERE
                          `a`.`group` = :group AND
                          `b`.`param_id` = :param_id AND
                          `b`.`value_id` = :value_id";
            $result = $this->database->fetchAll(
                $query,
                array(
                    'group'    => $group,
                    'param_id' => $key,
                    'value_id' => $value
                ),
                $this->enableDataCache
            );
            $res = array();
            foreach($result as $item) {
                $res[] = $item['id'];
            }
            $ids[] = $res;
        }
        $count = count($ids);
        if (0 == $count) {
            return array();
        }
        $products = $ids[0];
        for ($i = 1; $i < $count; $i++) {
            $products = array_intersect($products, $ids[$i]);
        }
        if (0 == count($products)) {
            return array();
        }

        return $products;

    }

    /**
     * Функция проверяет корректность идентификаторов параметров и значений;
     * если параметры и значения коррекные, возвращает true, иначе false;
     * результат работы кэшируется
     */
    public function getCheckParams($param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->checkParams($param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция проверяет корректность идентификаторов параметров и значений;
     * если параметры и значения коррекные, возвращает true, иначе false
     */
    protected function checkParams($param) {

        if (empty($param)) {
            return true;
        }

        $count = count($param);

        $params = implode(',', array_keys($param));
        $query = "SELECT COUNT(*) FROM `params` WHERE `id` IN (" . $params . ")";
        $count1 = $this->database->fetchOne($query);

        $values = implode(',', $param);
        $query = "SELECT COUNT(*) FROM `values` WHERE `id` IN (" . $values . ")";
        $count2 = $this->database->fetchOne($query);

        return ($count == $count1) && ($count == $count2);

    }

    /**
     * Функция возвращает путь от корня каталога до категории с уникальным
     * идентификатором $id; результат работы кэшируется
     */
    public function getCategoryPath($id) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryPath($id);
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
     * Функция возвращает путь от корня каталога до категории с уникальным
     * идентификатором $id
     */
    protected function categoryPath($id) {

        $path = array();
        $current = $id;
        while ($current) {
            $query = "SELECT `parent`, `name` FROM `categories` WHERE `id` = :current";
            $res = $this->database->fetch($query, array('current' => $current), $this->enableDataCache);
            $path[] = array(
                'url' => $this->getURL('frontend/catalog/category/id/' . $current),
                'name' => $res['name']
            );
            $current = $res['parent'];
        }
        $path[] = array('name' => 'Каталог', 'url' => $this->getURL('frontend/catalog/index'));
        $path[] = array('name' => 'Главная', 'url' => $this->getURL('frontend/index/index'));
        $path = array_reverse($path);
        return $path;

    }

    /**
     * Функция возвращает массив всех родителей категории с уникальным
     * идентификатром $id; результат работы кэшируется
     */
    protected function getAllCategoryParents($id = 0) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allCategoryParents($id);
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
     * Функция возвращает массив всех родителей категории с уникальным
     * идентификатром $id
     */
    protected function allCategoryParents($id = 0) {

        if (0 == $id) {
            return array(0 => 0);
        }
        $path = array();
        $path[] = $id;
        $current = $id;
        while ($current) {
            $query = "SELECT
                          `parent`
                      FROM
                          `categories`
                      WHERE
                          `id` = :current";
            $res = $this->database->fetchOne($query, array('current' => $current), $this->enableDataCache);
            $path[] = $res;
            $current = $res;
        }
        $path = array_reverse($path);
        return $path;

    }

    /**
     * Вспмогательная функция, очищает строку поискового запроса с сайта
     * от всякого мусора
     */
    protected function cleanSearchString($search) {
        $search = iconv_substr($search, 0, 64);
        // удаляем все, кроме букв и цифр
        $search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
        // сжимаем двойные пробелы
        $search = preg_replace('#\s+#u', ' ', $search);
        $search = trim($search);
        $search = utf8_strtolower($search);
        return $search;
    }

}
