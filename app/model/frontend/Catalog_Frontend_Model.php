<?php
/**
 * Класс Catalog_Frontend_Model для работы с каталогом товаров, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Catalog_Frontend_Model extends Frontend_Model {

    // для блокирования ajax поиска для пользователя, в разработке
    private $userFrontendModel;

    public function __construct() {
        parent::__construct();
        // TODO: для блокирования ajax поиска для пользователя, в разработке
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

    /**
     * Функция возвращает дочерние категории категории с уникальным идентификатором $id
     */
    public function getCategoryChilds($id) {
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
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`price` AS `price`, `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`new` AS `new`, `a`.`hit` AS `hit`, `a`.`image` AS `image`,
                      `a`.`purpose` AS `purpose`, `a`.`techdata` AS `techdata`,
                      `a`.`features` AS `features`, `a`.`complect` AS `complect`,
                      `a`.`equipment` AS `equipment`, `a`.`padding` AS `padding`,
                      `a`.`category2` AS `second`, `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`, `a`.`group` AS `grp_id`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`id` = :id AND `a`.`visible` = 1";
        $product = $this->database->fetch($query, array('id' => $id));
        if (false === $product) {
            return null;
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
        $product['docs'] = $this->database->fetchAll($query, array('id' => $id));
        // ссылки на файлы документации
        foreach ($product['docs'] as $key => $value) {
            $product['docs'][$key]['url'] = $this->config->site->url . 'files/catalog/docs/' . $value['file'];
        }

        // добавляем информацию о сертификатах
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`title` AS `title`,
                      `a`.`filename` AS `file`, `a`.`count` AS `count`
                  FROM
                      `certs` `a` INNER JOIN `cert_prod` `b`
                      ON `a`.`id`=`b`.`cert_id`
                  WHERE
                      `b`.`prod_id` = :id
                  ORDER BY
                      `a`.`title`";
        $temp = $this->database->fetchAll($query, array('id' => $id));
        // ссылки на файлы сертификатов: у товара может быть несколько сертификатов, а каждый
        // сертификат может иметь несколько файлов (т.е. содержать несколько страниц)
        $certs = array();
        foreach ($temp as $key => $value) {
            if ( ! is_file('files/catalog/cert/' . $value['file'])) {
                continue;
            }
            $certs[$key]['title'] = $value['title'];
            $certs[$key]['count'] = $value['count'];
            $certs[$key]['files'][] = $this->config->site->url . 'files/catalog/cert/' . $value['file'];
            if ($value['count'] > 1) {
                $page = 1;
                while ($page < $value['count']) {
                    $file = str_replace('.jpg', $page.'.jpg', $value['file']);
                    $certs[$key]['files'][] = $this->config->site->url . 'files/catalog/cert/' . $file;
                    $page++;
                }
            }
        }
        $product['certs'] = $certs;

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
    public function getChildCategories($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array(), $sort = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->childCategories($id, $group, $maker, $hit, $new, $param, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker . '-hit-' . $hit
               . '-new-' . $new . '-param-' . md5(serialize($param)) . '-sort-' . $sort;
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
    protected function childCategories($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array(), $sort = 0) {

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
        // по лидерам продаж, по новинкам, параметрам подбора
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
                          (`category` IN (" . $childs . ") OR `category2` IN (" . $childs . "))
                          AND `a`.`visible` = 1";
            if ($group) { // фильтр по функциональной группе
                $query = $query . " AND `a`.`group` = " . $group;
            }
            if ($maker) { // фильтр по производителю
                $query = $query . " AND `a`.`maker` = " . $maker;
            }
            if ($hit) { // фильтр по лидерам продаж
                $query = $query . " AND `a`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `a`.`new` > 0";
            }
            if ( ! empty($param)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($group, $param);
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
            if ($hit) {
                $url = $url . '/hit/1';
            }
            if ($new) {
                $url = $url . '/new/1';
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
    public function getCategoryProducts($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array(), $sort = 0, $start = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryProducts($id, $group, $maker, $hit, $new, $param, $sort, $start);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker. '-hit-' . $hit
               . '-new-' . $new . '-param-' . md5(serialize($param)) . '-sort-' . $sort . '-start-' . $start;
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
    protected function categoryProducts($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array(), $sort = 0, $start = 0) {

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
        if ($hit) { // фильтр по лидерам продаж
            $tmp = $tmp . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $tmp = $tmp . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
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
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`price` AS `price`, `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`, `a`.`hit` AS `hit`, `a`.`new` AS `new`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`,
                      `a`.`group` AS `grp_id`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      (`a`.`category` IN (" . $childs . ") OR `a`.`category2` IN (" . $childs . "))" . $tmp . "
                      AND `a`.`visible` = 1
                  ORDER BY " . $order . "
                  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query);

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
            $products[$key]['action']['compare'] = $this->getURL('frontend/compare/addprd');
        }

        return $products;
    }

    /**
     * Возвращает количество товаров в категории $id и в ее потомках, т.е.
     * суммарное кол-во товаров не только в категории $id, но и в дочерних
     * категориях и в дочерних-дочерних категориях и так далее; результат
     * работы кэшируется
     */
    public function getCountCategoryProducts($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countCategoryProducts($id, $group, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' .$group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает количество товаров в категории $id и в ее потомках, т.е.
     * суммарное кол-во товаров не только в категории $id, но и в дочерних
     * категориях и в дочерних-дочерних категориях и так далее; результат
     * работы кэшируется
     */
    protected function countCategoryProducts($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {
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
        if ($hit) { // фильтр по лидерам продаж
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query);
    }

    /**
     * Возвращает массив производителей товаров в категории $id и в ее потомках,
     * т.е. не только производителей товаров этой категории, но и производителей
     * товаров в дочерних категориях, производителей товаров в дочерних-дочерних
     * категориях и так далее; результат работы кэшируется
     */
    public function getCategoryMakers($id, $group = 0, $hit = 0, $new = 0, $param = array()) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryMakers($id, $group, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit
               . '-new-' . $new . '-param-' . md5(serialize($param));
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
    protected function categoryMakers($id, $group = 0, $hit = 0, $new = 0, $param = array()) {

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

        if (0 == $group && 0 == $hit && 0 == $new) {
            return $makers;
        }

        // теперь подсчитываем количество товаров для каждого производителя с
        // учетом фильтров по функциональной группе, лидерам продаж, новинкам
        // и по параметрам
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
            if ($group) { // фильтр по функциональной группе
                $query = $query . " AND `b`.`group` = " . $group;
            }
            if ($hit) { // фильтров по лидерам продаж
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `b`.`new` > 0";
            }
            if ( ! empty($param)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($group, $param);
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
     * дочерних-дочерних категориях и т.д. Результат работы кэшируется
     */
    public function getCategoryGroups($id, $maker = 0, $hit = 0, $new = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryGroups($id, $maker, $hit, $new);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив функциональных групп товаров в категории $id и в ее потомках,
     * т.е. не только функциональные группы товаров этой категории, но и функциональные
     * группы товаров в дочерних категориях, функциональные группы товаров в
     * дочерних-дочерних категориях и т.д.
     */
    protected function categoryGroups($id, $maker = 0, $hit = 0, $new = 0) {

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
                      (`b`.`category` IN (" . $childs . ") OR `b`.`category2` IN (" . $childs . "))
                      AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`. `name`
                  ORDER BY
                      COUNT(*) DESC, `a`.`name`";
        $groups = $this->database->fetchAll($query);

        if (0 == $maker && 0 == $hit && 0 == $new) {
            return $groups;
        }

        // теперь подсчитываем количество товаров для каждой группы с
        // учетом фильтров по производителю, лидерам продаж, новинкам
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
                          AND `b`.`visible` = 1";
            if ($maker) {
                $query = $query . " AND `b`.`maker` = " . $maker;
            }
            if ($hit) {
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) {
                $query = $query . " AND `b`.`new` > 0";
            }
            $groups[$key]['count'] = $this->database->fetchOne($query);
        }

        return $groups;

    }

    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранной категории $id и всех ее потомков; результат работы кэшируется
     */
    public function getCategoryGroupParams($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryGroupParams($id, $group, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранной категории и всех ее потомков
     */
    protected function categoryGroupParams($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {

        if (0 == $group) {
            return array();
        }

        // получаем список всех параметров подбора для выбранной функциональной
        // группы и выбранной категории и всех ее потомков
        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
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
                      1, 2, 3, 4
                  ORDER BY
                      `f`.`name`, `g`.`name`";
        $result = $this->database->fetchAll($query);

        // теперь подсчитываем количество товаров для каждого параметра и каждого
        // значения параметра с учетом фильтров по производителю, лидерам продаж,
        // новинкам и по параметрам
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
                          AND `e`.`param_id` = " . $value['param_id'] . "
                          AND `e`.`value_id` = " . $value['value_id'] . "
                          AND `b`.`visible` = 1";
            if ($maker) { // фильтр по производителю
                $query = $query . " AND `b`.`maker` = " . $maker;
            }
            if ($hit) { // фильтр по лидерам продаж
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `b`.`new` > 0";
            }

            $temp = $param;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($group, $temp);
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
                $params[$counter] = array(
                    'id' => $value['param_id'],
                    'name' => $value['param_name']
                );
            }
            $params[$counter]['values'][] = array(
                'id' => $value['value_id'],
                'name' => $value['value_name'],
                'count' => $value['count']
            );
        }

        return $params;

    }

    /**
     * Функция возвращает количество лидеров продаж в категории $id и ее потомках,
     * с учетом фильтров по функциональной группе, производителю и т.п. Результат
     * работы кэшируется
     */
    public function getCountCategoryHit($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countCategoryHit($id, $group, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество лидеров продаж в категории $id и ее потомках,
     * с учетом фильтров по функциональной группе, производителю и т.п.
     */
    protected function countCategoryHit($id, $group, $maker, $hit, $new, $param) {

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
        if ( ! $hit) {
            // надо выяснить, сколько товаров будет найдено, если отметить
            // галочку «Лидер продаж»; на данный момент checkbox не отмечен,
            // но если пользователь его отметит - сколько будет найдено товаров?
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query);

    }

    /**
     * Функция возвращает количество новинок в категории $id и ее потомках, с
     * учетом фильтров по функциональной группе, производителю и т.п. Результат
     * работы кэшируется
     */
    public function getCountCategoryNew($id, $group = 0, $maker = 0, $hit = 0, $new = 0, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countCategoryNew($id, $group, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество новинок в категории $id и ее потомках, с
     * учетом фильтров по функциональной группе, производителю и т.п.
     */
    protected function countCategoryNew($id, $group, $maker, $hit, $new, $param) {

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
        if ($hit) { // фильтр по лидерам продаж
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ( ! $new) {
            // надо выяснить, сколько товаров будет найдено, если отметить
            // галочку «Новинка»; на данный момент checkbox не отмечен, но
            // если пользователь его отметит - сколько будет найдено товаров?
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query);

    }

    /**
     * Вспомогательная функция, возвращает массив идентификаторов товаров,
     * которые входят в функциональную группу $group и  подходят под параметры
     * подбора $param
     */
    private function getProductsByParam($group = 0, $param = array()) {

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
                          `a`.`group` = :group
                          AND `b`.`param_id` = :param_id AND `b`.`value_id` = :value_id";
            $result = $this->database->fetchAll(
                $query,
                array(
                    'group'    => $group,
                    'param_id' => $key,
                    'value_id' => $value
                ), $this->enableDataCache
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
        if (count($products) == 0) {
            return array();
        }

        return $products;

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
    public function catalogMenu($id = 0) {

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
     * Функция возвращает массив всех родителей категории с уникальным
     * идентификатром $id; результат работы кэшируется
     */
    private function getAllCategoryParents($id = 0) {

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
     * Функция возвращает массив всех производителей; результат работы кэшируется
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
     * Функция возвращает массив всех производителей
     */
    protected function allMakers($limit) {

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
            $query = $query . " LIMIT " . $limit;
        }
        $makers = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок на страницы отдельных производителей
        foreach($makers as $key => $value) {
            $makers[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
        }

        return $makers;

    }
    
    /**
     * Функция возвращает массив 15 производителей; результат работы кэшируется
     */
    public function getMakers() {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makers();
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
     * Функция возвращает массив 15 производителей
     */
    protected function makers() {

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
                      COUNT(*) DESC, `a`.`name`
                  LIMIT
                      15";
        $makers = $this->database->fetchAll($query);

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
    public function getMakerProducts($id, $group = 0, $hit = 0, $new = 0, $param = array(), $sort = 0, $start = 0) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerProducts($id, $group, $hit, $new, $param, $sort, $start);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
                . '-param-' . md5(serialize($param)) . '-sort-' . $sort . '-start-' . $start;
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
    protected function makerProducts($id, $group, $hit, $new, $param, $sort, $start) {

        $tmp = '';
        if ($group) { // фильтр по функциональной группе
            $tmp = $tmp . " AND `a`.`group` = " . $group;
        }
        if ($hit) { // фильтр по лидерам продаж
            $tmp = $tmp . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $tmp = $tmp . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return array();
            }
            $tmp = $tmp . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }

        switch ($sort) { // сортировка
            case 0: $temp = '`b`.`globalsort`, `a`.`sortorder`';  break; // сортировка по умолчанию
            case 1: $temp = '`a`.`price`';                        break; // сортировка по цене, по возрастанию
            case 2: $temp = '`a`.`price` DESC';                   break; // сортировка по цене, по убыванию
            case 3: $temp = '`a`.`name`';                         break; // сортировка по наименованию, по возрастанию
            case 4: $temp = '`a`.`name` DESC';                    break; // сортировка по наименованию, по убыванию
            case 5: $temp = '`a`.`code`';                         break; // сортировка по коду, по возрастанию
            case 6: $temp = '`a`.`code` DESC';                    break; // сортировка по коду, по убыванию
        }

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`new` AS `new`, `a`.`hit` AS `hit`,
                      `a`.`image` AS `image`, `a`.`price` AS `price`, `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`, `a`.`group` AS `grp_id`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1" . $tmp . "
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
            $products[$key]['action']['compare'] = $this->getURL('frontend/compare/addprd');
        }

        return $products;

    }

    /**
     * Функция возвращает кол-во товаров производителя с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getCountMakerProducts($id, $group = 0, $hit = 0, $new = 0, $param = array()) {
        
        $temp = '';
        if ($group) { // фильтр по функциональной группе
            $temp = $temp . " AND `a`.`group` = " . $group;
        }
        if ($hit) { // фильтр по лидерам продаж
            $temp = $temp . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $temp = $temp . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $temp = $temp . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a` INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1" . $temp;
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);
    }
    
    /**
     * Функция возвращает массив функциональных групп для производителя с
     * уникальным идентификатором $id; результат работы кэшируется
     */
    public function getMakerGroups($id, $hit = 0, $new = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerGroups($id, $hit, $new);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-hit-' . $hit . '-new-' . $new;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }
    
    /**
     * Функция возвращает массив функциональных групп для производителя с
     * уникальным идентификатором $id
     */
    protected function makerGroups($id, $hit, $new) {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`. `name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                  WHERE
                      `b`.`maker` = :maker
                      AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`. `name`
                  ORDER BY
                      COUNT(*) DESC, `a`.`name`";
        $groups = $this->database->fetchAll($query, array('maker' => $id));

        if (0 == $hit && 0 == $new) {
            return $groups;
        }

        // теперь подсчитываем количество товаров для каждой группы с
        // учетом фильтров по лидерам продаж и новинкам
        foreach ($groups as $key => $value)  {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `groups` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      WHERE
                          `b`.`maker` = :maker
                          AND `a`.`id` = :group
                          AND `b`.`visible` = 1";
            if ($hit) {
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) {
                $query = $query . " AND `b`.`new` > 0";
            }
            $groups[$key]['count'] = $this->database->fetchOne($query, array('maker' => $id, 'group' => $value['id']));
        }

        return $groups;
    }
    
    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранного производителя $id; результат работы кэшируется
     */
    public function getMakerGroupParams($id, $group = 0, $hit = 0, $new = 0, $param = array()) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerGroupParams($id, $group, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранного производителя $id
     */
    protected function makerGroupParams($id, $group = 0, $hit = 0, $new = 0, $param = array()) {

        if (0 == $group) {
            return array();
        }

        // получаем список всех параметров подбора для выбранной функциональной
        // группы и выбранного производителя
        $query = "SELECT
                      `e`.`id` AS `param_id`, `e`.`name` AS `param_name`,
                      `f`.`id` AS `value_id`, `f`.`name` AS `value_name`,
                      COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `product_param_value` `d` ON `b`.`id` = `d`.`product_id`
                      INNER JOIN `params` `e` ON `d`.`param_id` = `e`.`id`
                      INNER JOIN `values` `f` ON `d`.`value_id` = `f`.`id`
                  WHERE
                      `a`.`id` = :group
                      AND `b`.`maker` = :maker
                      AND `b`.`visible` = 1
                  GROUP BY
                      1, 2, 3, 4
                  ORDER BY
                      `e`.`name`, `f`.`name`";
        $result = $this->database->fetchAll($query, array('group' => $group, 'maker' => $id));

        // теперь подсчитываем количество товаров для каждого параметра и каждого значения
        // параметра с учетом фильтров по лидерам продаж, по новинкам и по параметрам
        foreach ($result as $key => $value)  {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `groups` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                          INNER JOIN `product_param_value` `d` ON `b`.`id` = `d`.`product_id`
                          INNER JOIN `params` `e` ON `d`.`param_id` = `e`.`id`
                          INNER JOIN `values` `f` ON `d`.`value_id` = `f`.`id`
                      WHERE
                          `a`.`id` = :group
                          AND `b`.`maker` = :maker
                          AND `d`.`param_id` = :param_id
                          AND `d`.`value_id` = :value_id
                          AND `b`.`visible` = 1";
            if ($hit) { // фильтр по лидерам продаж
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `b`.`new` > 0";
            }

            $temp = $param;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($group, $temp);
                if ( ! empty($ids)) {
                    $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                    $result[$key]['count'] = $this->database->fetchOne(
                        $query,
                        array('group' => $group, 'maker' => $id, 'param_id' => $value['param_id'], 'value_id' => $value['value_id'])
                    );
                } else {
                    $result[$key]['count'] = 0;
                }
            } else {
                $result[$key]['count'] = $this->database->fetchOne(
                    $query,
                    array('group' => $group, 'maker' => $id, 'param_id' => $value['param_id'], 'value_id' => $value['value_id'])
                );
            }
        }

        $params = array();
        $param_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $params[$counter] = array(
                    'id' => $value['param_id'],
                    'name' => $value['param_name']
                );
            }
            $params[$counter]['values'][] = array(
                'id' => $value['value_id'],
                'name' => $value['value_name'],
                'count' => $value['count']
            );
        }

        return $params;

    }
    
    /**
     * Функция возвращает количество лидеров продаж для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, новинкам и
     * параметрам. Результат работы кэшируется
     */
    public function getCountMakerHit($id, $group = 0, $hit = 0, $new = 0, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countMakerHit($id, $group, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество лидеров продаж для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, новинкам и
     * параметрам
     */
    protected function countMakerHit($id, $group, $hit, $new, $param) {

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :maker AND `a`.`visible` = 1";
        if ($group) { // фильтр по функциональной группе
            $query = $query . " AND `a`.`group` = " . $group;
        }
        if ( ! $hit) {
            // надо выяснить, сколько товаров будет найдено, если поставить
            // галочку «Лидер продаж»; на данный момент checkbox не отмечен, но
            // если пользователь его отметит - сколько будет найдено товаров?
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query, array('maker' => $id));

    }

    /**
     * Функция возвращает количество новинок для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, лидерам
     * продаж и параметрам. Результат работы кэшируется
     */
    public function getCountMakerNew($id, $group = 0, $hit = 0, $new = 0, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countMakerNew($id, $group, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество новинок для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, лидерам
     * продаж и параметрам
     */
    protected function countMakerNew($id, $group, $hit, $new, $param) {

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`maker` = :maker AND `a`.`visible` = 1";
        if ($group) { // фильтр по функциональной группе
            $query = $query . " AND `a`.`group` = " . $group;
        }
        if ($hit) { // фильтр по лидерам продаж
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ( ! $new) {
            // надо выяснить, сколько товаров будет найдено, если поставить галочку
            // «Новинка»; на данный момент checkbox не отмечен, но если пользователь
            // его отметит - сколько будет найдено товаров?
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($group, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query, array('maker' => $id));
        
    }

    /**
     * Функция возвращает ЧПУ для категории с уникальным идентификатором $id с учетом
     * фильтров и сортировки товаров; результат работы кэшируется
     */
    public function getCategoryURL($id, $group, $maker, $hit, $new, $param, $sort) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryURL($id, $group, $maker, $hit, $new, $param, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker. '-hit-' . $hit
               . '-new-' . $new . '-param-' . md5(serialize($param)) . '-sort-' . $sort;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }
    
    /**
     * Функция возвращает ЧПУ для категории с уникальным идентификатором $id с учетом
     * фильтров и сортировки товаров
     */
    protected function categoryURL($id, $group, $maker, $hit, $new, $param, $sort) {

        $url = 'frontend/catalog/category/id/' . $id;
        if ($group) {
            $url = $url . '/group/' . $group;
        }
        if ($maker) {
            $url = $url . '/maker/' . $maker;
        }
        if ($hit) {
            $url = $url . '/hit/1';
        }
        if ($new) {
            $url = $url . '/new/1';
        }
        if ( ! empty($param)) {
            $temp = array();
            foreach ($param as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/param/' . implode('-', $temp);
        }
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        return $this->getURL($url);

    }
    
    /**
     * Функция возвращает массив ссылок для сортировки товаров категории $id по цене,
     * наименованию, коду (артикулу); результат работы кэшируется
     */
    public function getCategorySortOrders($id, $group, $maker, $hit, $new, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categorySortOrders($id, $group, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров категории $id по цене,
     * наименованию, коду (артикулу)
     */
    protected function categorySortOrders($id, $group, $maker, $hit, $new, $param) {

        $url = 'frontend/catalog/category/id/' . $id;
        if ($group) {
            $url = $url . '/group/' . $group;
        }
        if ($maker) {
            $url = $url . '/maker/' . $maker;
        }
        if ($hit) {
            $url = $url . '/hit/1';
        }
        if ($new) {
            $url = $url . '/new/1';
        }
        if ( ! empty($param)) {
            $temp = array();
            foreach ($param as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/param/' . implode('-', $temp);
        }
        /*
         * варианты сортировки:
         * 0 - по умолчанию,
         * 1 - по цене, по возрастанию
         * 2 - по цене, по убыванию
         * 3 - по наименованию, по возрастанию
         * 4 - по наименованию, по убыванию
         * 5 - по коду, по возрастанию
         * 6 - по коду, по убыванию
         */
        $sortorders = array();
        for ($i = 0; $i <= 6; $i++) {
            switch ($i) {
                case 0: $name = 'без сортировки';  break;
                case 1: $name = 'цена, возр.';     break;
                case 2: $name = 'цена, убыв.';     break;
                case 3: $name = 'название, возр.'; break;
                case 4: $name = 'название, убыв.'; break;
                case 5: $name = 'код, возр.';      break;
                case 6: $name = 'код, убыв.';      break;
            }
            $temp = $i ? $url . '/sort/' . $i : $url;
            $sortorders[$i] = array(
                'url'  => $this->getURL($temp),
                'name' => $name
            );
        }
        return $sortorders;

    }
    
    /**
     * Функция возвращает ЧПУ для страницы производителя с уникальным идентификатором $id,
     * с учетом фильтров и сортировки товаров производителя; результат работы кэшируется
     */
    public function getMakerURL($id, $group = 0, $hit = 0, $new = 0, $param = array(), $sort = 0) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerURL($id, $group, $hit, $new, $param, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param)) . '-sort-' . $sort;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }
    
    /**
     * Функция возвращает ЧПУ для страницы производителя с уникальным идентификатором $id,
     * с учетом фильтров и сортировки товаров производителя
     */
    protected function makerURL($id, $group, $hit, $new, $param, $sort) {

        $url = 'frontend/catalog/maker/id/' . $id;
        if ($group) {
            $url = $url . '/group/' . $group;
        }
        if ($hit) {
            $url = $url . '/hit/1';
        }
        if ($new) {
            $url = $url . '/new/1';
        }
        if ( ! empty($param)) {
            $temp = array();
            foreach ($param as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/param/' . implode('-', $temp);
        }
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        return $this->getURL($url);

    }
    
    /**
     * Функция возвращает массив ссылок для сортировки товаров производителя $id по цене,
     * наименованию, коду (артикулу); результат работы кэшируется
     */
    public function getMakerSortOrders($id, $group = 0, $hit = 0, $new = 0, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerSortOrders($id, $group, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
                . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров производителя $id по цене,
     * наименованию, коду (артикулу)
     */
    protected function makerSortOrders($id, $group, $hit, $new, $param) {

        $url = 'frontend/catalog/maker/id/' . $id;
        if ($group) {
            $url = $url . '/group/' . $group;
        }
        if ($hit) {
            $url = $url . '/hit/1';
        }
        if ($new) {
            $url = $url . '/new/1';
        }
        if ( ! empty($param)) {
            $temp = array();
            foreach ($param as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/param/' . implode('-', $temp);
        }
        /*
         * варианты сортировки:
         * 0 - по умолчанию,
         * 1 - по цене, по возрастанию
         * 2 - по цене, по убыванию
         * 3 - по наименованию, по возрастанию
         * 4 - по наименованию, по убыванию
         * 5 - по коду, по возрастанию
         * 6 - по коду, по убыванию
         */
        $sortorders = array();
        for ($i = 0; $i <= 6; $i++) {
            switch ($i) {
                case 0: $name = 'без сортировки';  break;
                case 1: $name = 'цена, возр.';     break;
                case 2: $name = 'цена, убыв.';     break;
                case 3: $name = 'название, возр.'; break;
                case 4: $name = 'название, убыв.'; break;
                case 5: $name = 'код, возр.';      break;
                case 6: $name = 'код, убыв.';      break;
            }
            $temp = $i ? $url . '/sort/' . $i : $url;
            $sortorders[$i] = array(
                'url'  => $this->getURL($temp),
                'name' => $name
            );
        }
        return $sortorders;

    }

    /**
     * Функция возвращает результаты поиска по каталогу; результат работы
     * кэшируется
     */
    public function getSearchResults($search = '', $start = 0, $ajax = false) {

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
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает результаты поиска по каталогу; результат работы
     * кэшируется
     */
    protected function searchResults($search = '', $start = 0, $ajax = false) {
        
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return array();
        }
        $query = $this->getSearchQuery($search);
        if (empty($query)) {
            return array();
        }
        
        $query = $query . ' LIMIT ' . $start . ', ' . $this->config->pager->frontend->products->perpage;
        $result = $this->database->fetchAll($query, array(), $this->enableDataCache);
        // добавляем в массив результатов поиска информацию об URL товаров и фото
        foreach($result as $key => $value) {
            if ($ajax) { // для поиска в шапке сайта
                $result[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                unset(
                    $result[$key]['price2'],
                    $result[$key]['price3'],
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
                $result[$key]['action']['compare'] = $this->getURL('frontend/compare/addprd');
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
        // небольшой хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
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
                      `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`,
                      `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `a`.`hit` AS `hit`,
                      `a`.`new` AS `new`,
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
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового
        // запроса, как и для первого слова
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
        if ( ! empty($matches)) {
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
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового
        // запроса, как и для первого слова
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
        if ( ! empty($matches)) {
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
        foreach ($words as $word) {
            if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
            if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        if (count($codes) > 0) {
            $query = $query." OR `a`.`code`='".$codes[0]."'";
            for ($i = 1; $i < count($codes); $i++) {
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

    // для блокирования поиска для пользователя, в разработке
    private function searchLock() {
        file_put_contents( 'temp/search/' . $this->userFrontendModel->getVisitorId() . '.txt', '');
    }

    // для блокирования поиска для пользователя, в разработке
    private function searchUnlock() {
        $file = 'temp/search' . $this->userFrontendModel->getVisitorId() . '.txt';
        if (is_file($file)) {
            // unlink($file);
        }
    }

    // для блокирования поиска для пользователя, в разработке
    private function isSearchLocked() {
        $file = 'temp/search' . $this->userFrontendModel->getVisitorId() . '.txt';
        return is_file($file) && ((time() - filemtime($file)) < 5);
    }

}
