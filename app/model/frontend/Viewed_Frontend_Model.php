<?php
/**
 * Класс Viewed_Frontend_Model отвечает за список товаров, просмотренных
 * пользователем, взаимодействует с базой данных, реализует шаблон проектирования
 * «Наблюдатель», общедоступная часть сайта; см. описание интерфейса SplObserver
 * http://php.net/manual/ru/class.splobserver.php
 */
class Viewed_Frontend_Model extends Frontend_Model implements SplObserver {

    /**
     * уникальный идентификатор посетителя сайта, который сохраняется в cookie
     * и нужен для работы покупательской корзины, списка отложенных товаров,
     * списка товаров для сравнения и истории просмотренных товаров
     */
    private $visitorId;


    public function __construct() {

        parent::__construct();
        // уникальный идентификатор посетителя сайта
        if ( ! isset($this->register->userFrontendModel)) {
            // экземпляр класса модели для работы с пользователями
            new User_Frontend_Model();
        }
        $this->visitorId = $this->register->userFrontendModel->getVisitorId();

    }

    /**
     * Функция добавляет товар в список просмотренных товаров
     */
    public function addToViewed($productId) {

        // такой товар уже есть в списке просмотренных?
        $query = "SELECT
                      1
                  FROM
                      `viewed`
                  WHERE
                      `visitor_id` = :visitor_id AND `product_id` = :product_id";
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        $res = $this->database->fetchOne($query, $data);
        if (false === $res) { // если пользователь еще не просматривал товар, добавляем его в список
            $query = "INSERT INTO `viewed`
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
        } else { // если пользователь уже просматривал товар ранее, обновляем дату просмотра
            $query = "UPDATE
                          `viewed`
                      SET
                          `added` = NOW()
                      WHERE
                          `visitor_id` = :visitor_id AND `product_id` = :product_id";
        }
        $this->database->execute($query, $data);

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }

    }

    /**
     * Функция возвращает массив товаров, просмотренных посетителем;
     * для центральной колонки, полный вариант
     */
    public function getViewedProducts($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`price` AS `price`,
                      `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`,
                      `a`.`unit` AS `unit`,
                      `a`.`image` AS `image`,
                      `a`.`hit` AS `hit`,
                      `a`.`new` AS `new`,
                      `c`.`id` AS `ctg_id`,
                      `c`.`name` AS `ctg_name`,
                      `d`.`id` AS `mkr_id`,
                      `d`.`name` AS `mkr_name`,
                      `a`.`group` AS `grp_id`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `products` `a`
                      INNER JOIN `viewed` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT
                      :start, :limit";
        $products = $this->database->fetchAll(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                'start'      => $start,
                'limit'      => $this->config->pager->frontend->products->perpage,
            )
        );
        // добавляем в массив товаров информацию об URL товаров, производителей, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) {
            $host = $this->config->cdn->url;
        }
        foreach($products as $key => $value) {
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
            // атрибут action тега form для добавления товара в список сравнения
            $products[$key]['action']['compare'] = $this->getURL('frontend/compare/addprd');
            // атрибут action тега form для добавления товара в список отложенных
            $products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
        }

        // удаляем старые товары
        if (rand(1, 100) === 50) {
            $this->removeOldViewed();
        }

        return $products;
    }

    /**
     * Функция возвращает массив товаров, просмотренных посетителем; для правой
     * колонки, сокращенный вариант; результат работы кэшируется
     */
    public function getSideViewedProducts() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->sideViewedProducts();
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
     * Функция возвращает массив товаров, просмотренных посетителем;
     * для правой колонки, сокращенный вариант
     */
    protected function sideViewedProducts() {
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
                      INNER JOIN `viewed` `b` ON `a`.`id` = `b`.`product_id`
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
     * Функция возвращает кол-во товаров, просмотренных посетителем
     */
    public function getCountViewedProducts() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `viewed` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                     `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция удаляет все старые списки просмотренных товаров
     */
    public function removeOldViewed() {
        $query = "DELETE FROM
                      `viewed`
                  WHERE
                      `product_id` NOT IN (SELECT `id` FROM `products` WHERE 1)";
        $this->database->execute($query);

        $query = "DELETE FROM
                      `viewed`
                  WHERE
                      `added` < NOW() - INTERVAL :days DAY";
        $this->database->execute($query, array('days' => $this->config->user->cookie));
    }

    /**
     * Функция объединяет списки просмотренных товаров (ещё) не авторизованного
     * посетителя и (уже) авторизованного пользователя сразу после авторизации,
     * реализация шаблона проектирования «Наблюдатель»; см. описание интерфейса
     * SplObserver http://php.net/manual/ru/class.splobserver.php
     */
    public function update(SplSubject $userFrontendModel) {

        $newVisitorId = $userFrontendModel->getVisitorId();
        $oldVisitorId = $this->visitorId;

        if ($newVisitorId == $oldVisitorId) {
            return;
        }

        $query = "UPDATE
                      `viewed`
                  SET
                      `visitor_id` = :new_visitor_id
                  WHERE
                      `visitor_id` = :old_visitor_id";
        $this->database->execute(
            $query,
            array(
                'old_visitor_id' => $oldVisitorId,
                'new_visitor_id' => $newVisitorId
            )
        );

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            // кэш (ещё) не авторизованного посетителя
            $key = __CLASS__ . '-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            // кэш (уже) авторизованного пользователя
            $key = __CLASS__ . '-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;

        // если среди просмотренных есть два одинаковых товара
        $query = "SELECT
                      MAX(`id`) AS `id`, `product_id`, COUNT(*) AS `count`
                  FROM
                      `viewed`
                  WHERE
                      `visitor_id` = :visitor_id
                  GROUP BY
                      `product_id`
                  HAVING
                      COUNT(*) > 1";
        $res = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        if (empty($res)) {
            return;
        }
        foreach ($res as $item) {
            $query = "DELETE FROM
                          `viewed`
                      WHERE
                          `id` < :id AND `product_id` = :product_id AND `visitor_id` = :visitor_id";
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

    // Получаем рекомендации для пользователя на основе просмотренных товаров
    public function getRecommendations() {

    }
}
