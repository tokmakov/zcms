<?php
/**
 * Класс Catalog_Frontend_Model для работы с каталогом товаров, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Catalog_Frontend_Model extends Frontend_Model {

    private $userFrontendModel;

    public function __construct() {
        parent::__construct();
        $this->userFrontendModel =
            isset($this->register->userFrontendModel) ? $this->register->userFrontendModel : new User_Frontend_Model();
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
        $query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = 0 ORDER BY `sortorder`";
        $root = $this->database->fetchAll($query, array());
        // добавляем в массив информацию об URL категорий
        foreach($root as $key => $value) {
            $root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
        }
        return $root;
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
        $query = "SELECT `id`, `name`, `parent`
                  FROM `categories`
                  WHERE `parent` = 0 OR `parent` IN (SELECT `id` FROM `categories` WHERE `parent` = 0)
                  ORDER BY `sortorder`";
        $root = $this->database->fetchAll($query, array());
        // добавляем в массив информацию об URL категорий
        foreach($root as $key => $value) {
            $root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
            if (isset($value['childs'])) {
                foreach($value['childs'] as $k => $v) {
                    $root[$key]['childs'][$k]['url'] = $this->getURL('frontend/catalog/category/id/' . $v['id']);
                }
            }
        }
        // строим дерево
        $tree = $this->makeTree($root);
        return $tree;
    }

    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getProduct($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->product($id);
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
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    protected function product($id) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
                      `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`, `a`.`purpose` AS `purpose`, `a`.`techdata` AS `techdata`,
                      `a`.`features` AS `features`, `a`.`complect` AS `complect`, `a`.`equipment` AS `equipment`,
                      `a`.`category2` AS `second`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`id` = :id AND `a`.`visible` = 1";
        $product = $this->database->fetch($query, array('id' => $id));
        if (false === $product) {
            return false;
        }
        // добавляем информацию о файлах документации
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`title` AS `title`,
                      `a`.`filename` AS `file`, `a`.`filetype` AS `type`
                  FROM
                      `docs` `a` INNER JOIN `doc_prd` `b`
                      ON `a`.`id`=`b`.`doc_id`
                  WHERE
                      `b`.`prd_id` = :id
                  ORDER BY
                      `a`.`title`";
        $docs = $this->database->fetchAll($query, array('id' => $id));
        $product['docs'] = $docs;
        return $product;
    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `description`, `keywords`, `parent`
                  FROM
                      `categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id), $this->enableDataCache);
    }

    /**
     * Возвращает массив дочерних категорий категории с уникальным идентификатором
     * $id с количеством товаров в каждой из них (и во всех дочерних); результат
     * работы кэшируется
     */
    public function getChildCategories($id, $group = 0, $maker = 0, $param, $sort = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->childCategories($id, $group, $maker, $param, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker . '-sort-' . $sort;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив дочерних категорий категории с уникальным идентификатором
     * $id с количеством товаров в каждой из них (и во всех дочерних)
     */
    protected function childCategories($id, $group = 0, $maker = 0, $param = array(), $sort = 0) {

        // получаем список дочерних категорий
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  ORDER BY
                      `sortorder`";
        $childCategories = $this->database->fetchAll($query, array('id' => $id));

        // для каждой дочерней категории получаем количество товаров в ней и в ее
        // потомках с учетом фильтров по функциональной группе, производителю, по
        // параметрам подбора
        foreach ($childCategories as $key => $value) {
            $childs = $this->getAllChildIds($value['id']);
            $childs[] = $value['id'];
            $childs = implode(',', $childs);
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `products` `a`
                          INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                          INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      WHERE
                          (`category` IN (" . $childs . ") OR `category2` IN (" . $childs . ")) AND `a`.`visible` = 1";
            if ($group) { // фильтр по функциональной группе
                $query = $query . " AND `a`.`group` = " . $group;
            }
            if ($maker) { // фильтр по производителю
                $query = $query . " AND `a`.`maker` = " . $maker;
            }
            if ( ! empty($param)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($param);
                if ( ! empty($ids)) {
                    $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
                    $childCategories[$key]['count'] = $this->database->fetchOne($query);
                } else {
                    $childCategories[$key]['count'] = 0;
                }
            } else {
                $childCategories[$key]['count'] = $this->database->fetchOne($query);
            }

            // добавляем в массив информацию об URL дочерних категорий
            $url = 'frontend/catalog/category/id/' . $value['id'];
            if ($group) {
                $url = $url . '/group/' . $group;
            }
            if ($maker) {
                $url = $url . '/maker/' . $maker;
            }
            if ( ! empty($param)) {
                $temp = array();
                foreach ($param as $k => $v) {
                    $temp[] = $k . '.' . $v;
                }
                $url = $url . '/param/' . implode('-', $temp);
            }
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            $childCategories[$key]['url'] = $this->getURL($url);
        }

        return $childCategories;

    }

    /**
     * Возвращает массив идентификаторов всех потомков категории $id, т.е.
     * дочерние, дочерние дочерних и так далее; результат работы кэшируется
     */
    // TODO: public или protected или private?
    public function getAllChildIds($id) {
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
     * Возвращает массив идентификаторов всех потомков категории $id,
     * т.е. дочерние, дочерние дочерних и т.п.
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
    private function childIds($id) {
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
     * Возвращает массив товаров категории $id и ее потомков, т.е. не только товары
     * этой категории, но и товары дочерних категорий, товары дочерних-дочерних
     * категорий и так далее; результат работы кэшируется
     */
    public function getCategoryProducts($id, $group = 0, $maker = 0, $param = array(), $sort = 0, $start = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryProducts($id, $group, $maker, $param, $sort, $start);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-sort-' . $sort . '-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив товаров категории $id и ее потомков, т.е. не только товары
     * этой категории, но и товары дочерних категорий, товары дочерних-дочерних
     * категорий и так далее
     */
    protected function categoryProducts($id, $group = 0, $maker = 0, $param = array(), $sort = 0, $start = 0){

        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);

        $tmp = '';
        if ($group) { // фильтр по функциональной группе
            $tmp = $tmp . " AND `a`.`group` = " . $group;
        }
        if ($maker) { // фильтр по производителю
            $tmp = $tmp . " AND `a`.`maker` = " . $maker;
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($param);
            if (empty($ids)) {
                return array();
            }
            $tmp = $tmp . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }

        switch ($sort) { // сортировка
            case 0: $order = '`b`.`globalsort`, `a`.`sortorder`';  break; // сортировка по умолчанию
            case 1: $order = '`a`.`price`';      break;                   // сортировка по цене, по возрастанию
            case 2: $order = '`a`.`price` DESC'; break;                   // сортировка по цене, по убыванию
            case 3: $order = '`a`.`name`';       break;                   // сортировка по наименованию, по возрастанию
            case 4: $order = '`a`.`name` DESC';  break;                   // сортировка по наименованию, по убыванию
            case 5: $order = '`a`.`code`';       break;                   // сортировка по коду, по возрастанию
            case 6: $order = '`a`.`code` DESC';  break;                   // сортировка по коду, по убыванию
        }

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
                      `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      (`a`.`category` IN (" . $childs . ") OR `a`.`category2` IN (" . $childs . "))" . $tmp . " AND `a`.`visible` = 1
                  ORDER BY " . $order . "
                  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query, array());

        // добавляем в массив товаров информацию об URL товаров, производителей, фото
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL ссылки на фото товара
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // атрибут action тега form для добавления товара в корзину
            $products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
            // атрибут action тега form для добавления товара в список отложенных
            $products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
            // атрибут action тега form для добавления товара в список сравнения
            $products[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
        }

        return $products;
    }

    /**
     * Возвращает количество товаров в категории $id и в ее потомках, т.е.
     * суммарное кол-во товаров не только в категории $id, но и в дочерних
     * категориях и в дочерних-дочерних категориях и так далее; результат
     * работы кэшируется
     */
    public function getCountCategoryProducts($id, $group = 0, $maker = 0, $param = array()) {
        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      (`a`.`category` IN (" . $childs . ") OR `a`.`category2` IN (" . $childs . "))
                      AND `a`.`visible` = 1";
        if ($group) { // фильтр по функциональной группе
            $query = $query . " AND `a`.`group` = " . $group;
        }
        if ($maker) { // фильтр по производителю
            $query = $query . " AND `a`.`maker` = " . $maker;
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Возвращает массив производителей товаров в категории $id и в ее потомках,
     * т.е. не только производителей товаров этой категории, но и производителей
     * товаров в дочерних категориях, производителей товаров в дочерних-дочерних
     * категориях и так далее; результат работы кэшируется
     */
    public function getCategoryMakers($id, $group = 0, $param = array()) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryMakers($id, $group, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив производителей товаров в категории $id и в ее потомках,
     * т.е. не только производителей товаров этой категории, но и производителей
     * товаров в дочерних категориях, производителей товаров в дочерних-дочерних
     * категориях и так далее
     */
    protected function categoryMakers($id, $group = 0, $param = array()) {

        // получаем список всех произвоителей этой категории и ее потомков
        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);
        $query = "SELECT
                      `a`.`id` AS `id`, `a`. `name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                  WHERE
                      (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                      AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`. `name`
                  ORDER BY
                      `a`.`name`";

        $makers = $this->database->fetchAll($query);

        if (0 == $group) {
            return $makers;
        }

        // теперь подсчитываем количество товаров для каждого производителя
        // с учетом фильтров по функциональной группе и по параметрам
        foreach ($makers as $key => $value) {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `makers` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      WHERE
                          (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                          AND `a`.`id` = " . $value['id'] . "
                          AND `b`.`visible` = 1";
            if ($group) {
                $query = $query . " AND `b`.`group` = " . $group;
            }
            if ( ! empty($param)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($param);
                if ( ! empty($ids)) {
                    $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                    $makers[$key]['count'] = $this->database->fetchOne($query);
                } else {
                    $makers[$key]['count'] = 0;
                }
            } else {
                $makers[$key]['count'] = $this->database->fetchOne($query);
            }
        }

        return $makers;

    }

    /**
     * Возвращает массив функциональных групп товаров в категории $id и в ее потомках,
     * т.е. не только функциональные группы товаров этой категории, но и функциональные
     * группы товаров в дочерних категориях, функциональные группы товаров в
     * дочерних-дочерних категориях и т.д.
     */
    public function getCategoryGroups($id, $maker = 0) {

        // получаем список всех функциональных групп этой категории и ее потомков
        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);

        $query = "SELECT
                      `a`.`id` AS `id`, `a`. `name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . ")) AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`. `name`
                  ORDER BY
                      `a`.`name`";
        $groups = $this->database->fetchAll($query);

        if (0 == $maker) {
            return $groups;
        }

        // теперь подсчитываем количество товаров для каждой группы
        // с учетом фильтра по производителю
        foreach ($groups as $key => $value)  {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `groups` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                          INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      WHERE
                          (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                          AND `a`.`id` = " . $value['id'] . "
                          AND `b`.`maker` = " . $maker . "
                          AND `b`.`visible` = 1";
            $groups[$key]['count'] = $this->database->fetchOne($query);
        }

        return $groups;
    }

    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранной категории и всех ее потомков
     */
    public function getGroupParams($category, $group = 0, $maker = 0, $param = array()) {

        if (0 == $group) {
            return array();
        }

        // получаем список всех параметров подбора для выбранной функциональной
        // группы и выбранной категории и всех ее потомков
        $childs = $this->getAllChildIds($category);
        $childs[] = $category;
        $childs = implode(',', $childs);

        $query = "SELECT
                      `f`.`id` AS `param_id`, `f`.`name` AS `param_name`,
                      `g`.`id` AS `value_id`, `g`.`name` AS `value_name`,
                      COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `product_param_value` `e` ON `b`.`id` = `e`.`product_id`
                      INNER JOIN `params` `f` ON `e`.`param_id` = `f`.`id`
                      INNER JOIN `values` `g` ON `e`.`value_id` = `g`.`id`
                  WHERE
                      (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                      AND `a`.`id` = " . $group . "
                      AND `b`.`visible` = 1
                  GROUP BY
                      1, 2, 3, 4";
        $result = $this->database->fetchAll($query);

        // теперь подсчитываем количество товаров для каждого параметра и каждого
        // значения параметра с учетом фильтров по производителю и по параметрам
        foreach ($result as $key => $value)  {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `groups` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                          INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                          INNER JOIN `product_param_value` `e` ON `b`.`id` = `e`.`product_id`
                          INNER JOIN `params` `f` ON `e`.`param_id` = `f`.`id`
                          INNER JOIN `values` `g` ON `e`.`value_id` = `g`.`id`
                      WHERE
                          (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                          AND `a`.`id` = " . $group . "
                          AND `e`.`param_id` = " . $value['param_id'] . " AND `e`.`value_id` = " . $value['value_id'] . "
                          AND `b`.`visible` = 1";
            if ($maker) { // фильтр по производителю
                $query = $query . " AND `b`.`maker` = " . $maker;
            }

            $temp = $param;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($temp);
                if ( ! empty($ids)) {
                    $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                    $result[$key]['count'] = $this->database->fetchOne($query);
                } else {
                    $result[$key]['count'] = 0;
                }
            } else {
                $result[$key]['count'] = $this->database->fetchOne($query);
            }
        }

        $params = array();
        $param_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $params[$counter] = array('id' => $value['param_id'], 'name' => $value['param_name']);
            }
            $params[$counter]['values'][] = array('id' => $value['value_id'], 'name' => $value['value_name'], 'count' => $value['count']);
        }

        return $params;

    }

    /**
     * Вспомогательная функция, возвращает массив идентификаторов товаров,
     * которые подходят под параметры подбора
     */
    private function getProductsByParam($param = array()) {
        if (empty($param)) {
            return array();
        }

        $ids = array();
        foreach ($param as $key => $value) {
            $query = "SELECT `product_id` FROM `product_param_value` WHERE `param_id` = :param_id AND `value_id` = :value_id";
            $res = $this->database->fetchAll($query, array('param_id' => $key, 'value_id' => $value), $this->enableDataCache);
            $r = array();
            foreach($res as $item) {
                $r[] = $item['product_id'];
            }
            $ids[] = $r;
        }
        $count = count($ids);
        if ($count == 0) {
            return array();
        }
        $result = $ids[0];
        for ($i = 1; $i < $count; $i++) {
            $result = array_intersect($result, $ids[$i]);
        }
        if (count($result) == 0) {
            return array();
        }

        return $result;
    }

    /**
     * Функция проверяет корректность идентификаторов значений параметров подбора
     */
    public function isValidParams($ids) {
        if (empty($ids)) {
            return false;
        }
        if (is_array($ids)) {
            $count = count($ids);
            $temp = implode(',', $ids);
        } else {
            $count = 1;
            $temp = $ids;
        }
        $query = "SELECT COUNT(*) FROM `values` WHERE `id` IN (" . $temp . ")";
        $res = $this->database->fetchOne($query);
        return $count == $res;
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
        $path[] = array('url' => $this->getURL('frontend/catalog/index'), 'name' => 'Каталог');
        $path[] = array('url' => $this->getURL('frontend/index/index'), 'name' => 'Главная');
        $path = array_reverse($path);
        return $path;

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
        $path = $this->getAllCategoryParents($id);
        return $this->catalogBranch($path, 0, $id);
    }

    /**
     * Функция возвращает массив категорий каталога для построения навигационной
     * панели (дерево каталога с одной раскрытой веткой)
     */
    private function catalogBranch($path, $level, $id) {
        // этот код возвращает массив, где уровень вложенности задает переменная $level
        $query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = :parent ORDER BY `sortorder`";
        $items = $this->database->fetchAll($query, array('parent' => $path[$level]), $this->enableDataCache);
        $result = array();
        foreach ($items as $item) {
            $result[] = array(
                'id' => $item['id'],
                'name' => $item['name'],
                'url' => $this->getURL('frontend/catalog/category/id/' . $item['id']),
                'level' => $level
            );
            if ($id == $item['id']) $result[count($result)-1]['current'] = true;
            // получить подкатегории?
            if (($level+1 < count($path)) && ($item['id'] == $path[$level+1])) {
                if ($level == 0) $result[count($result)-1]['opened'] = true;
                // рекурсивный вызов функции catalogBranch()
                $out = $this->catalogBranch($path, $level + 1, $id);
                // добавляем подкатегории в конец массива $result
                foreach ($out as $value) {
                    $result[] = $value;
                }
            }
        }
        return $result;
        /*
        // этот код возвращает массив, где уровень вложенности определается наличием вложенного массива childs
        $query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = :parent ORDER BY `sortorder`";
        $res = $this->database->fetchAll($query, array('parent' => $path[$level]), $this->enableDataCache);
        $result = array();
        foreach ($res as $i => $item) {
            $result[$i] = array('id' => $item['id'], 'name' => $item['name']);
            // получить подкатегории?
            if (($level+1 < count($path)) && ($item['id'] == $path[$level+1])) {
                // рекурсивный вызов функции getCatalogBranch()
                $out = $this->getCatalogBranch($path, $level + 1);
                // добавляем подкатегории текущей категории
                foreach ($out as $value) {
                    $result[$i]['childs'][] = $value;
                }
            }
        }
        return $result;
        */
    }

    /**
     * Функция возвращает массив всех родителей категории с уникальным
     * идентификатром $id; результат работы кэшируется
     */
    private function getAllCategoryParents($id) {
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
    protected function allCategoryParents($id) {
        if ($id == 0) {
            return array(0 => 0);
        }
        $path = array();
        $path[] = $id;
        $current = $id;
        while ($current) {
            $query = "SELECT `parent` FROM `categories` WHERE `id` = :current";
            $res = $this->database->fetchOne($query, array('current' => $current), $this->enableDataCache);
            $path[] = $res;
            $current = $res;
        }
        $path = array_reverse($path);
        return $path;
    }

    /**
     * Функция возвращает массив всех производителей (если $limit=0); результат
     * работы кэшируется
     */
    public function getAllMakers($limit = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allMakers($limit);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-limit-' . $limit;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает массив всех производителей (если $limit=0)
     */
    protected function allMakers($limit = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                  WHERE
                      `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`.`name`
                  ORDER BY
                      `a`.`name`";
        if ($limit) {
            $query = $query . ' LIMIT ' . $limit;
        }
        $makers = $this->database->fetchAll($query, array());
        // добавляем в массив URL ссылок на страницы отдельных производителей
        foreach($makers as $key => $value) {
            $makers[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
        }
        return $makers;
    }

    /**
     * Функция возвращает информацию о производителе с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getMaker($id) {
        $query = "SELECT
                      `id`, `name`, `keywords`, `description`, `body`
                  FROM
                      `makers`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id), $this->enableDataCache);
    }

    /**
     * Функция возвращает массив товаров производителя с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getMakerProducts($id, $sortorder = 0, $start = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerProducts($id, $sortorder, $start);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-sortorder-' . $sortorder . '-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает массив товаров производителя с уникальным идентификатором $id
     */
    protected function makerProducts($id, $sortorder = 0, $start = 0) {
        switch ($sortorder) { // сортировка
            case 0: $temp = '`b`.`globalsort`, `a`.`sortorder`';  break; // сортировка по умолчанию
            case 1: $temp = '`a`.`price`';                        break; // сортировка по цене, по возрастанию
            case 2: $temp = '`a`.`price` DESC';                   break; // сортировка по цене, по убыванию
            case 3: $temp = '`a`.`name`';                         break; // сортировка по наименованию, по возрастанию
            case 4: $temp = '`a`.`name` DESC';                    break; // сортировка по наименованию, по убыванию
            case 5: $temp = '`a`.`code`';                         break; // сортировка по коду, по возрастанию
            case 6: $temp = '`a`.`code` DESC';                    break; // сортировка по коду, по убыванию
        }
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
                      `a`.`image` AS `image`, `a`.`price` AS `price`, `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`, `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1
                  ORDER BY " . $temp . "
                  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query, array('id' => $id));
        // добавляем в массив URL ссылок на товары и фото
        foreach($products as $key => $value) {
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // атрибут action тега form для добавления товара в корзину
            $products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
            // атрибут action тега form для добавления товара в список отложенных
            $products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
            // атрибут action тега form для добавления товара в список сравнения
            $products[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
        }
        return $products;
    }

    /**
     * Функция возвращает кол-во товаров производителя с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getCountMakerProducts($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a` INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);
    }

    /**
     * Функция возвращает единицы измерения товаров в каталоге
     */
    public function getUnits() {
        $units = array(
            0 => 'руб',
            1 => 'руб/шт',
            2 => 'руб/компл',
            3 => 'руб/упак',
            4 => 'руб/метр',
            5 => 'руб/пара',
        );
        return $units;
    }

    /**
     * Функция возвращает результаты поиска по каталогу; результат работы
     * кэшируется
     */
    public function getSearchResults($search = '', $start = 0, $ajax = false) {
        $search = $this->cleanSearchString($search);
        file_put_contents('search.txt', $search . PHP_EOL, FILE_APPEND);

        if ($ajax) {
            while ($this->isSearchLocked()) {
                usleep(100);
            }
            $this->searchLock();
        }

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->searchResults($search, $start, $ajax);
        }

        // уникальный ключ доступа к кэшу
        $a = ($ajax) ? 'true' : 'false';
        $key = __METHOD__ . '()-search-' . md5($search) . '-start-' . $start . '-ajax-' . $a;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        $result = $this->getCachedData($key, $function, $arguments);
        if ($ajax) {
            $this->searchUnlock();
        }
        return $result;
    }

    /**
     * Функция возвращает результаты поиска по каталогу
     */
    protected function searchResults($search = '', $start = 0, $ajax = false) {
        if (empty($search)) {
            return array();
        }
        $query = $this->getSearchQuery($search);
        if (empty($query)) {
            return array();
        }
        $query = $query . ' LIMIT ' . $start . ', ' . $this->config->pager->frontend->products->perpage;
        $result = $this->database->fetchAll($query);
        // добавляем в массив результатов поиска информацию об URL товаров и фото
        foreach($result as $key => $value) {
            if ($ajax) { // для поиска в шапке сайта
                $result[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                unset(
                    $result[$key]['shortdescr'],
                    $result[$key]['image'],
                    $result[$key]['ctg_id'],
                    $result[$key]['ctg_name'],
                    $result[$key]['mkr_id'],
                    $result[$key]['ctg_name'],
                    $result[$key]['relevance']
                );
            } else { // для страницы поиска
                // URL ссылки на страницу товара
                $result[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                // URL ссылки на страницу производителя
                $result[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
                // URL ссылки на фото товара
                if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                    $result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
                } else {
                    $result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
                }
                // атрибут action тега form для добавления товара в корзину
                $result[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
                // атрибут action тега form для добавления товара в список отложенных
                $result[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
                // атрибут action тега form для добавления товара в список сравнения
                $result[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
            }
        }
        return $result;
    }

    /**
     * Функция возвращает количество результатов поиска по каталогу
     */
    public function getCountSearchResults($search = '') {
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return 0;
        }
        $query = $this->getCountSearchQuery($search);
        if (empty($query)) {
            return 0;
        }
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Функция возвращает SQL-запрос для поиска по каталогу
     */
    private function getSearchQuery($search) {
        if (empty($search)) {
            return '';
        }
        if (utf8_strlen($search) < 2) {
            return '';
        }
        // небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
        if (preg_match('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search)) {
            preg_match_all('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search, $temp1);
            $search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
        }
        if (preg_match('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search)) {
            preg_match_all('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search, $temp2);
            $search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );
        }
        $matches = array_merge(isset($temp1[0]) ? $temp1[0] : array(), isset($temp2[0]) ? $temp2[0] : array());

        $words = explode(' ', $search);
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`,
                      `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`,
                      `c`.`name` AS `mkr_name`";

        $query = $query.", IF( LOWER(`a`.`name`) REGEXP '^".$words[0]."', 0.1, 0 ) + IF( LOWER(`a`.`title`) REGEXP '^".$words[0]."', 0.05, 0 )";

        $prd_name = 1.0; // коэффициент веса для `name`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_name."*( IF( `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
        // как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
        }
        // если слова расположены рядом в нужном порядке
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
        }
        // если мы разделяли строку ABC123 на ABC и 123
        if (!empty($matches)) {
            foreach ($matches as $item) {
                $query = $query." + IF( `a`.`name` LIKE '%".$item."%', 0.1, 0 )";
            }
        }
        $query = $query." )";

        $prd_title = 0.8; // коэффициент веса для `title`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_title."*( IF( `a`.`title` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." - IF( `a`.`title` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
        // как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `a`.`title` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." - IF( `a`.`title` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
        }
        // если слова расположены рядом в нужном порядке
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0  )";
        }
        // если мы разделяли строку ABC123 на ABC и 123
        if (!empty($matches)) {
            foreach ($matches as $item) {
                $query = $query." + IF( `a`.`title` LIKE '%".$item."%', 0.1, 0 )";
            }
        }
        $query = $query." )";

        $prd_maker = 0.6; // коэффициент веса для `mkr_name`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_maker."*( IF( `c`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." - IF( (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%') OR (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`title` LIKE '%".$words[0]."%'), ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.1, 0 )";
        $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."'), 0.1, 0 )";
        $query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.1, 0 )";
        $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]'), 0.1, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
        // как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `c`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." - IF( (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%') OR (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`title` LIKE '%".$words[$i]."%'), ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.1, 0 )";
            $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."'), 0.1, 0 )";
            $query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.1, 0 )";
            $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]'), 0.1, 0 )";
        }
        $query = $query." )";

        $prd_code = 1.0; // коэффициент веса для `code`
        $codes = array();
        foreach($words as $word) {
            if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
            if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        if (count($codes) > 0) {
            $query = $query." + " . $prd_code . "*( IF( `a`.`code`='".$codes[0]."', 1.0, 0 )";
            for ($i = 1; $i < count($codes); $i++) {
                $query = $query." + IF( `a`.`code`='".$codes[$i]."', 1.0, 0 )";
            }
            $query = $query." )";
        }

        $query = $query." AS `relevance`";

        $query = $query." FROM
                              `products` `a`
                              INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                              INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                          WHERE (";

        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        if (count($codes) > 0) {
            $query = $query." OR `a`.`code`='".$codes[0]."'";
            for ($i = 1; $i < count( $codes ); $i++) {
              $query = $query." OR `a`.`code`='".$codes[$i]."'";
            }
        }
        $query = $query.") AND `a`.`visible` = 1";
        $query = $query." ORDER BY `relevance` DESC, LENGTH(`a`.`name`), `a`.`name`";

        return $query;
    }

    /**
     * Функция возвращает SQL-запрос для поиска по каталогу
     */
    private function getCountSearchQuery($search) {
        if (empty($search)) {
            return '';
        }
        if (utf8_strlen($search) < 2) {
            return '';
        }
        // небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
        $search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
        $search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );

        $words = explode(' ', $search);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE (";

        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        $codes = array();
        foreach($words as $word) {
            if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
            if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        if (count($codes) > 0) {
            $query = $query." OR `a`.`code`='".$codes[0]."'";
            for ($i = 1; $i < count( $codes ); $i++) {
                $query = $query." OR `a`.`code`='".$codes[$i]."'";
            }
        }
        $query = $query.") AND `a`.`visible` = 1";

        return $query;
    }

    /**
     * Вспмогательная функция, очищает строку поискового запроса с сайта
     * от всякого мусора
     */
    private function cleanSearchString($search) {
        $search = utf8_substr($search, 0, 64);
        // удаляем все, кроме букв и цифр
        $search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
        // сжимаем двойные пробелы
        $search = preg_replace('#\s+#u', ' ', $search);
        $search = trim($search);
        $search = utf8_strtolower($search);
        return $search;
    }

    private function searchLock() {
        file_put_contents( 'temp/search/' . $this->userFrontendModel->getVisitorId() . '.txt', '');
    }

    private function searchUnlock() {
        $file = 'temp/search' . $this->userFrontendModel->getVisitorId() . '.txt';
        if (is_file($file)) {
            // unlink($file);
        }
    }

    private function isSearchLocked() {
        $file = 'temp/search' . $this->userFrontendModel->getVisitorId() . '.txt';
        return is_file($file) && ((time() - filemtime($file)) < 5);
    }

}
