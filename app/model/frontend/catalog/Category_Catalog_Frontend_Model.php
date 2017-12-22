<?php
/**
 * Класс Category_Catalog_Frontend_Model для работы с категориями каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Category_Catalog_Frontend_Model extends Catalog_Frontend_Model {

    /*
     * public function getCategory(...)
     * public function getChildCategories(...)
     * protected function childCategories(...)
     * public function getCategoryProducts(...)
     * protected function categoryProducts(...)
     * public function getCountCategoryProducts(...)
     * protected function countCategoryProducts(...)
     * public function getCategoryMakers(...)
     * protected function categoryMakers(...)
     * public function getCategoryGroups(...)
     * protected function categoryGroups(...)
     * public function getCategoryGroupParams(...)
     * protected function categoryGroupParams(...)
     * public function getCountCategoryHit(...)
     * protected function countCategoryHit(...)
     * public function getCountCategoryNew(...)
     * protected function countCategoryNew(...)
     * public function getCategoryURL(...)
     * protected function categoryURL(...)
     * public function getCategorySortOrders(...)
     * protected function categorySortOrders(...)
     * public function getOthersPerPage(...)
     * protected function othersPerPage
     */

    public function __construct() {
        parent::__construct();
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
    public function getChildCategories($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->childCategories($id, $group, $maker, $hit, $new, $filter, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
            . '-hit-' . $hit. '-new-' . $new . '-filter-' . md5(serialize($filter))
            . '-sort-' . $sort . '-perpage-' . $perpage;
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
    protected function childCategories($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

        // TODO: избавиться от запроса в цикле

        /*
         * получаем список дочерних категорий
         */
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  ORDER BY
                      `sortorder`";
        $childCategories = $this->database->fetchAll($query, array('id' => $id));

        /*
         * для каждой дочерней категории получаем количество товаров в ней и в ее
         * потомках с учетом фильтров по функциональной группе, производителю, по
         * по лидерам продаж, по новинкам, параметрам подбора
         */
        foreach ($childCategories as $key => $value) {
            // получаем массив идентификаторов всех потомков текущей
            // категории, т.е. дочерние, дочерние дочерних и так далее
            $childs = $this->getAllChildIds($value['id']);
            // добавляем в этот массив идентификатор текущей категории
            $childs[] = $value['id'];
            // преобразуем массив в строку, чтобы составить условие SQL
            // запроса вида WHERE `category` IN (78, 56, 34, 12), где
            // 12 — идентификатор текущей категории, а 78, 56, 34 —
            // идетификаторы ее потомков
            $childs = implode(',', $childs);
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `products` `a`
                          INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                          INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                          INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
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
            if ( ! empty($filter)) { // фильтр по параметрам подбора
                /*
                 * получаем массив идентификаторов товаров, которые входят в функциональную
                 * группу $group и  подходят под параметры подбора $param
                 */
                $ids = $this->getProductsByFilter($group, $filter);
                if ( ! empty($ids)) { // если такие товары существуют, добавляем условие
                    $query = $query . " AND `a`.`id` IN (" . implode(',', $ids) . ")";
                    $childCategories[$key]['count'] = $this->database->fetchOne($query);
                } else { // таких товаров вообще нет
                    $childCategories[$key]['count'] = 0;
                }
            } else {
                $childCategories[$key]['count'] = $this->database->fetchOne($query);
            }

            /*
             * Добавляем в массив информацию об URL дочерних категорий; в общем виде URL имеет
             * вид frontend/catalog/category/id/12/group/34/maker/56/hit/1/param/123.234-345.456,
             * где
             * 12 — уникальный идентификатор текущей дочерней категории
             * 34 — уникальный идентификатор функциональной группы
             * 56 — уникальный идентификатор производителя
             * 123 — уникальный идентификатор параметра подбора, например, «Напряжение питания»
             * 234 — уникальный идентификатор значения параметра подбора, например, «12 Вольт»
             * 345 — уникальный идентификатор параметра подбора, например, «Способ установки»
             * 456 — уникальный идентификатор значения параметра подбора, например, «врезной»
             *
             * При переходе по ссылкам на дочерние категории, сохраняются все фильтры, которые
             * пользователь применил для категории каталога с идентификатором $id: фильтр по
             * функциональной группе, фильтр по производителю, фильтр по лидерам продаж и новикам,
             * фильтр по параметром подбора для выбранной функциональной группы.
             */
            $url = 'frontend/catalog/category/id/' . $value['id'];
            if ($group) { // фильтр по функционалу
                $url = $url . '/group/' . $group;
            } else {
                /*
                 * сразу включаем фильтр по функционалу, если у текущей дочерней категории все
                 * товары принадлежат одной функциональной группе, чтобы при переходе в эту
                 * категорию сразу стали доступны параметры подбора (без выбора единственной
                 * функциональной группы из выпадающего списка)
                 */
                $only = $this->getIsOnlyCategoryGroup($value['id']);
                if ($only) {
                    $url = $url . '/group/' . $only;
                }
            }
            if ($maker) { // фильтр по производителю
                $url = $url . '/maker/' . $maker;
            }
            if ($hit) { // фильтр по лидерам продаж
                $url = $url . '/hit/1';
            }
            if ($new) { // фильтр по новинкам
                $url = $url . '/new/1';
            }
            if ( ! empty($filter)) { // доп.фильтры (параметры подбора)
                $temp = array();
                foreach ($filter as $k => $v) {
                    $temp[] = $k . '.' . $v;
                }
                $url = $url . '/filter/' . implode('-', $temp);
            }
            if ($sort) { // сортировка
                $url = $url . '/sort/' . $sort;
            }
            if ($perpage) { // товаров на страницу
                $url = $url . '/perpage/' . $perpage;
            }
            $childCategories[$key]['url'] = $this->getURL($url);
        }

        return $childCategories;

    }

    /**
     * Возвращает массив товаров категории $id и ее потомков, т.е. не только товары
     * этой категории, но и товары дочерних категорий, товары дочерних-дочерних
     * категорий и так далее; результат работы кэшируется
     */
    public function getCategoryProducts($id, $group, $maker, $hit, $new, $filter, $sort, $start, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryProducts($id, $group, $maker, $hit, $new, $filter, $sort, $start, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
            . '-hit-' . $hit. '-new-' . $new . '-filter-' . md5(serialize($filter))
            . '-sort-' . $sort . '-start-' . $start . '-perpage-' . $perpage;
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
    protected function categoryProducts($id, $group, $maker, $hit, $new, $filter, $sort, $start, $perpage) {

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
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
                      `d`.`id` AS `grp_id`, `d`.`name` AS `grp_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                  WHERE
                      (`a`.`category` IN (" . $childs . ") OR `a`.`category2` IN (" . $childs . "))" . $tmp . "
                      AND `a`.`visible` = 1
                  ORDER BY
                      " . $order . "
                  LIMIT
                      :start, :limit";
        $limit = $perpage ? $perpage : $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll(
            $query,
            array(
                'start' => $start,
                'limit' => $limit
            )
        );

        // добавляем в массив товаров информацию об URL товаров, производителей, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network
            $host = $this->config->cdn->url;
        }
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL ссылки на фото товара
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
     * Возвращает количество товаров в категории $id и в ее потомках, т.е.
     * суммарное кол-во товаров не только в категории $id, но и в дочерних
     * категориях и в дочерних-дочерних категориях и так далее; результат
     * работы кэшируется
     */
    public function getCountCategoryProducts($id, $group, $maker, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countCategoryProducts($id, $group, $maker, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' .$group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-filter-' . md5(serialize($filter));
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
     * категориях и в дочерних-дочерних категориях и так далее
     */
    protected function countCategoryProducts($id, $group, $maker, $hit, $new, $filter) {

        // получаем массив идентификаторов всех потомков категории
        $childs = $this->getAllChildIds($id);
        // добавляем в массив идентификатор этой категории
        $childs[] = $id;
        // преобразуем массив в строку 123,456,789 — чтобы написать
        // условие SQL-запроса WHERE `id` IN (123,456,789)
        $childs = implode(',', $childs);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
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
        if ( ! empty($filter)) { // фильтр по параметрам подбора
            $ids = $this->getProductsByFilter($group, $filter);
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
    public function getCategoryMakers($id, $group, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запросов к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryMakers($id, $group, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнены запросы к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-hit-' . $hit
               . '-new-' . $new . '-filter-' . md5(serialize($filter));
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
    protected function categoryMakers($id, $group, $hit, $new, $filter) {

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
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
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

        /*
         * теперь подсчитываем количество товаров для каждого производителя с учетом
         * фильтров по функциональной группе, лидерам продаж, новинкам и по параметрам
         */
        foreach ($makers as $key => $value) {
            $query = "SELECT
                          COUNT(*)
                      FROM
                          `makers` `a`
                          INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                          INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                          INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
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
            if ( ! empty($filter)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByFilter($group, $filter);
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
    public function getCategoryGroups($id, $group, $maker, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryGroups($id, $group, $maker, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker . '-hit-' . $hit
               . '-new-' . $new . '-filter-' . md5(serialize($filter));
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
    protected function categoryGroups($id, $group, $maker, $hit, $new, $filter) {

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
                      `a`.`name`, COUNT(*) DESC";
        $groups = $this->database->fetchAll($query);

        /*
         * Небольшой трюк, чтобы визуально представить список функциональных групп более наглядно:
         * фактически, в виде двух списков. Первый список — функциональные группы, содержащие более
         * одного товара, второй список — функциональные группы, содержащие только один товар.
         * Разделение на два списка происходит, только если функциональных групп больше 15 шт.
         */
        if (count($groups) > 15) {
            $bound = false;
            foreach ($groups as $value)  {
                if ($value['count'] > 1) {
                    $bound = true;
                    break;
                }
            }
            if ($bound) {
                $first = array();  // функциональные группы категории, содержащие более одного товара
                $second = array(); // функциональные группы категории, содержащие только один товар
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

        if (0 == $group && 0 == $maker && 0 == $hit && 0 == $new) {
            return $groups;
        }

        if ($group && empty($filter) && 0 == $maker && 0 == $hit && 0 == $new) {
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
            $groups[$key]['count'] = $this->database->fetchOne($query);

        }
        return $groups;

    }

    /**
     * Возвращает массив параметров подбора для выбранной категории $id и всех ее потомков
     * и выбранной функциональной группы; результат работы кэшируется
     */
    public function getCategoryGroupParams($id, $group, $maker, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryGroupParams($id, $group, $maker, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-filter-' . md5(serialize($filter));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Возвращает массив параметров подбора для выбранной категории $id и всех ее потомков
     * и выбранной функциональной группы
     */
    protected function categoryGroupParams($id, $group, $maker, $hit, $new, $filter) {

        if (0 == $group) {
            return array();
        }

        /*
         * Получаем список всех параметров подбора для выбранной функциональной
         * группы и выбранной категории и всех ее потомков
         */
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

        /*
         * Теперь подсчитываем количество товаров для каждого параметра и каждого
         * значения параметра с учетом фильтров по производителю, лидерам продаж,
         * новинкам и по параметрам. В результате получим такой массив
         *
         * $result = Array(
         *   [0] => Array (
         *     [param_id] => 183
         *     [param_name] => Напряжение питания, В
         *     [value_id] => 1670
         *     [value_name] => переменное 220
         *     [count] => 5
         *   )
         *   [1] => Array(
         *     [param_id] => 183
         *     [param_name] => Напряжение питания, В
         *     [value_id] => 2011
         *     [value_name] => постоянное 12-24
         *     [count] => 7
         *   )
         *   [2] => Array(
         *     [param_id] => 183
         *     [param_name] => Напряжение питания, В
         *     [value_id] => 97
         *     [value_name] => батарейка Крона
         *   )
         *   [3] => Array(
         *     [param_id] => 34
         *     [param_name] => Регистрируемый газ
         *     [value_id] => 95
         *     [value_name] => Комбинированный
         *     [count] => 1
         *   )
         *   [4] => Array(
         *     [param_id] => 34
         *     [param_name] => Регистрируемый газ
         *     [value_id] => 94
         *     [value_name] => Природный газ
         *     [count] => 1
         *   )
         *   [5] => Array(
         *     [param_id] => 34
         *     [param_name] => Регистрируемый газ
         *     [value_id] => 93
         *     [value_name] => Угарный газ
         *     [count] => 1
         *   )
         * )
         */
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

            $temp = $filter;
            if (( ! empty($temp)) && isset($temp[$value['param_id']])) {
                unset($temp[$value['param_id']]);
            }
            if ( ! empty($temp)) { // фильтр по параметрам подбора
                $ids = $this->getProductsByFilter($group, $temp);
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

        /*
         * Приводим полученные данные к такому виду, чтобы с ними было удобно
         * работать в шаблоне
         *
         * $params = Array(
         *   [0] => Array(
         *     [id] => 183
         *     [name] => Напряжение питания, В
         *     [selected] => true
         *     [values] => Array(
         *       [0] => Array (
         *         [id] => 1670
         *         [name] => переменное 220
         *         [count] => 5
         *         [selected] => true
         *       )
         *       [1] => Array(
         *         [id] => 2011
         *         [name] => постоянное 12-24
         *         [count] => 7
         *         [selected] => false
         *       )
         *       [2] => Array(
         *         [id] => 97
         *         [name] => батарейка Крона
         *         [count] => 0
         *         [selected] => false
         *       )
         *     )
         *   )
         *   [1] => Array(
         *     [id] => 34
         *     [name] => Регистрируемый газ
         *     [selected] => false
         *     [values] => Array(
         *       [0] => Array(
         *         [id] => 95
         *         [name] => Комбинированный
         *         [count] => 1
         *         [selected] => false
         *       )
         *       [1] => Array(
         *         [id] => 94
         *         [name] => Природный газ
         *         [count] => 1
         *         [selected] => false
         *       )
         *       [2] => Array(
         *         [id] => 93
         *         [name] => Угарный газ
         *         [count] => 2
         *         [selected] => false
         *       )
         *     )
         *   )
         * )
         */
        $filters = array();
        $param_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $filters[$counter] = array(
                    'id'       => $value['param_id'],
                    'name'     => $value['param_name'],
                    'selected' => isset($filter[$value['param_id']]),
                );
            }
            $filters[$counter]['values'][] = array(
                'id'       => $value['value_id'],
                'name'     => $value['value_name'],
                'count'    => $value['count'],
                'selected' => in_array($value['value_id'], $filter)
            );
        }

        return $filters;

    }

    /**
     * Функция возвращает количество лидеров продаж в категории $id и ее потомках,
     * с учетом фильтров по функциональной группе, производителю и т.п. Результат
     * работы кэшируется
     */
    public function getCountCategoryHit($id, $group, $maker, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countCategoryHit($id, $group, $maker, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-filter-' . md5(serialize($filter));
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
    protected function countCategoryHit($id, $group, $maker, $hit, $new, $filter) {

        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
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
            /*
             * надо выяснить, сколько товаров будет найдено, если отметить
             * галочку «Лидер продаж»; на данный момент checkbox не отмечен,
             * но если пользователь его отметит — сколько будет найдено товаров?
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
        return $this->database->fetchOne($query);

    }

    /**
     * Функция возвращает количество новинок в категории $id и ее потомках, с
     * учетом фильтров по функциональной группе, производителю и т.п. Результат
     * работы кэшируется
     */
    public function getCountCategoryNew($id, $group, $maker, $hit, $new, $filter) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countCategoryNew($id, $group, $maker, $hit, $new, $filter);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
               . '-hit-' . $hit . '-new-' . $new . '-filter-' . md5(serialize($filter));
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
    protected function countCategoryNew($id, $group, $maker, $hit, $new, $filter) {

        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $childs = implode(',', $childs);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
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
            /*
             * надо выяснить, сколько товаров будет найдено, если отметить
             * галочку «Новинка»; на данный момент checkbox не отмечен, но
             * если пользователь его отметит — сколько будет найдено товаров?
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

        return $this->database->fetchOne($query);

    }

    /**
     * Функция возвращает ЧПУ для категории с уникальным идентификатором $id с учетом
     * фильтров и сортировки товаров; результат работы кэшируется
     */
    public function getCategoryURL($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categoryURL($id, $group, $maker, $hit, $new, $filter, $sort, $perpage);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker.
            '-hit-' . $hit. '-new-' . $new . '-filter-' . md5(serialize($filter))
            . '-sort-' . $sort . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает URL страницы категории с уникальным идентификатором $id с учетом
     * фильтров и сортировки товаров
     */
    protected function categoryURL($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

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
        if ( ! empty($filter)) {
            $temp = array();
            foreach ($filter as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/filter/' . implode('-', $temp);
        }
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        if ($perpage) {
            $url = $url . '/perpage/' . $perpage;
        }

        return $this->getURL($url);

    }

    /**
     * Функция возвращает массив ссылок для сортировки товаров категории $id по цене,
     * наименованию, коду (артикулу); результат работы кэшируется
     */
    public function getCategorySortOrders($id, $group, $maker, $hit, $new, $filter, $perpage) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categorySortOrders($id, $group, $maker, $hit, $new, $filter, $perpage);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
            . '-hit-' . $hit . '-new-' . $new . '-filter-' . md5(serialize($filter))
            . '-perpage-' . $perpage;
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
    protected function categorySortOrders($id, $group, $maker, $hit, $new, $filter, $perpage) {

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
        if ( ! empty($filter)) {
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
     * товаров категории на страницу; результат работы кэшируется
     */
    public function getOthersPerPage($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->othersPerPage($id, $group, $maker, $hit, $new, $filter, $sort, $perpage);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-group-' . $group . '-maker-' . $maker
            . '-hit-' . $hit . '-new-' . $new. '-filter-' . md5(serialize($filter))
            . '-sort-' . $sort . '-perpage-' . $perpage;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив ссылок для переключения на показ 10,20,50,100
     * товаров функциональной группы на страницу
     */
    protected function othersPerPage($id, $group, $maker, $hit, $new, $filter, $sort, $perpage) {

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
        if ( ! empty($filter)) {
            $temp = array();
            foreach ($filter as $key => $value) {
                $temp[] = $key . '.' . $value;
            }
            $url = $url . '/filter/' . implode('-', $temp);
        }
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }

        /*
         * $items = Array (
         *   [0] => Array (
         *     [url] => //www.host.ru/catalog/category/98
         *     [name] => 10
         *     [current] => false
         *   )
         *   [1] => Array (
         *     [url] => //www.host.ru/catalog/category/98/perpage/20
         *     [name] => 20
         *     [current] => true
         *   )
         *   [2] => Array (..........)
         *   [3] => Array (
         *     [url] => //www.host.ru/catalog/group/98/perpage/100
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
