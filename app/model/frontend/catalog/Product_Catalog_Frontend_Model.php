<?php
/**
 * Класс Product_Catalog_Frontend_Model для работы с товарами каталога,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Product_Catalog_Frontend_Model extends Catalog_Frontend_Model {

    /*
     * public function getProduct(...)
     * protected function product(...)
     * public function getLikedProducts(...)
     * protected function likedProducts(...)
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getProduct($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->product($id);
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
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    protected function product($id) {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`, `a`.`price` AS `price`,
                      `a`.`price2` AS `price2`, `a`.`price3` AS `price3`, `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`, `a`.`new` AS `new`, `a`.`hit` AS `hit`,
                      `a`.`image` AS `img`, `a`.`purpose` AS `purpose`,
                      `a`.`techdata` AS `techdata`, `a`.`features` AS `features`,
                      `a`.`complect` AS `complect`, `a`.`equipment` AS `equipment`,
                      `a`.`padding` AS `padding`, `a`.`category2` AS `second`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`,
                      `d`.`id` AS `grp_id`, `d`.`name` AS `grp_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                  WHERE
                      `a`.`id` = :id AND `a`.`visible` = 1";
        $product = $this->database->fetch($query, array('id' => $id));
        if (false === $product) {
            return null;
        }

        // технические характеристики
        if ( ! empty($product['techdata'])) {
            $product['techdata'] = unserialize($product['techdata']);
        } else {
            $product['techdata'] = array();
        }

        /*
         * добавляем информацию о файлах изображений
         */
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network, см. app/config/cdn.php
            $host = $this->config->cdn->url;
        }
        if ((!empty($product['img'])) && is_file('files/catalog/imgs/medium/' . $product['img'])) {
            $product['image']['medium'] = $host . 'files/catalog/imgs/medium/' . $product['img'];
        } else {
            $product['image']['medium'] = $host . 'files/catalog/imgs/medium/nophoto.jpg';
        }
        if ((!empty($product['img'])) && is_file('files/catalog/imgs/big/' . $product['img'])) {
            $product['image']['big'] = $host . 'files/catalog/imgs/big/' . $product['img'];
        } else {
            $product['image']['big'] = $host . 'files/catalog/imgs/big/nophoto.jpg';
        }

        /*
         * добавляем информацию о файлах документации
         */
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
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->doc) { // Content Delivery Network, см. app/config/cdn.php
            $host = $this->config->cdn->url;
        }
        foreach ($product['docs'] as $key => $value) {
            $product['docs'][$key]['url'] = $host . 'files/catalog/docs/' . $value['file'];
        }

        /*
         * добавляем информацию о сертификатах
         */
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
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->cert) { // Content Delivery Network, см. app/config/cdn.php
            $host = $this->config->cdn->url;
        }
        foreach ($temp as $key => $value) {
            if ( ! is_file('files/catalog/cert/' . $value['file'])) {
                continue;
            }
            $certs[$key]['title'] = $value['title'];
            $certs[$key]['count'] = $value['count'];
            $certs[$key]['files'][] = array(
                'name' => $value['file'],
                'url'  => $host . 'files/catalog/cert/' . $value['file']
            );
            if ($value['count'] > 1) {
                $page = 1;
                while ($page < $value['count']) {
                    $file = str_replace('.jpg', $page.'.jpg', $value['file']);
                    if (is_file('files/catalog/cert/' . $file)) {
                        $certs[$key]['files'][] = array(
                            'name' => $file,
                            'url'  => $host . 'files/catalog/cert/' . $file
                        );
                    }
                    $page++;
                }
            }
        }
        $product['certs'] = $certs;

        return $product;

    }

    /**
     * Функция возвращает массив товаров похожих товаров для товара с уникальным
     * идентификатором $id; результат работы кэшируется
     */
    public function getLikedProducts($id, $group, $category, $title) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запросов к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->likedProducts($id, $group, $category, $title);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будут выполнены запросы к базе данных
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
     * Функция возвращает массив товаров похожих товаров для товара с уникальным
     * идентификатором $id
     */
    protected function likedProducts($id, $group, $category, $title) {

        $query = "SELECT
                      `id`, `title`
                  FROM
                      `products`
                  WHERE
                      `group` = :group";
        $temp = $this->database->fetchAll($query . " ORDER BY `id`", array('group' => $group));
        $tmp = array();
        foreach ($temp as $item) {
            similar_text($title, $item['title'], $percent);
            if ($percent > 90) {
                $tmp[] = $item;
            }
        }
        $result = $tmp;
        if (count($result) > 10) {
            $query = $query . " AND `category` = :category";
            $temp = $this->database->fetchAll($query . " ORDER BY `id`", array('group' => $group, 'category' => $category));
            $tmp = array();
            foreach ($temp as $item) {
                similar_text($title, $item['title'], $percent);
                if ($percent > 90) {
                    $tmp[] = $item;
                }
            }
            if (count($tmp) > 4) {
                $result = $tmp;
            }
        }

        while (count($result) > 9) {
            array_pop($result);
        }

        $ids = array();
        foreach ($result as $item) {
            $ids[] = $item['id'];
        }

        $ids = implode(',', $ids);

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
                      `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`id` IN (".$ids.") AND `a`.`id` <> :product_id AND `a`.`visible` = 1
                  ORDER BY
                      `a`.`price` DESC";
        $result = $this->database->fetchAll($query, array('product_id' => $id));

        $count = count($result);

        if ($count == 0) {
            return array();
        }

        if ($count > 8) { // четыре самых дорогих и четыре самых дешевых
            $products = array_merge(array_slice($result, 0, 4), array_slice($result, -4));
        } else {
            $products = $result;
        }

        // добавляем в массив товаров информацию об URL товаров, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network, см. app/config/cdn.php
            $host = $this->config->cdn->url;
        }
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL ссылки на фото товара
            if (( ! empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // атрибут action тега form для добавления товара в корзину
            $products[$key]['action'] = $this->getURL('frontend/basket/addprd');
        }

        return $products;

    }

}
