<?php
/**
 * Класс Compared_Frontend_Model отвечает за сравнение товаров,
 * реализует шаблон проектирования «Наблюдатель»
 */
class Compared_Frontend_Model extends Frontend_Model implements SplObserver {

    /**
     * хранит уникальный идентификатор посетителя сайта
     */
    private $visitorId;


    public function __construct() {

        parent::__construct();
        // уникальный идентификатор посетителя сайта
        if (!isset($this->userFrontendModel)) {
            // экземпляр класса модели для работы с пользователями
            $this->userFrontendModel =
                isset($this->register->userFrontendModel) ? $this->register->userFrontendModel : new User_Frontend_Model();
        }
        $this->visitorId = $this->userFrontendModel->getVisitorId();

    }

    /**
     * Функция добавляет товар в список отложенных
     */
    public function addToCompared($productId) {

        // такой товар уже есть в списке сравнения?
        $query = "SELECT 1
                  FROM `compared`
                  WHERE `visitor_id` = :visitor_id AND `product_id` = :product_id";
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        $res = $this->database->fetchOne($query, $data);
        if (false === $res) { // если товара еще нет в списке сравнения, добавляем его
            $query = "INSERT INTO `compared`
                      (
                          `visitor_id`,
                          `product_id`,
                          `added`
                      )
                      VALUES
                      (
                          :visitor_id,
                          :product_id,
                          NOW()
                      )";
        } else { // если товар уже в списке сравнения, обновляем дату
            $query = "UPDATE `compared`
                      SET `added` = NOW()
                      WHERE `visitor_id` = :visitor_id AND `product_id` = :product_id";
        }
        $this->database->execute($query, $data);

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }

    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для центральной колонки, полный вариант
     */
    public function getComparedProducts($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`techdata` AS `techdata`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`image` AS `image`,
                      `c`.`id` AS `ctg_id`,
                      `c`.`name` AS `ctg_name`,
                      `d`.`id` AS `mkr_id`,
                      `d`.`name` AS `mkr_name`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `products` `a`
                      INNER JOIN `compared` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем в массив товаров информацию об URL товаров, производителей, фото
        foreach($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL файла изображения товара
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // технические характеристики
            if (!empty($value['techdata'])) {
                $products[$key]['techdata'] = unserialize($value['techdata']);
            } else {
                $products[$key]['techdata'] = array();
            }
            // атрибут action тега form для добавления товара в корзину
            $products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
            // атрибут action тега form для добавления товара в список отложенных
            $products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
            // атрибут action тега form для удаления товара из списка сравнения
            $products[$key]['action']['compared'] = $this->getURL('frontend/compared/rmvprd');
        }
        return $products;
    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения; для правой
     * колонки, сокращенный вариант; результат работы кэшируется
     */
    public function getSideComparedProducts() {
        // если не включено кэширование данных
        if (!$this->enableDataCache) {
            return $this->sideComparedProducts();
        }

        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для правой колонки, сокращенный вариант
     */
    protected function sideComparedProducts() {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `products` `a`
                      INNER JOIN `compared` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT " . $this->config->pager->frontend->products->perpage;
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products as $key => $value) {
            $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
        }
        return $products;
    }

    /**
     * Функция возвращает кол-во товаров, отложенных посетителем для сравнения
     */
    public function getCountComparedProducts() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `compared` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        $res = $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));

        // удаляем старые товары
        if (rand(1, 100) == 50) {
            $this->removeOldCompared();
        }

        return $res;
    }

    /**
     * Функция возвращает кол-во страниц, которые нужны для показа всех товаров для сравнеия
     */
    public function getTotalPages() {
        return ceil($this->getCountComparedProducts() / $this->config->pager->frontend->products->perpage);
    }

    /**
     * Функция удаляет товар из списка отложенных для сравнения товаров
     */
    public function removeFromCompared($productId) {
        $query = "DELETE FROM `compared`
                  WHERE `product_id` = :product_id AND `visitor_id` = :visitor_id";
        $this->database->execute(
            $query,
            array(
                'product_id' => $productId,
                'visitor_id' => $this->visitorId
            )
        );
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }
    }

    /**
     * Функция удаляет все старые списки отложенных для сравнения товаров
     */
    public function removeOldCompared() {
        $query = "DELETE FROM `compared` WHERE `product_id` NOT IN (SELECT `id` FROM `products` WHERE 1)";
        $this->database->execute($query);

        $query = "DELETE FROM `compared` WHERE `added` < NOW() - INTERVAL :days DAY";
        $this->database->execute($query, array('days' => $this->config->user->cookie));
    }

    /**
     * Функция объединяет списки отложенных для сравнения товаров (ещё) не
     * авторизованного посетителя и (уже) авторизованного пользователя сразу
     * после авторизации, реализация шаблона проектирования «Наблюдатель»
     */
    public function update(SplSubject $userFrontendModel) {

        $newVisitorId = $userFrontendModel->getVisitorId();

        if ($newVisitorId == $this->visitorId) {
            return;
        }

        $query = "UPDATE `compared` SET `visitor_id` = :new_visitor_id WHERE `visitor_id` = :old_visitor_id";
        $this->database->execute(
            $query,
            array(
                'old_visitor_id' => $this->visitorId,
                'new_visitor_id' => $newVisitorId
            )
        );

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;

        // если среди отложенных для сравнения есть два одинаковых товара
        $query = "SELECT MAX(`id`) AS `id`, `product_id`, COUNT(*) AS `count`
                  FROM `compared`
                  WHERE `visitor_id` = :visitor_id
                  GROUP BY `product_id`
                  HAVING COUNT(*) > 1";
        $res = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        if (empty($res)) {
            return;
        }
        foreach ($res as $item) {
            $query = "DELETE FROM `compared`
                      WHERE `id` < :id AND `product_id` = :product_id AND `visitor_id` = :visitor_id";
            $this->database->execute(
                $query,
                array(
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'visitor_id' => $this->visitorId
                )
            );
        }

    }


    // Получаем рекомендации для пользователя на основе отложенных для сравнения товаров
    public function getRecommendations() {

    }
}
