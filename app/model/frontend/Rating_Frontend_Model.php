<?php
/**
 * Класс Rating_Backend_Model для работы с рейтингом продаж, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Rating_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех категорий и товаров рейтинга,
     * результат работы кэшируется
     */
    public function getRating() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->rating();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
     * Возвращает массив всех категорий и товаров рейтинга
     */
    protected function rating() {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`sortorder` AS `number`, `a`.`name` AS `root`
                  FROM
                      `rating_categories` `a`
                  WHERE
                      `parent` = 0 AND EXISTS
                      (
                           SELECT
                               1
                           FROM
                               `rating_categories` `b` INNER JOIN `rating_products` `c`
                               ON `b`.`id` = `c`.`category`
                           WHERE
                               `b`.`parent` = `a`.`id`
                      )
                  ORDER BY
                      `a`.`sortorder`";
        $rating = $this->database->fetchAll($query);

        foreach ($rating as $key => $value) {
            $rating[$key]['childs'] = $this->getRootCategory($value['id']);
        }

        return $rating;
    }

    /**
     * Возвращает массив дочерних категорий и товаров корневой категории $id
     */
    private function getRootCategory($id) {
        // получаем все товары и категории
        $query = "SELECT
                      `b`.`product_id` AS `id`, `b`.`code` AS `code`,
                      `b`.`name` AS `name`, `b`.`title` AS `title`,
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `a`.`sortorder` AS `ctg_sort`, `b`.`sortorder` AS `prd_sort`
                  FROM
                      `rating_categories` `a` INNER JOIN `rating_products` `b`
                      ON `a`.`id` = `b`.`category`
                  WHERE
                      `a`.`parent` = :parent
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));

        $root = array();
        $ctg_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($ctg_id != $value['ctg_id']) {
                $counter++;
                $ctg_id = $value['ctg_id'];
                $root[$counter] = array(
                    'number'   => $value['ctg_sort'],
                    'category' => $value['ctg_name'],
                );
            }
            $url = null;
            if ( ! empty($value['id'])) {
                $url = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            }
            $root[$counter]['products'][] = array(
                'number'  => $value['prd_sort'],
                'code'    => $value['code'],
                'name'    => $value['name'],
                'title'   => $value['title'],
                'url'     => $url,
            );
        }

        return $root;
    }

}