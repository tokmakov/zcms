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
    private $groupId = null;


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
        if (is_null($this->groupId)) {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
        } else {
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
        }
    }

    /**
     * Функция добавляет товар в список сравнения
     */
    public function addToCompare($productId) {
        
        // можно добавить к сравнению только 5 товаров
        if ($this->getCompareCount() > 4) {
            return false;
        }
        
        // удаляем кэш, потому как после добавления товара он будет не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }
        
        // данные для выполнения SQL-запросов
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        // функциональная группа нового товара
        $newProductGroupId = $this->getProductGroup($productId);
        if (false === $newProductGroupId) {
            return false;
        }
        
        /*
         * список сравнения пуст, можем добавлять любой товар
         */
        if (is_null($this->groupId)) {
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
            $this->database->execute($query, $data);
            $this->groupId = $newProductGroupId;
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
            return true;
        }
        
        /*
         * уже есть товары в списке сравнения, нужны дополнительные проверки
         */
         
        // можно добавить только товар, принадлежащий к той же функциональной
        // группе, что и другие товары в списке сравнения
        if ($this->groupId !== $newProductGroupId) {
            return false;
        }            
        // такой товар уже есть в списке сравнения?
        $query = "SELECT
                      1
                  FROM
                      `compare`
                  WHERE
                      `visitor_id` = :visitor_id AND
                      `product_id` = :product_id AND
                      `active`     = 1";
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
            $this->database->execute($query, $data);
        } else { // если товар уже в списке сравнения, обновляем дату добавления
            $query = "UPDATE
                          `compare`
                      SET
                          `added` = NOW()
                      WHERE
                          `visitor_id` = :visitor_id AND
                          `product_id` = :product_id AND
                          `active`     = 1";
            $this->database->execute($query, $data);
        }
        // обновляем cookie
        setcookie('compare_group', $this->groupId, time() + 31536000, '/');

        return true;

    }
    
    /**
     * Функция возвращает количество товаров в списке сравнения
     */
    private function getCompareCount() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1 AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция возвращает идентификатор функциональной группы товара $id
     */
    private function getProductGroup($id) {
        $query = "SELECT
                      `a`.`group`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE
                      `a`.`id` = :id AND
                      `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает идентификатор функциональной группы товаров,
     * которые уже есть в списке сравнения; если список сравнения пустой,
     * функция возвращает null; результат работы кэшируется
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
     * которые уже есть в списке сравнения; если список сравнения пустой,
     * функция возвращает null
     */
    protected function compareGroup() {
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
        $group = $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
        if (false === $group) {
            return null;
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
                      `b`.`added` DESC";
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
     * Функция возвращает наименование функциональной группы
     */
    public function getGroupName() {
        if (is_null($this->groupId)) {
            return '';
        }
        if (0 === $this->groupId) {
            return '';
        }
        $query = "SELECT
                      `name`
                  FROM
                      `groups`
                  WHERE
                      `id` = :group_id";
        return $this->database->fetchOne($query, array('group_id' => $this->groupId));
    }
    
    /**
     * Функция возвращает массив параметров, привязанных к группе
     */
    public function getGroupParams() {
        
        if (is_null($this->groupId)) {
            return array();
        }
        
        // получаем массив товаров, отложенных для сравнения
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`, `d`.`name` AS `maker`
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `a`.`group` = :group_id AND
                      `b`.`visitor_id` = :visitor_id AND
                      `b`.`active` = 1 AND
                      `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC";
        $products = $this->database->fetchAll(
            $query,
            array('group_id' => $this->groupId, 'visitor_id' => $this->visitorId)
        );
        $title[]      = 'Функциональное наименование';
        $code[]       = 'Код';
        $maker[]      = 'Производитель';
        $shortdescr[] = 'Краткое описание';
        foreach ($products as $product) {
            $title[]      = $product['title'];
            $code[]       = $product['code'];
            $maker[]      = $product['maker'];
            $shortdescr[] = $product['shortdescr'];
        }
        if (0 == $this->groupId) {
            return array($title, $code, $maker, $shortdescr);
        }
        
        // получаем массив параметров подбора для функциональной группы
        $query = "SELECT
                      `f`.`id` AS `id`, `f`.`name` AS `name`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `product_param_value` `e` ON `b`.`id` = `e`.`product_id`
                      INNER JOIN `params` `f` ON `e`.`param_id` = `f`.`id`
                  WHERE
                      `a`.`active` = 1 AND
                      `b`.`group` = :group_id AND
                      `b`.`visible` = 1
                  GROUP BY
                      1, 2
                  ORDER BY
                      `f`.`name`";
        $result = $this->database->fetchAll($query, array('group_id' => $this->groupId));

        /*
         * перебираем все параметры подбора, для каждого товара
         * получаем конкретное значение параметра
         */
        $params = array();
        // цикл по параметрам подбора
        foreach ($result as $i => $value) {
            $params[$i][] = $value['name'];
            // цикл по товарам, отложенным для сравнения
            foreach ($products as $j => $product) {
                $query = "SELECT
                              `b`.`name` AS `value`
                          FROM
                              `product_param_value` `a` INNER JOIN `values` `b`
                              ON `a`.`value_id` = `b`.`id`
                          WHERE
                              `a`.`product_id` = :product_id AND `a`.`param_id` = :param_id";
                $res = $this->database->fetchAll(
                    $query,
                    array(
                        'product_id' => $product['id'],
                        'param_id'   => $value['id']
                    ),
                    $this->enableDataCache
                );
                if (empty($res)) {
                    $params[$i][$j+1] = '';
                    continue;
                }
                if (count($res) > 1) {
                    foreach($res as $item) {
                        $params[$i][$j+1][] = $item['value'];
                    }
                } else {
                    $params[$i][$j+1] = $res[0]['value'];
                }
            }
        }
        
        return array_merge(array($title, $code, $maker), $params, array($shortdescr));
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
                      `a`.`unit` AS `unit`
                  FROM
                      `products` `a`
                      INNER JOIN `compare` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `b`.`active` = 1 AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC";
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
        if (is_null($this->groupId)) {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
        } else {
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
        }

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
                      `visitor_id` = :visitor_id AND `active` = 1";
        $this->database->execute(
            $query,
            array(
                'visitor_id' => $this->visitorId
            )
        );
        $this->groupId = null;
        // удаляем cookie
        setcookie('compare_group', '', time() - 86400, '/');

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

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->register->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;
        
        $this->groupId = $this->getCompareGroup();

        // если список сравнения и после объединения пустой,
        // больше ничего делать не надо
        if (is_null($this->groupId)) {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
            return;
        }
        
        // в объединенном списке сравнения есть товары, но в нем
        // могут быть товары из разных функциональных групп
        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `b`.`visible` = 1 AND
                      `b`.`group` <> :group_id";
        $temp = $this->database->fetchAll(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                'group_id'   => $this->groupId
            )
        );
        if ( ! empty($temp)) {
            foreach ($temp as $item) {
                $ids[] = $item['id'];
            }
            $query = "DELETE FROM `compare` WHERE `id` IN (" . implode(',', $ids) . ")";
            $this->database->execute(
                $query,
                array(
                    'visitor_id' => $this->visitorId,
                    'group_id'   => $this->groupId
                )
            );
        }
        // обновляем cookie
        setcookie('compare_group', $this->groupId, time() + 31536000, '/');

        // если в списке сравнения оказались два одинаковых товара (это возможно,
        // если оба списка содержали товары одной функциональной группы)
        $query = "SELECT
                      MAX(`id`) AS `id`, `product_id`, COUNT(*) AS `count`
                  FROM
                      `compare`
                  WHERE
                      `visitor_id` = :visitor_id AND
                      `active` = 1
                  GROUP BY
                      `product_id`
                  HAVING
                      COUNT(*) > 1";
        $result = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        if (empty($result)) {
            return;
        }
        foreach ($result as $item) {
            $query = "DELETE FROM
                          `compare`
                      WHERE
                          `id` < :id AND
                          `product_id` = :product_id AND
                          `visitor_id` = :visitor_id AND
                          `active` = 1";
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
