<?php
/**
 * Класс Maker_Catalog_Frontend_Model для работы с производителями каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Maker_Catalog_Frontend_Model extends Catalog_Frontend_Model {
    
    /*
     * public function getAllMakers(...)
     * protected function allMakers(...)
     * public function getMakers()
     * protected function makers()
     * public function getMaker(...)
     */

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает массив всех производителей; результат работы кэшируется
     */
    public function getAllMakers() {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allMakers();
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
     * Функция возвращает массив всех производителей
     */
    protected function allMakers() {

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
        $makers = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок на страницы отдельных производителей
        foreach($makers as $key => $value) {
            $makers[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
        }

        return $makers;

    }
    
    /**
     * Функция возвращает массив производителей для правой колонки; результат работы кэшируется
     */
    public function getMakers($limit) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makers($limit);
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
     * Функция возвращает массив производителей для правой колонки
     */
    protected function makers($limit) {

        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
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
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                  WHERE
                      `a`.`id` IN (" . $ids . ") AND
                      `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`.`name`
                  ORDER BY
                      `a`.`name`";
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
     * Функция возвращает результаты поиска производителя; результат работы
     * кэшируется
     */
    public function getMakerSearchResult($query) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerSearchResult($query);
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
     * Функция возвращает результаты поиска производителя
     */
    protected function makerSearchResult($query) {

        $query = $this->cleanSearchString($query);
        if (empty($query)) {
            return array();
        }
        $words = explode(' ', $query);
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`
                  FROM
                      `makers` `a`
                  WHERE
                      EXISTS (SELECT 1 FROM `products` `b` WHERE `a`.`id` = `b`.`maker` AND `b`.`visible` = 1)
                      AND `a`.`name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." AND `a`.`name` LIKE '%".$words[$i]."%'";
        }
        $query = $query." ORDER BY `a`.`name` LIMIT 10";
        $result = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок на страницы производителей
        foreach($result as $key => $value) {
            $result[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
        }
        return $result;

    }

    /**
     * Функция возвращает массив товаров производителя с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getMakerProducts($id, $group, $hit, $new, $param, $sort, $start) {

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
                      INNER JOIN `groups` `c` ON `a`.`group` = `c`.`id`
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
    public function getCountMakerProducts($id, $group, $hit, $new, $param) {
        
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
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `groups` `c` ON `a`.`group` = `c`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1" . $temp;
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);
    }
    
    /**
     * Функция возвращает массив функциональных групп для производителя с
     * уникальным идентификатором $id; результат работы кэшируется
     */
    public function getMakerGroups($id, $hit, $new) {
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
                      `a`.`name`, COUNT(*) DESC";
        $groups = $this->database->fetchAll($query, array('maker' => $id));

        if (count($groups) > 15) {
            $bound = false;
            foreach ($groups as $value)  {
                if ($value['count'] > 1) {
                    $bound = true;
                    break;
                }
            }
            if ($bound) {
                $first = array();
                $second = array();
                foreach ($groups as $value)  {
                    if ($value['count'] > 1) {
                        $first[] = $value;
                    } else {
                        $second[] = $value;
                    }
                }
            }
            if (!empty($second)) {
                $second[0]['bound'] = true;
            }
            $groups = array_merge($first, $second);
        }

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
    public function getMakerGroupParams($id, $group, $hit, $new, $param) {
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
    protected function makerGroupParams($id, $group, $hit, $new, $param) {

        if (0 == $group) {
            return array();
        }

        // получаем список всех параметров подбора для выбранной функциональной
        // группы и выбранного производителя
        $query = "SELECT
                      `d`.`id` AS `param_id`, `d`.`name` AS `param_name`,
                      `e`.`id` AS `value_id`, `e`.`name` AS `value_name`,
                      COUNT(*) AS `count`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `product_param_value` `c` ON `a`.`id` = `c`.`product_id`
                      INNER JOIN `params` `d` ON `c`.`param_id` = `d`.`id`
                      INNER JOIN `values` `e` ON `c`.`value_id` = `e`.`id`
                  WHERE
                      `a`.`group` = :group
                      AND `a`.`maker` = :maker
                      AND `a`.`visible` = 1
                  GROUP BY
                      1, 2, 3, 4
                  ORDER BY
                      `d`.`name`, `e`.`name`";
        $result = $this->database->fetchAll($query, array('group' => $group, 'maker' => $id));

        // теперь подсчитываем количество товаров для каждого параметра и каждого значения
        // параметра с учетом фильтров по лидерам продаж, по новинкам и по параметрам
        foreach ($result as $key => $value)  {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `products` `a`
                          INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                          INNER JOIN `product_param_value` `c` ON `a`.`id` = `c`.`product_id`
                          INNER JOIN `params` `d` ON `c`.`param_id` = `d`.`id`
                          INNER JOIN `values` `e` ON `c`.`value_id` = `e`.`id`
                      WHERE
                          `a`.`group` = :group
                          AND `a`.`maker` = :maker
                          AND `a`.`visible` = 1
                          AND `c`.`param_id` = :param_id
                          AND `c`.`value_id` = :value_id";
            if ($hit) { // фильтр по лидерам продаж
                $query = $query . " AND `a`.`hit` > 0";
            }
            if ($new) { // фильтр по новинкам
                $query = $query . " AND `a`.`new` > 0";
            }

            $temp = $param;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByParam($group, $temp);
                if ( ! empty($ids)) {
                    $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
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
                    'name' => $value['param_name'],
                    'selected' => isset($param[$value['param_id']]),
                );
            }
            $params[$counter]['values'][] = array(
                'id' => $value['value_id'],
                'name' => $value['value_name'],
                'count' => $value['count'],
                'selected' => in_array($value['value_id'], $param),
            );
        }

        return $params;

    }
    
    /**
     * Функция возвращает количество лидеров продаж для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, новинкам и
     * параметрам. Результат работы кэшируется
     */
    public function getCountMakerHit($id, $group, $hit, $new, $param) {

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
    public function getCountMakerNew($id, $group, $hit, $new, $param = array()) {

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
                      INNER JOIN `groups` `c` ON `a`.`group` = `c`.`id`
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
     * Функция возвращает ЧПУ для страницы производителя с уникальным идентификатором $id,
     * с учетом фильтров и сортировки товаров производителя; результат работы кэшируется
     */
    public function getMakerURL($id, $group, $hit, $new, $param, $sort) {

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
    public function getMakerSortOrders($id, $group, $hit, $new, $param) {

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

}
