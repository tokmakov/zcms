<?php
/**
 * Класс Sale_Frontend_Model для работы с товарами по сниженным ценам,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Sale_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех товаров по сниженным ценам,
     * результат работы кэшируется
     */
    public function getAllProducts() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allProducts();
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
     * Возвращает массив всех товаров по сниженным ценам
     */
    protected function allProducts() {

        // получаем все товары и категории
        $query = "SELECT
                      `b`.`id` AS `id`, `b`.`code` AS `code`,
                      `b`.`name` AS `name`, `b`.`title` AS `title`,
                      `b`.`price1` AS `price1`, `b`.`price2` AS `price2`,
                      `b`.`unit` AS `unit`, `b`.`count` AS `count`,
                      `b`.`description` AS `description`,
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `a`.`sortorder` AS `ctg_sort`, `b`.`sortorder` AS `prd_sort`
                  FROM
                      `sale_categories` `a` INNER JOIN `sale_products` `b`
                      ON `a`.`id` = `b`.`category`
                  WHERE
                      1
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`";
        $result = $this->database->fetchAll($query);

        $products = array();
        $ctg_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($ctg_id != $value['ctg_id']) {
                $counter++;
                $ctg_id = $value['ctg_id'];
                $products[$counter] = array(
                    'category' => $value['ctg_name'],
                );
            }
            $image = null;
            if (is_file('files/sale/' . $value['id'] . '.jpg')) {
                $image = $this->config->site->url . 'files/sale/' . $value['id'] . '.jpg';
            }
            $products[$counter]['products'][] = array(
                'number'      => $value['prd_sort'],
                'code'        => $value['code'],
                'name'        => $value['name'],
                'title'       => $value['title'],
                'image'       => $image,
                'count'       => $value['count'],
                'description' => $value['description'],
                'price1'      => $value['price1'],
                'price2'      => $value['price2'],
                'unit'        => $value['unit'],
            );
        }

        return $products;

    }

}