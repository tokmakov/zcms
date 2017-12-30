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
     * public function getMakerSearchResult(...)
     * protected function makerSearchResult(...)
     * public function getMakerProducts(...)
     * protected function makerProducts(...)
     * public function getCountMakerProducts(...)
     * public function getMakerGroups(...)
     * protected function makerGroups(...)
     * public function getMakerGroupParams(...)
     * protected function makerGroupParams(...)
     * public function getCountMakerHit(...)
     * protected function countMakerHit(...)
     * public function getCountMakerNew(...)
     * protected function countMakerNew(...)
     * public function getMakerURL(...)
     * protected function makerURL(...)
     * public function getMakerSortOrders(...)
     * protected function makerSortOrders(...)
     * public function getOthersPerPage(...)
     * protected function othersPerPage(...)
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех производителей; результат работы кэшируется
     */
    public function getAllMakers($sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allMakers($sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-sort-' . $sort . '-perpage-' . $perpage;
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
    protected function allMakers($sort, $perpage) {

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
            $url = 'frontend/catalog/maker/id/' . $value['id'];
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) {
                $url = $url . '/perpage/' . $perpage;
            }
            $makers[$key]['url'] = $this->getURL($url);
        }

        return $makers;

    }

    /**
     * Функция возвращает массив производителей для левой колонки и для
     * главной страницы каталога; результат работы кэшируется
     */
    public function getMakers($limit, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makers($limit, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-limit-' . $limit . '-sort-' . $sort . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив производителей для левой колонки и для
     * главной страницы каталога
     */
    protected function makers($limit, $sort, $perpage) {

        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
                  WHERE
                      `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`
                  ORDER BY
                      COUNT(*) DESC
                  LIMIT
                      :limit";
        $temp = $this->database->fetchAll($query, array('limit' => $limit));
        $ids = array();
        foreach ($temp as $value) {
            $ids[] = $value['id'];
        }
        if (empty($ids)) {
            return array();
        }
        $ids = implode(',', $ids);

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
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
            $url = 'frontend/catalog/maker/id/' . $value['id'];
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) {
                $url = $url . '/perpage/' . $perpage;
            }
            $makers[$key]['url'] = $this->getURL($url);
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
    public function getMakerSearchResult($query, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makerSearchResult($query, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-query-' . md5($query) . '-sort-' . $sort . '-perpage-' . $perpage;
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
    protected function makerSearchResult($query, $sort, $perpage) {

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
            $url = 'frontend/catalog/maker/id/' . $value['id'];
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) {
                $url = $url . '/perpage/' . $perpage;
            }
            $result[$key]['url'] = $this->getURL($url);
        }
        return $result;

    }

    /**
     * Функция возвращает массив товаров производителя с уникальным идентификатором
     * $id; результат работы кэшируется
     */
    public function getMakerProducts($id, $group, $hit, $new, $filter, $sort, $start, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makerProducts($id, $group, $hit, $new, $filter, $sort, $start, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit
            . '-new-' . $new. '-filter-' . md5(serialize($filter)) . '-sort-'
            . $sort . '-start-' . $start . '-perpage-' . $perpage;
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
    protected function makerProducts($id, $group, $hit, $new, $filter, $sort, $start, $perpage) {

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
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`, `a`.`group` AS `grp_id`,
                      `c`.`name` AS `grp_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `groups` `c` ON `a`.`group` = `c`.`id`
                  WHERE
                      `a`.`maker` = :id AND `a`.`visible` = 1" . $tmp . "
                  ORDER BY
                      " . $temp . "
                  LIMIT
                      :start, :limit";
        $limit = $perpage ? $perpage : $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll(
            $query,
            array(
                'id'    => $id,
                'start' => $start,
                'limit' => $limit
            )
        );

        // добавляем в массив URL ссылок на товары и фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network
            $host = $this->config->cdn->url;
        }
        foreach($products as $key => $value) {
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/nophoto.jpg';
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
    public function getCountMakerProducts($id, $group, $hit, $new, $filter) {

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
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
    public function getMakerGroups($id, $group, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makerGroups($id, $group, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-filter-' . md5(serialize($filter));
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
    protected function makerGroups($id, $group, $hit, $new, $filter) {

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

        /*
         * Небольшой трюк, чтобы визуально представить список функциональных групп более наглядно:
         * фактически, в виде двух списков. Первый список — функциональные группы, содержащие более
         * одного товара, второй список — функциональные группы, содержащие только один товар.
         * Разделение на два списка происходит, только если функциональных групп больше 15 шт.
         */
        if (count($groups) > 15) {
            $bound = false;
            foreach ($groups as $value)  { // есть хоть одна функциональная группа, содержащая один товар?
                if ($value['count'] > 1) {
                    $bound = true;
                    break;
                }
            }
            if ($bound) {
                $first = array();  // функциональные группы производителя, содержащие более одного товара
                $second = array(); // функциональные группы производителя, содержащие только один товар
                foreach ($groups as $value)  {
                    if ($value['count'] > 1) {
                        $first[] = $value;
                    } else {
                        $second[] = $value;
                    }
                }
            }
            if ( ! empty($second)) {
                $second[0]['bound'] = true;
            }
            $groups = array_merge($first, $second);
        }

        if (0 == $group && 0 == $hit && 0 == $new) {
            return $groups;
        }

        if ($group && empty($filter) && 0 == $hit && 0 == $new) {
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
            /*
             * если выбрана функциональная группа, то на количество товаров в ней
             * влияют выбранные параметры подбора; но они влияют только на количество
             * товаров выбранной функциональной группы, потому как для других
             * функциональных групп параметры подбора будет совсем другими
             */
            if ($group) {
                if ($group == $value['id']) {
                    if ( ! empty($filter)) {
                        $ids = $this->getProductsByFilter($group, $filter);
                        if (empty($ids)) {
                            $groups[$key]['count'] = 0;
                            continue;
                        } else {
                            $query = $query . " AND `b`.`id` IN (" . implode(',', $ids) . ")";
                        }
                    }
                }
            }
            $groups[$key]['count'] = $this->database->fetchOne(
                $query,
                array(
                    'maker' => $id,
                    'group' => $value['id']
                )
            );
        }

        return $groups;
    }

    /**
     * Возвращает массив параметров подбора для выбранной функциональной группы
     * и выбранного производителя $id; результат работы кэшируется
     */
    public function getMakerGroupParams($id, $group, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makerGroupParams($id, $group, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-filter-' . md5(serialize($filter));
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
    protected function makerGroupParams($id, $group, $hit, $new, $filter) {

        if (0 == $group) {
            return array();
        }

        /*
         * Получаем список всех параметров подбора для выбранной функциональной
         * группы и выбранного производителя
         */
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

        /*
         * Теперь подсчитываем количество товаров для каждого параметра и каждого значения
         * параметра с учетом фильтров по лидерам продаж, по новинкам и по параметрам
         */
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

            $temp = $filter;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByFilter($group, $temp);
                if ( ! empty($ids)) {
                    $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
                    $result[$key]['count'] = $this->database->fetchOne(
                        $query,
                        array(
                            'group' => $group,
                            'maker' => $id,
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
                        'group' => $group,
                        'maker' => $id,
                        'param_id' => $value['param_id'],
                        'value_id' => $value['value_id']
                    )
                );
            }
        }

        /*
         * Преобразуем массив результатов к виду, удобному для вывода в шаблоне
         */
        $filters = array();
        $param_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $filters[$counter] = array(
                    'id' => $value['param_id'],
                    'name' => $value['param_name'],
                    'selected' => isset($filter[$value['param_id']]),
                );
            }
            $filters[$counter]['values'][] = array(
                'id' => $value['value_id'],
                'name' => $value['value_name'],
                'count' => $value['count'],
                'selected' => in_array($value['value_id'], $filter),
            );
        }

        return $filters;

    }

    /**
     * Функция возвращает количество лидеров продаж для производителя с уникальным
     * идентификатором $id с учетом фильтров по функциональной группе, новинкам и
     * параметрам. Результат работы кэшируется
     */
    public function getCountMakerHit($id, $group, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countMakerHit($id, $group, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-filter-' . md5(serialize($filter));
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
    protected function countMakerHit($id, $group, $hit, $new, $filter) {

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
        if ( ! $hit) {
            /*
             * надо выяснить, сколько товаров будет найдено, если поставить
             * галочку «Лидер продаж»; на данный момент checkbox не отмечен, но
             * если пользователь его отметит — сколько будет найдено товаров?
             */
            $query = $query . " AND `a`.`hit` > 0";
        }
        if ($new) { // фильтр по новинкам
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
    public function getCountMakerNew($id, $group, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countMakerNew($id, $group, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
               . '-filter-' . md5(serialize($filter));
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
    protected function countMakerNew($id, $group, $hit, $new, $filter) {

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
            /*
             * надо выяснить, сколько товаров будет найдено, если поставить галочку
             * «Новинка»; на данный момент checkbox не отмечен, но если пользователь
             * его отметит — сколько будет найдено товаров?
             */
            $query = $query . " AND `a`.`new` > 0";
        }
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
    public function getMakerURL($id, $group, $hit, $new, $filter, $sort, $perpage) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->makerURL($id, $group, $hit, $new, $filter, $sort, $perpage);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
            . '-filter-' . md5(serialize($filter)) . '-sort-' . $sort . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает ЧПУ для страницы производителя с уникальным идентификатором
     * $id, с учетом всех фильтров и сортировки товаров производителя
     */
    protected function makerURL($id, $group, $hit, $new, $filter, $sort, $perpage) {

        $url = 'frontend/catalog/maker/id/' . $id;
        if ($group) { // включен фильтр по функциональной группе?
            $url = $url . '/group/' . $group;
        }
        if ($hit) { // включен фильтр по лидерам продаж?
            $url = $url . '/hit/1';
        }
        if ($new) { // включен фильтр по новинкам?
            $url = $url . '/new/1';
        }
        if ( ! empty($filter)) { // включены доп.фильтры (параметры подбора)?
            $temp = array();
            foreach ($filter as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/filter/' . implode('-', $temp);
        }
        if ($sort) { // включена сортировка ?
            $url = $url . '/sort/' . $sort;
        }
        if ($perpage) { // включен показ 20,50,100 товаров на страницу?
            $url = $url . '/perpage/' . $perpage;
        }
        return $this->getURL($url);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров производителя $id
     * по цене, наименованию, коду (артикулу); результат работы кэшируется
     */
    public function getMakerSortOrders($id, $group, $hit, $new, $filter, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->makerSortOrders($id, $group, $hit, $new, $filter, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
            . '-filter-' . md5(serialize($filter)) . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров производителя с
     * уникальным идентификатором $id по цене, наименованию, коду (артикулу)
     */
    protected function makerSortOrders($id, $group, $hit, $new, $filter, $perpage) {

        $url = 'frontend/catalog/maker/id/' . $id;
        if ($group) { // включен фильтр по функциональной группе?
            $url = $url . '/group/' . $group;
        }
        if ($hit) { // включен фильтр по лидерам продаж?
            $url = $url . '/hit/1';
        }
        if ($new) { // включен фильтр по новинкам?
            $url = $url . '/new/1';
        }
        if ( ! empty($filter)) { // включены доп.фильтры (параметры подбора)?
            $temp = array();
            foreach ($filter as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/filter/' . implode('-', $temp);
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
            if ($perpage) {
                $temp = $temp . '/perpage/' . $perpage;
            }
            $sortorders[$i] = array(
                'url'  => $this->getURL($temp),
                'name' => $name
            );
        }
        return $sortorders;

    }

    /**
     * Функция возвращает массив ссылок для переключения на показ 10,20,50,100
     * товаров производителя на страницу; результат работы кэшируется
     */
    public function getOthersPerPage($id, $group, $hit, $new, $filter, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->othersPerPage($id, $group, $hit, $new, $filter, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit . '-new-' . $new
                . '-filter-' . md5(serialize($filter)) . '-sort-' . $sort . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для переключения на показ 10,20,50,100
     * товаров производителя на страницу
     */
    protected function othersPerPage($id, $group, $hit, $new, $filter, $sort, $perpage) {

        $url = 'frontend/catalog/maker/id/' . $id;
        if ($group) { // включен фильтр по функциональной группе?
            $url = $url . '/group/' . $group;
        }
        if ($hit) { // включен фильтр по лидерам продаж?
            $url = $url . '/hit/1';
        }
        if ($new) { // включен фильтр по новинкам?
            $url = $url . '/new/1';
        }
        if ( ! empty($filter)) { // включены доп.фильтры (параметры подбора)?
            $temp = array();
            foreach ($filter as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/filter/' . implode('-', $temp);
        }
        if ($sort) { // включена сортировка?
            $url = $url . '/sort/' . $sort;
        }

        /*
         * $items = Array (
         *   [0] => Array (
         *     [url] => //www.host.ru/catalog/maker/42
         *     [name] => 10
         *     [current] => false
         *   )
         *   [1] => Array (
         *     [url] => //www.host.ru/catalog/maker/42/perpage/20
         *     [name] => 20
         *     [current] => true
         *   )
         *   [2] => Array (
         *      ..........
         *   )
         *   [3] => Array (
         *     [url] => //www.host.ru/catalog/maker/42/perpage/100
         *     [name] => 100
         *     [current] => false
         *   )
         * )
         */
        $items = array();
        $items[] = array( // товаров на странице по умолчанию
            'url'     => $this->getURL($url),
            'name'    => $this->config->pager->frontend->products->perpage,
            'current' => !$perpage
        );
        $others = $this->config->pager->frontend->products->others;
        foreach ($others as $variant) { // другие варианты кол-ва товаров на странице
            $temp = $url;
            $temp = $temp . '/perpage/' . $variant;
            $items[] = array(
                'url'     => $this->getURL($temp),
                'name'    => $variant,
                'current' => $variant === $perpage
            );
        }

        return $items;

    }

}
