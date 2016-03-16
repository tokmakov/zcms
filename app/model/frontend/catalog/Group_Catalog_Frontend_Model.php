<?php
/**
 * Класс Group_Catalog_Frontend_Model для работы с функциональными группами каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Group_Catalog_Frontend_Model extends Catalog_Frontend_Model {
    
    /*
     * public function getAllGroups()
     * protected function allGroups() 
     * public function getGroups(...)
     * protected function groups(...)
     * public function getGroupSearchResult(...)
     * protected function groupSearchResult(...)
     * public function getGroupName(...)
     * public function getGroupProducts(...)
     * protected function groupProducts(...)
     * public function getCountGroupProducts(...)
     * public function getGroupMakers(...)
     * protected function groupMakers(...)
     */

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает массив всех функциональных групп; результат работы кэшируется
     */
    public function getAllGroups() {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allGroups();
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
     * Функция возвращает массив всех функциональных групп
     */
    protected function allGroups() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visible` = 1
                  GROUP BY
                     1, 2
                  ORDER BY
                      `a`.`name`, COUNT(*) DESC";
        $groups = $this->database->fetchAll($query);
        // добавляем ссылки
        foreach ($groups as $key => $value) {
            $groups[$key]['url'] = $this->getURL('frontend/catalog/group/id/' . $value['id']);
        }
        return $groups;

    }
    
    /**
     * Функция возвращает массив функциональных групп для правой колонки; результат работы кэшируется
     */
    public function getGroups($limit) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groups($limit);
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
     * Функция возвращает массив функциональных групп для правой колонки
     */
    protected function groups($limit) {

        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`
                  ORDER BY
                      COUNT(*) DESC
                  LIMIT " . $limit;
        $temp = $this->database->fetchAll($query);
        $ids = array();
        foreach ($temp as $value) {
            $ids[] = $value['id'];
        }
        $ids = implode(',', $ids);
        
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `groups` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`group`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      `a`.`id` IN (" . $ids . ") AND
                      `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`.`name`
                  ORDER BY
                      `a`.`name`";
        $groups = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок на страницы функциональных групп
        foreach($groups as $key => $value) {
            $groups[$key]['url'] = $this->getURL('frontend/catalog/group/id/' . $value['id']);
        }

        return $groups;

    }

    /**
     * Функция возвращает результаты поиска функциональной группы; результат работы
     * кэшируется
     */
    public function getGroupSearchResult($query) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupSearchResult($query);
        }
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-query-' . md5($query);
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }
    
    /**
     * Функция возвращает результаты поиска функциональной группы
     */
    protected function groupSearchResult($query) {
        $query = $this->cleanSearchString($query);
        if (empty($query)) {
            return array();
        }
        $words = explode(' ', $query);
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `groups`
                  WHERE
                      `name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." AND `name` LIKE '%".$words[$i]."%'";
        }
        $query = $query." ORDER BY `name` LIMIT 10";
        $result = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок на страницы функциональных групп
        foreach($result as $key => $value) {
            $result[$key]['url'] = $this->getURL('frontend/catalog/group/id/' . $value['id']);
        }
        return $result;
    }

    /**
     * Функция возвращает наименование функциональной группы
     */
    public function getGroupName($id) {
        $query = "SELECT `name` FROM `groups` WHERE `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);
    }
    
    /**
     * Функция возвращает массив товаров функциональной группы с уникальным
     * идентификатором $id; результат работы кэшируется
     */
    public function getGroupProducts($id, $maker, $hit, $new, $param, $sort, $start) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupProducts($id, $maker, $hit, $new, $param, $sort, $start);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
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
    protected function groupProducts($id, $maker, $hit, $new, $param, $sort, $start) {

        $tmp = '';
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
            $ids = $this->getProductsByParam($id, $param);
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
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`group` = :id AND `a`.`visible` = 1" . $tmp . "
                  ORDER BY " . $temp . "
                  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query, array('id' => $id));

        // добавляем в массив URL ссылок на товары и фото
        foreach($products as $key => $value) {
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
     * Функция возвращает кол-во товаров функциональной группы с уникальным
     * идентификатором $id; результат работы кэшируется
     */
    public function getCountGroupProducts($id, $maker, $hit, $new, $param) {

        $temp = '';
        if ($maker) { // фильтр по производителю
            $temp = $temp . " AND `a`.`maker` = " . $maker;
        }
        if ($hit) { // фильтр по лидерам продаж
            $temp = $temp . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $temp = $temp . " AND `a`.`new` > 0";
        }
        if ( ! empty($param)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByParam($id, $param);
            if (empty($ids)) {
                return 0;
            }
            $temp = $temp . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`group` = :id AND `a`.`visible` = 1" . $temp;
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);

    }

    /**
     * Возвращает массив производителей товаров для функциональной группы
     * с уникальным идентификатором $id; результат работы кэшируется
     */
    public function getGroupMakers($id, $hit, $new, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupMakers($id, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-hit-' . $hit. '-new-'
            . $new . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Возвращает массив производителей товаров для функциональной
     * группы с уникальным идентификатором $id
     */
    protected function groupMakers($id, $hit, $new, $param) {

        // получаем список всех производителей для функциональной группы
        $query = "SELECT
                      `a`.`id` AS `id`, `a`. `name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                  WHERE
                      `b`.`group` = :id
                      AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`. `name`
                  ORDER BY
                      `a`.`name`";

        $makers = $this->database->fetchAll($query, array('id' => $id));

        if (0 == $hit && 0 == $new && empty($param)) {
            return $makers;
        }

        // теперь подсчитываем количество товаров для каждого производителя с
        // учетом фильтров по лидерам продаж, новинкам и по параметрам
        foreach ($makers as $key => $value) {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `makers` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      WHERE
                          `b`.`group` = :id AND
                          `a`.`id` = :maker AND
                          `b`.`visible` = 1";
            if ($hit) { // фильтров по лидерам продаж
                $query = $query . " AND `b`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `b`.`new` > 0";
            }
            if ( ! empty($param)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($id, $param);
                if ( ! empty($ids)) {
                    $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                    $makers[$key]['count'] = $this->database->fetchOne($query, array('id' => $id, 'maker' => $value['id']));
                } else {
                    $makers[$key]['count'] = 0;
                }
            } else {
                $makers[$key]['count'] = $this->database->fetchOne($query, array('id' => $id, 'maker' => $value['id']));
            }
        }

        return $makers;

    }
    
    /**
     * Функция возвращает массив параметров подбора для товаров функциональной
     * группы с уникальным идентификатором $id; результат работы кэшируется
     */
    public function getGroupParams($id, $maker, $hit, $new, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupParams($id, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
            . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив параметров подбора для товаров функциональной
     * группы с уникальным идентификатором $id
     */
    protected function groupParams($id, $maker, $hit, $new, $param) {

        // получаем список всех параметров подбора для функциональной группы
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
                      `a`.`id` = :id AND
                      `b`.`visible` = 1
                  GROUP BY
                      1, 2, 3, 4
                  ORDER BY
                      `e`.`name`, `f`.`name`";
        $result = $this->database->fetchAll($query, array('id' => $id));

        // теперь подсчитываем количество товаров для каждого параметра и каждого значения
        // параметра с учетом фильтров производителю, лидерам продаж, новинкам и параметрам
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
                          `a`.`id` = :id AND
                          `b`.`visible` = 1 AND
                          `d`.`param_id` = :param_id AND
                          `d`.`value_id` = :value_id";
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
                $ids = $this->getProductsByParam($id, $temp);
                if ( ! empty($ids)) {
                    $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                    $result[$key]['count'] = $this->database->fetchOne(
                        $query,
                        array(
                            'id'       => $id,
                            'param_id' => $value['param_id'],
                            'value_id' => $value['value_id']
                        )
                    );
                } else {
                    $result[$key]['count'] = 0;
                }
            } else {
                $result[$key]['count'] = $this->database->fetchOne(
                    $query,
                    array(
                        'id'       => $id,
                        'param_id' => $value['param_id'],
                        'value_id' => $value['value_id']
                    )
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
                    'id'       => $value['param_id'],
                    'name'     => $value['param_name'],
                    'selected' => isset($param[$value['param_id']]),
                );
            }
            $params[$counter]['values'][] = array(
                'id'       => $value['value_id'],
                'name'     => $value['value_name'],
                'count'    => $value['count'],
                'selected' => in_array($value['value_id'], $param),
            );
        }

        return $params;

    }
    
    /**
     * Функция возвращает количество лидеров продаж для функциональной группы с уникальным
     * идентификатором $id с учетом фильтров по производителю, новинкам и параметрам.
     * Результат работы кэшируется
     */
    public function getCountGroupHit($id, $maker, $hit, $new, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countGroupHit($id, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество лидеров продаж для функциональной группы с уникальным
     * идентификатором $id с учетом фильтров по производителю, новинкам и параметрам
     */
    protected function countGroupHit($id, $maker, $hit, $new, $param) {

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`group` = :id AND `a`.`visible` = 1";
        if ($maker) { // фильтр по производителю
            $query = $query . " AND `a`.`maker` = " . $maker;
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
            $ids = $this->getProductsByParam($id, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query, array('id' => $id));

    }

    /**
     * Функция возвращает количество новинок для функциональной группы с уникальным
     * идентификатором $id с учетом фильтров по производителю, лидерам продаж и параметрам.
     * Результат работы кэшируется
     */
    public function getCountGroupNew($id, $maker, $hit, $new, $param = array()) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countGroupNew($id, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
               . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество новинок для функциональной группы с уникальным
     * идентификатором $id с учетом фильтров по производителю, лидерам продаж и параметрам
     */
    protected function countGroupNew($id, $maker, $hit, $new, $param) {

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`group` = :id AND `a`.`visible` = 1";
        if ($maker) { // фильтр по производителю
            $query = $query . " AND `a`.`maker` = " . $maker;
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
            $ids = $this->getProductsByParam($id, $param);
            if (empty($ids)) {
                return 0;
            }
            $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
        }
        return $this->database->fetchOne($query, array('id' => $id));
        
    }
    
    /**
     * Функция возвращает ЧПУ для страницы функциональной группы с уникальным
     * идентификатором $id, с учетом фильтров и сортировки; результат работы
     * кэшируется
     */
    public function getGroupURL($id, $maker, $hit, $new, $param, $sort) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupURL($id, $maker, $hit, $new, $param, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
            . '-param-' . md5(serialize($param)) . '-sort-' . $sort;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает ЧПУ для страницы функциональной группы с уникальным
     * идентификатором $id, с учетом фильтров и сортировки
     */
    protected function groupURL($id, $maker, $hit, $new, $param, $sort) {

        $url = 'frontend/catalog/group/id/' . $id;
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
     * Функция возвращает массив ссылок для сортировки товаров функциональной группы
     * $id по цене, наименованию, коду (артикулу); результат работы кэшируется
     */
    public function getGroupSortOrders($id, $maker, $hit, $new, $param) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->groupSortOrders($id, $maker, $hit, $new, $param);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-hit-' . $hit . '-new-' . $new
            . '-param-' . md5(serialize($param));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров функциональной группы
     * $id по цене, наименованию, коду (артикулу)
     */
    protected function groupSortOrders($id, $maker, $hit, $new, $param) {

        $url = 'frontend/catalog/group/id/' . $id;
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

}
