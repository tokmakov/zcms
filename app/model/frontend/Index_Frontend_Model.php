<?php
/**
 * Класс Page_Frontend_Model для показа гланой страницы,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Index_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    public function getIndexPage() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->indexPage();
        }

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
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    protected function indexPage() {
        $query = "SELECT
                      `name`, `title`, `description`, `keywords`, `body`
                  FROM
                      `start`
                  WHERE
                      `id` = 1";
        return $this->database->fetch($query);
    }

    /**
     * Возвращает массив всех баннеров для главной (стартовой) страницы сайта
     */
    public function getAllBanners() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allBanners();
        }

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
     * Возвращает массив всех баннеров для главной (стартовой) страницы сайта
     */
    protected function allBanners() {
        $query = "SELECT
                      `id`, `name`, `url`, `alttext`
                  FROM
                      `slider`
                  WHERE
                      `visible` = 1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Возвращает массив трех последних событий отрасли; результат работы кэшируется
     */
    public function getGeneralNews() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->generalNews();
        }

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
     * Возвращает массив трех последних событий отрасли
     */
    protected function generalNews() {
        $query = "SELECT
                      `id`, `name`, `excerpt`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `news`
                  WHERE
                      `category` = 2
                  ORDER BY
                      `added` DESC
                  LIMIT
                      3";
        $news = $this->database->fetchAll($query);
        // добавляем в массив новостей информацию об URL новости, картинки
        foreach($news as $key => $value) {
            $news[$key]['url']['item'] = $this->getURL('frontend/news/item/id/' . $value['id']);
            if (is_file('./files/news/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/default.jpg';
            }
        }
        return $news;
    }

    /**
     * Возвращает массив трех последних новостей компании; результат работы кэшируется
     */
    public function getCompanyNews() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->companyNews();
        }

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
     * Возвращает массив трех последних новостей компании
     */
    protected function companyNews() {
        $query = "SELECT
                      `id`, `name`, `excerpt`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `news`
                  WHERE
                      `category` = 1
                  ORDER BY
                      `added` DESC
                  LIMIT
                      3";
        $news = $this->database->fetchAll($query);
        // добавляем в массив новостей информацию об URL новости, картинки
        foreach($news as $key => $value) {
            $news[$key]['url']['item'] = $this->getURL('frontend/news/item/id/' . $value['id']);
            if (is_file('./files/news/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/default.jpg';
            }
        }
        return $news;
    }

    /**
     * Возвращает массив лидеров продаж для главной (стартовой) страницы сайта;
     * результат работы кэшируется
     */
    public function getHitProducts() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->hitProducts();
        }

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
     * Возвращает массив лидеров продаж для главной (стартовой) страницы сайта
     */
    protected function hitProducts() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`price` AS `price`,`a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`, `a`.`image` AS `image`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`hit` = 2
                      AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`globalsort`, `a`.`sortorder`
                  LIMIT
                      15";
        $products = $this->database->fetchAll($query);

        // добавляем в массив товаров информацию об URL товаров, фото
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на фото товара
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
        }

        return $products;

    }

    /**
     * Возвращает массив новых товаров для главной (стартовой) страницы сайта;
     * результат работы кэшируется
     */
    public function getNewProducts() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->newProducts();
        }

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
     * Возвращает массив новых товаров для главной (стартовой) страницы сайта
     */
    protected function newProducts() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`price` AS `price`,`a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`, `a`.`image` AS `image`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`new` = 2
                      AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`globalsort`, `a`.`sortorder`
                  LIMIT
                      15";
        $products = $this->database->fetchAll($query);

        // добавляем в массив товаров информацию об URL товаров, фото
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на фото товара
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
        }

        return $products;

    }

}
