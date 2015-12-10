<?php
/**
 * Класс Compare_Frontend_Model отвечает за сравнение товаров,
 * реализует шаблон проектирования «Наблюдатель»
 */
class Compare_Frontend_Model extends Frontend_Model implements SplObserver {

    /**
     * хранит уникальный идентификатор посетителя сайта
     */
    private $visitorId;

    /**
     * хранит идентификатор функциональной группы товаров,
     * которые в настоящий момент добавлены к сравнению
     */
    private $groupId = 0;


    public function __construct() {

        parent::__construct();
        // уникальный идентификатор посетителя сайта
        if ( ! isset($this->register->userFrontendModel)) {
            // экземпляр класса модели для работы с пользователями
            new User_Frontend_Model();
        }
        $this->visitorId = $this->register->userFrontendModel->getVisitorId();
        // идентификатор функциональной группы товаров для сравнения
        $this->groupId = $this->getCompareGroup();
        $time = 86400 * $this->config->user->cookie;
        setcookie('compare_group', $this->groupId, time() + $time, '/');

    }

    /**
     * Функция добавляет товар в список сравнения
     */
    public function addToCompare($productId) {

        // к сравению может быть добавлен только товар, принадлежащий
        // той же функциональной группе, что и другие товары
        if ($this->groupId && $this->groupId !== $this->getProductGroup($productId)) {
            return false;
        }

        // такой товар уже есть в списке сравнения?
        $query = "SELECT
                      1
                  FROM
                      `compare`
                  WHERE
                      `visitor_id` = :visitor_id AND `product_id` = :product_id";
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        $res = $this->database->fetchOne($query, $data);
        if (false === $res) { // если товара еще нет в списке сравнения, добавляем его
            $query = "INSERT INTO `compare`
                      (
                          `visitor_id`,
                          `product_id`,
                          `active`,
                          `added`
                      )
                      VALUES
                      (
                          :visitor_id,
                          :product_id,
                          1,
                          NOW()
                      )";
        } else { // если товар уже в списке сравнения, обновляем дату
            $query = "UPDATE
                          `compare`
                      SET
                          `added` = NOW(),
                          `active` = 1
                      WHERE
                          `visitor_id` = :visitor_id AND `product_id` = :product_id";
        }
        $this->database->execute($query, $data);

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }

        return true;

    }

    /**
     * Функция возвращает идентификатор функциональной группы товара $id
     */
    private function getProductGroup($id) {
        $group = 0;
        $query = "SELECT `group` FROM `products` WHERE `id` = :id";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            $group = $res;
        }
        return $group;
    }

    /**
     * Функция возвращает идентификатор функциональной группы товаров,
     * которые уже есть в списке сравнения; результат работы кэшируется
     */
    private function getCompareGroup() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->compareGroup();
        }

        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает идентификатор функциональной группы товаров,
     * которые уже есть в списке сравнения
     */
    protected function compareGroup() {
        $group = 0;
        $query = "SELECT
                      `a`.`group`
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1 AND `a`.`visible` = 1
                  ORDER BY
                      `added` DESC
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
        if ($res) {
            $group = $res;
        }
        return $group;
    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для центральной колонки, полный вариант
     */
    public function getCompareProducts() {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`techdata` AS `techdata`,
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
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1 AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT
                      10";
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
            $products[$key]['action']['compare'] = $this->getURL('frontend/compare/rmvprd');
        }

        // удаляем старые товары
        if (rand(1, 100) == 50) {
            $this->removeOldCompare();
        }

        return $products;
    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для центральной колонки, полный вариант
     */
    public function getCompareProducts2() {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`image` AS `image`,
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1
                      AND `a`.`group` = :group_id AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT
                      10";
        $products = $this->database->fetchAll(
            $query,
            array(
                'group_id'   => $this->groupId,
                'visitor_id' => $this->visitorId
            )
        );
        // добавляем в массив товаров информацию об URL товаров, фото
        foreach($products as $key => $value) {
            // URL файла изображения товара
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
            $query = "SELECT
                          `a`.`group_id` AS `group_id`, `a`.`param_id` AS `param_id`,
                          GROUP_CONCAT(`c`.`name` SEPARATOR '¤') AS `param_value`
                      FROM
                          `group_param_value` `a`
                          LEFT JOIN `product_param_value` `b`
                          ON `a`.`param_id` = `b`.`param_id`
                          LEFT JOIN `params` `c`
                          ON `b`.`param_id` = `c`.`id`
                          LEFT JOIN `values` `d`
                          ON `b`.`value_id` = `d`.`id`
                      WHERE
                          `a`.`group_id` = :group_id
                          AND `b`.`product_id` = :product_id
                      GROUP BY
                          1, 2
                      ORDER BY
                          `c`.`name`, `c`.`id`";
            $products[$key]['params'] = $this->database->fetchAll(
                $query,
                array(
                    'group_id' => $this->groupId,
                    'product_id' => $value['id']
                )
            );
        }

        // удаляем старые товары
        if (rand(1, 100) == 50) {
            $this->removeOldCompare();
        }

        return $products;
    }

    public function getGroupName() {
        if (0 == $this->groupId) {
            return null;
        }
        $query = "SELECT
                      `name`
                  FROM
                      `groups`
                  WHERE
                      `id` = :group_id";
        return $this->database->fetchOne($query, array($this->groupId));
    }

    /**
     * Возвращает массив параметров, привязанных к группе
     */
    public function getGroupParams() {
        if (0 == $this->groupId) {
            return array();
        }
        // TODO: попробовать сделать запрос, не зависящий от таблицы group_param_value
        $query = "SELECT
                      DISTINCT `a`.`id` AS `id`, `a`.`name` AS `name`
                  FROM
                      `params` `a`
                      INNER JOIN `group_param_value` `b`
                      ON `a`.`id` = `b`.`param_id`
                  WHERE
                      `b`.`group_id` = :group_id
                  ORDER BY
                      `a`.`name`, `a`.`id`";
        return $this->database->fetchAll($query, array('group_id' => $this->groupId));

    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения; для
     * правой колонки, сокращенный вариант; результат работы кэшируется
     */
    public function getSideCompareProducts() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->sideCompareProducts();
        }

        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
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
    protected function sideCompareProducts() {
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
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1 AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC
                  LIMIT
                      10";
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products as $key => $value) {
            $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            $products[$key]['action'] = $this->getURL('frontend/compare/rmvprd');
        }
        return $products;
    }

    /**
     * Функция удаляет товар из списка отложенных для сравнения товаров
     */
    public function removeFromCompare($productId) {
        $query = "UPDATE
                      `compare`
                  SET
                      `active` = 0
                  WHERE
                      `product_id` = :product_id AND `visitor_id` = :visitor_id";
        $this->database->execute(
            $query,
            array(
                'product_id' => $productId,
                'visitor_id' => $this->visitorId
            )
        );
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }
        // на случай, если это последний товар из списка
        // сравнения; заодно сформируем кэш
        $this->groupId = $this->getCompareGroup();
        $time = 86400 * $this->config->user->cookie;
        // TODO: проверить при XmlHttpRequest
        setcookie('compare_group', $this->groupId, time() + $time, '/');

    }

    /**
     * Функция удаляет все товары из списка сравнения
     */
    public function clearCompareList() {
        $query = "UPDATE
                      `compare`
                  SET
                      `active` = 0
                  WHERE
                      `visitor_id` = :visitor_id";
        $this->database->execute(
            $query,
            array(
                'visitor_id' => $this->visitorId
            )
        );
        $this->groupId = 0;
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }
    }

    /**
     * Функция удаляет все старые товары для сравения
     */
    public function removeOldCompare() {
        $query = "DELETE FROM
                      `compare`
                  WHERE
                      `product_id` NOT IN (SELECT `id` FROM `products` WHERE 1)";
        $this->database->execute($query);

        $query = "DELETE FROM
                      `compare`
                  WHERE
                      `added` < NOW() - INTERVAL :days DAY";
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

        $query = "UPDATE
                      `compare`
                  SET
                      `visitor_id` = :new_visitor_id
                  WHERE
                      `visitor_id` = :old_visitor_id";
        $this->database->execute(
            $query,
            array(
                'old_visitor_id' => $this->visitorId,
                'new_visitor_id' => $newVisitorId
            )
        );

        // TODO: удалить товары, если они из разных функциональных групп
        // и установить в cookie правильное значение функциональной группы

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;

        // если среди отложенных для сравнения есть два одинаковых товара
        $query = "SELECT
                      MAX(`id`) AS `id`, `product_id`, COUNT(*) AS `count`
                  FROM
                      `compare`
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
                          `compare`
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


    // Получаем рекомендации для пользователя на основе отложенных для сравнения товаров
    public function getRecommendations() {

    }
}
