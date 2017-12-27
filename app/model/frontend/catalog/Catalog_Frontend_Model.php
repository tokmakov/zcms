<?php
/**
 * Абстрактный класс Catalog_Frontend_Model, родительский для всех моделей,
 * работающих с каталогом товаров, общедоступная часть сайта
 */
abstract class Catalog_Frontend_Model extends Frontend_Model {

    /*
     * protected function getAllChildIds(...)
     * protected function allChildIds(...)
     * protected function childIds(...)
     * protected function getProductsByParam(...)
     * public function getCheckFilters(...)
     * protected function checkFilters(...)
     * protected function getIsOnlyCategoryGroup(...)
     * protected function isOnlyCategoryGroup(...)
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
     * Функция возвращает массив идентификаторов всех потомков категории $id, т.е.
     * дочерние, дочерние дочерних и так далее; результат работы кэшируется
     */
    protected function getAllChildIds($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запросов к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allChildIds($id);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будут выполнены запросы к базе данных
         */
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
     * Функция возвращает массив идентификаторов всех потомков категории $id, т.е.
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
     * Функция возвращает массив идентификаторов дочерних категорий (прямых потомков)
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
        $result = $this->database->fetchAll(
            $query,
            array('id' => $id),
            $this->enableDataCache
        );
        $ids = array();
        foreach ($result as $item) {
            $ids[] = $item['id'];
        }
        return $ids;

    }

    /**
     * Вспомогательная функция, возвращает массив идентификаторов товаров,
     * которые входят в функциональную группу $group и  подходят под параметры
     * подбора $param; результат работы кэшируется
     */
    protected function getProductsByFilter($group, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->productsByFilter($group, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-group-' . $group . '-filter-' . md5(serialize($filter));
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
    protected function productsByFilter($group, $filter) {

        if (empty($group)) {
            return array();
        }

        if (empty($filter)) {
            return array();
        }

        /*
         * Для каждого элемента фильтра (параметра подбора) выполняем SQL-запрос, чтобы
         * получить массив идентификаторов товаров, подходящих под этот фильтр. Потом
         * с помощью функции array_intersect() получаем пересечение этих массивов, чтобы
         * получить массив идентификаторов товаров, подходящих под все фильтры.
         */
        $ids = array();
        foreach ($filter as $key => $value) {
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
        // получаем массив идентификаторов товаров, подходящих под все фильтры (параметры подбора)
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
    public function getCheckFilters($filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->checkFilters($filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-filter-' . md5(serialize($filter));
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
    protected function checkFilters($filter) {

        if (empty($filter)) {
            return true;
        }

        /*
         * Проверка заключается в том, что проверяется факт существования параметров
         * подбора и значений этих параметров в таблицах БД `params` и `values`
         */

        $count = count($filter);

        $filters = implode(',', array_keys($filter));
        $query = "SELECT COUNT(*) FROM `params` WHERE `id` IN (" . $filters . ")";
        $count1 = $this->database->fetchOne($query);

        $values = implode(',', $filter);
        $query = "SELECT COUNT(*) FROM `values` WHERE `id` IN (" . $values . ")";
        $count2 = $this->database->fetchOne($query);

        return ($count == $count1) && ($count == $count2);

    }

    /**
     * Если категория с уникальным идентификатором $id (и ее потомки) содержит товары
     * одной функциональной группы, функция возвращает идентификатор этой группы, в
     * противном случае возвращает false. Это позволяет сразу включить фильтр по
     * функционалу, чтобы стали доступны параметры подбора. Результат работы кэшируется
     */
    protected function getIsOnlyCategoryGroup($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->isOnlyCategoryGroup($id);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
     * Если категория с уникальным идентификатором $id (и ее потомки) содержит товары
     * одной функциональной группы, функция возвращает идентификатор этой группы, в
     * противном случае возвращает false. Это позволяет сразу включить фильтр по
     * функционалу, чтобы стали доступны параметры подбора без необходимости выбирать
     * из выпадающего списка единственную функциональную группу
     */
    protected function isOnlyCategoryGroup($id) {

        // получаем список всех функциональных групп категории $id и ее потомков
        $childs = $this->getAllChildIds($id);
        // добавляем в массив идентификатор этой категории
        $childs[] = $id;
        // преобразуем массив в строку 123,456,789 — чтобы написать
        // условие SQL-запроса WHERE `id` IN (123,456,789)
        $childs = implode(',', $childs);

        $query = "SELECT
                      DISTINCT `a`.`id` AS `id`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                      AND `b`.`visible` = 1";
        $result = $this->database->fetchAll($query);
        if (count($result) == 1) {
            return $result[0]['id'];
        }
        return false;

    }

    /**
     * Функция возвращает путь от корня каталога до категории с уникальным
     * идентификатором $id; результат работы кэшируется
     */
    public function getCategoryPath($id, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryPath($id, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
     * Функция возвращает путь от корня каталога до категории с уникальным
     * идентификатором $id
     */
    protected function categoryPath($id, $sort, $perpage) {

        $path = array();
        $current = $id;
        while ($current) {
            $query = "SELECT `parent`, `name` FROM `categories` WHERE `id` = :current";
            $res = $this->database->fetch($query, array('current' => $current), $this->enableDataCache);
            $url = 'frontend/catalog/category/id/' . $current;
            /*
             * сразу включаем фильтр по функционалу, если у текущей категории все товары
             * принадлежат одной функциональной группе, чтобы при переходе в эту категорию
             * стали доступны параметры подбора (без необходимости выбора единственной
             * функциональной группы из выпадающего списка)
             */
            $only = $this->getIsOnlyCategoryGroup($current);
            if ($only) {
                $url = $url . '/group/' . $only;
            }
            if ($sort) { // выбрана сортировка?
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) { // выбрано кол-во товаров на страницу?
                $url = $url . '/perpage/' . $perpage;
            }
            $path[] = array(
                'url' => $this->getURL($url),
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

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allCategoryParents($id);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
        $search = $this->stringToLower($search);
        return $search;
    }

    /**
     * Вспомогательная функция, преобразует строку в нижний регистр
     */
    protected function stringToLower($string) {
        $upper = array(
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т',
            'У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','A','B','C','D','E','F','G',
            'H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
        );
        $lower = array(
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т',
            'у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','a','b','c','d','e','f','g',
            'h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
        );
        return str_replace($upper, $lower, $string);
    }

}
