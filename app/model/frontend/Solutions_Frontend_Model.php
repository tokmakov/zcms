<?php
/**
 * Класс Solutions_Frontend_Model для работы с типовыми решениями,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Solutions_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех типовых решений во всех категориях
     */
    public function getAllSolutions($start) {
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
                  LIMIT " . $start . ", " . $this->config->pager->frontend->solutions->perpage;
        $solutions = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок
        foreach ($solutions as $key => $value) {
            $solutions[$key]['url'] = array(
                'ctg' => $this->getURL('frontend/solutions/category/id/' . $value['ctg_id']),
                'item' => $this->getURL('frontend/solutions/item/id/' . $value['id']),
            );
        }

        return $solutions;
    }

    /**
     * Возвращает общее количество типовых решений (во всех категориях)
     */
    public function getCountAllSolutions() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `solutions`
                  WHERE
                      1";
        return $this->database->fetchOne($query);
    }

    /**
     * Функция возвращает массив всех категорий типовых решений
     */
    public function getCategories() {
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
            $categories[$key]['url'] = $this->getURL('frontend/solutions/category/id/' . $value['id']);
        }
        return $categories;
    }

    /**
     * Функция возвращает массив всех типовых решений выбранной категории $id
     */
    public function getCategorySolutions($id, $start) {
        $query = "SELECT
                      `id`, `name`, `excerpt`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`
                  LIMIT " . $start . ", " . $this->config->pager->frontend->solutions->perpage;
        $solutions = $this->database->fetchAll($query, array('category' => $id));
        // добавляем в массив URL ссылок на страницы типовых решений
        foreach($solutions  as $key => $value) {
            $solutions[$key]['url'] = $this->getURL('frontend/solutions/item/id/' . $value['id']);
        }
        return $solutions ;
    }

    /**
     * Возвращает количество типовых решений в категории с уникальным
     * идентификатором $id
     */
    public function getCountCategorySolutions($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `solutions`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `keywords`, `description`, `excerpt`
                  FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о типовом решении с уникальным идентификатором $id
     */
    public function getSolution($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`, `a`.`description` AS `description`,
                      `a`.`excerpt` AS `excerpt`, `a`.`content1` AS `content1`, `a`.`content2` AS `content2`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `solutions` `a` INNER JOIN `solutions_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция возвращает массив товаров типового решения $id
     */
    public function getSolutionProducts($id) {
        $query = "SELECT
                      `b`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`price` AS `price`,
                      CASE WHEN `b`.`price` IS NULL THEN `a`.`price` ELSE `b`.`price` END AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`count` AS `count`,
                      `a`.`heading` AS `heading`,
                      `a`.`note` AS `note`,
                      `a`.`sortorder` AS `sortorder`,
                      CASE WHEN `b`.`id` IS NULL THEN 1 ELSE 0 END AS `empty`
                FROM
                    `solutions_products` `a` LEFT JOIN `products` `b`
                    ON `a`.`product_id`=`b`.`id` AND `b`.`visible`=1
                WHERE
                    `a`.`parent` = :parent
                ORDER BY
                    `a`.`sortorder`";
        $products = $this->database->fetchAll($query, array('parent' => $id));
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products  as $key => $value) {
            if ( ! $products[$key]['empty']) {
                $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            }
        }
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
                      `a`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));

        // через реестр обращаемся к экземпляру класса Basket_Frontend_Model
        // и добавляем эти товары в корзину
        $basketFrontendModel = $this->register->basketFrontendModel;
        foreach ($result as $key => $value) {
            $basketFrontendModel->addToBasket($value['id'], $value['count'], $key);
        }

    }

}
