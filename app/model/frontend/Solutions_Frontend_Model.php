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
    public function getAllSolutions() {
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
                      `a`.`sortorder`, `b`.`sortorder`";
        $solutions = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок для редактирования
        foreach ($solutions as $key => $value) {
            $solutions[$key]['url'] = array(
                'ctg' => $this->getURL('frontend/solutions/category/id/' . $value['ctg_id']),
                'item' => $this->getURL('frontend/solutions/item/id/' . $value['id']),
            );
        }

        return $solutions;
    }

    /**
     * Функция возвращает массив всех категорий типовых решений
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `solutions_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
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
    public function getCategorySolutions($id) {
        $query = "SELECT
                      `id`, `name`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $solutions = $this->database->fetchAll($query, array('category' => $id));
        // добавляем в массив URL ссылок на страницы типовых решений
        foreach($solutions  as $key => $value) {
            $solutions[$key]['url'] = $this->getURL('frontend/solutions/item/id/' . $value['id']);
        }
        return $solutions ;
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
                      `category`, `name`, `keywords`, `description`,
                      `excerpt`, `content1`, `content2`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id";
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
                    ON `a`.`code`=`b`.`code` AND `b`.`visible`=1
                WHERE
                    `a`.`parent` = :parent
                ORDER BY
                    `a`.`sortorder`";
        $products = $this->database->fetchAll($query, array('parent' => $id));
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products  as $key => $value) {
            $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
        }
        return $products;
    }

}
