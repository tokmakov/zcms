<?php
/**
 * Класс Solution_Frontend_Model для работы с типовыми решениями, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Solution_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех типовых решений во всех категориях,
     * результат работы кэшируется
     */
    public function getAllSolutions($start) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allSolutions($start);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив всех типовых решений во всех категориях
     */
    protected function allSolutions($start) {

        $query = "SELECT
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `b`.`id` AS `id`, `b`.`name` AS `name`,
                      `b`.`excerpt` AS `excerpt`, `b`.`sortorder` AS `sortorder`
                  FROM
                      `solutions_categories` `a`
                      INNER JOIN `solutions` `b` ON `a`.`id` = `b`.`category`
                  WHERE
                      1
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`
                  LIMIT " . $start . ", " . $this->config->pager->frontend->solution->perpage;
        $solutions = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок
        foreach ($solutions as $key => $value) {
            $solutions[$key]['url'] = array(
                'ctg'  => $this->getURL('frontend/solution/category/id/' . $value['ctg_id']),
                'item' => $this->getURL('frontend/solution/item/id/' . $value['id']),
            );
        }

        return $solutions;

    }

    /**
     * Возвращает общее количество типовых решений (во всех категориях),
     * результат работы кэшируется
     */
    public function getCountAllSolutions() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `solutions`
                  WHERE
                      1";
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Функция возвращает массив всех категорий типовых решений, результат
     * работы кэшируется
     */
    public function getCategories() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categories();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
     * Функция возвращает массив всех категорий типовых решений
     */
    protected function categories() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(`b`.`id`) AS `count`
                  FROM
                      `solutions_categories` `a`
                      LEFT JOIN `solutions` `b` ON `a`.`id` = `b`.`category`
                  WHERE
                      1
                  GROUP BY
                      1, 2
                  ORDER BY
                      `a`.`sortorder`";
        $categories = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок на станицы категорий
        foreach ($categories as $key => $value) {
            $categories[$key]['url'] = $this->getURL('frontend/solution/category/id/' . $value['id']);
        }

        return $categories;

    }

    /**
     * Функция возвращает массив всех типовых решений выбранной категории $id,
     * результат работы кэшируется
     */
    public function getCategorySolutions($id, $start = 0) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categorySolutions($id, $start);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив всех типовых решений выбранной категории $id
     */
    protected function categorySolutions($id, $start = 0) {

        $query = "SELECT
                      `id`, `name`, `excerpt`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`
                  LIMIT " . $start . ", " . $this->config->pager->frontend->solution->perpage;
        $solutions = $this->database->fetchAll($query, array('category' => $id));

        // добавляем в массив URL ссылок на страницы типовых решений
        foreach ($solutions  as $key => $value) {
            $solutions[$key]['url'] = $this->getURL('frontend/solution/item/id/' . $value['id']);
        }

        return $solutions;

    }

    /**
     * Возвращает количество типовых решений в категории с уникальным
     * идентификатором $id, результат работы кэшируется
     */
    public function getCountCategorySolutions($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `solutions`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id), $this->enableDataCache);
    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id,
     * результат работы кэшируется
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `keywords`, `description`, `excerpt`
                  FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id), $this->enableDataCache);
    }

    /**
     * Возвращает информацию о типовом решении с уникальным идентификатором $id,
     * результат работы кэшируется
     */
    public function getSolution($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->solution($id);
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
     * Возвращает информацию о типовом решении с уникальным идентификатором $id,
     * результат работы кэшируется
     */
    protected function solution($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`, `a`.`excerpt` AS `excerpt`,
                      `a`.`content1` AS `content1`, `a`.`content2` AS `content2`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `solutions` `a` INNER JOIN `solutions_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        $solution = $this->database->fetch($query, array('id' => $id), $this->enableDataCache);
        if (false === $solution) {
            return false;
        }
        // URL ссылок на файл PDF и изображение
        $solution['url'] = array(
            'pdf' => null,
            'img' => null
        );
        if (is_file('files/solution/' . $id . '.pdf')) {
            $solution['url']['pdf'] = $this->config->site->url . 'files/solution/' . $id . '.pdf';
        }
        if (is_file('files/solution/' . $id . '.jpg')) {
            $solution['url']['img'] = $this->config->site->url . 'files/solution/' . $id . '.jpg';
        }
        return $solution;
    }

    /**
     * Функция возвращает массив товаров типового решения с уникальным
     * идентификатором $id, результат работы кэшируется
     */
    public function getSolutionProducts($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->solutionProducts($id);
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
     * Функция возвращает массив товаров типового решения $id
     */
    protected function solutionProducts($id) {

        $query = "SELECT
                      `b`.`id` AS `id`,
                      `c`.`id` AS `group_id`,
                      `c`.`name` AS `group_name`,
                      `a`.`require` AS `require`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      CASE WHEN `b`.`price` IS NULL THEN `a`.`price` ELSE `b`.`price` END AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`count` AS `count`,
                      `a`.`changeable` AS `changeable`,
                      `a`.`sortorder` AS `sortorder`,
                      CASE WHEN `b`.`id` IS NULL THEN 1 ELSE 0 END AS `empty`
                FROM
                    `solutions_products` `a`
                    LEFT JOIN `products` `b` ON `a`.`product_id` = `b`.`id` AND `b`.`visible` = 1
                    INNER JOIN `solutions_groups` `c` ON `a`.`group` = `c`.`id`
                WHERE
                    `a`.`parent` = :parent
                ORDER BY
                    `c`.`sortorder`, `a`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));

        if (empty($result)) {
            return array();
        }

        $products = array();
        $group_id = 0;
        $counter = -1;
        $amount = 0;
        foreach ($result as $value) {
            if ($group_id != $value['group_id']) {
                $counter++;
                $group_id = $value['group_id'];
                $products[$counter] = array(
                    'name'   => $value['group_name'],
                    'amount' => $amount,
                );
                if ($counter) {
                    $products[$counter-1]['amount'] = $amount;
                }
                $amount = 0;
            }
            $cost = $value['price'] * $value['count'];
            $amount = $amount + $cost;
            $products[$counter]['products'][] = array(
                'id'         => $value['id'],
                'require'    => $value['require'],
                'code'       => $value['code'],
                'name'       => $value['name'],
                'title'      => $value['title'],
                'shortdescr' => $value['shortdescr'],
                'price'      => $value['price'],
                'unit'       => $value['unit'],
                'count'      => $value['count'],
                'cost'       => $cost,
                'changeable' => $value['changeable'],
                'sortorder'  => $value['sortorder'],
                'empty'      => $value['empty'],
                'url'        => $value['empty'] ? null : $this->getURL('frontend/catalog/product/id/' . $value['id'])
            );
        }
        $products[$counter]['amount'] = $amount;

        return $products;

    }

    /**
     * Функция добавляет в корзину все товары типового решения $id
     */
    public function addSolutionToBasket($id) {

        // получаем все товары типового решения
        $query = "SELECT
                      `a`.`product_id` AS `id`, `a`.`count` AS `count`
                  FROM
                      `solutions_products` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      `a`.`parent` = :parent AND `b`.`visible` = 1
                  ORDER BY
                      `a`.`group`, `a`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));

        // через реестр обращаемся к экземпляру класса Basket_Frontend_Model
        // и добавляем эти товары в корзину
        $basketFrontendModel = $this->register->basketFrontendModel;
        foreach ($result as $key => $value) {
            $basketFrontendModel->addToBasket($value['id'], $value['count'], $key);
        }

    }

}
