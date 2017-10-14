<?php
/**
 * Класс Basket_Frontend_Model отвечает за корзину покупателя, взаимодействует с базой
 * данных, реализует шаблон проектирования «Наблюдатель», общедоступная часть сайта; см.
 * описание интерфейса SplObserver http://php.net/manual/ru/class.splobserver.php
 */
class Basket_Frontend_Model extends Frontend_Model implements SplObserver {

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
     * Функция добавляет товар в корзину покупателя
     */
    public function addToBasket($productId, $quantity, $delay = 0) {
        if (0 == $quantity) {
            return;
        }
        if ($quantity > 16000000) {
            $quantity = 16000000;
        }
        // такой товар уже есть в корзине?
        $query = "SELECT
                      `quantity`
                  FROM
                      `baskets`
                  WHERE
                      `visitor_id` = :visitor_id AND `product_id` = :product_id";
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        $result = $this->database->fetchOne($query, $data);
        $data['quantity'] = $quantity;
        if (false === $result) { // если такого товара еще нет в корзине, добавляем его
            $query = "INSERT INTO `baskets`
                      (
                          `visitor_id`,
                          `product_id`,
                          `quantity`,
                          `added`
                      )
                      VALUES
                      (
                          :visitor_id,
                          :product_id,
                          :quantity,
                          NOW() - INTERVAL ".$delay." SECOND
                      )";
        } else { // если такой товар уже есть в корзине, обновляем количество и дату
            $data['quantity'] = $data['quantity'] + $result;
            if ($data['quantity'] > 16000000) {
                $data['quantity'] = 16000000;
            }
            $query = "UPDATE
                          `baskets`
                      SET
                          `quantity` = :quantity,
                          `added` = NOW() - INTERVAL ".$delay." SECOND
                      WHERE
                          `visitor_id` = :visitor_id AND `product_id` = :product_id";
        }
        $this->database->execute($query, $data);

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
    }

    /**
     * Функция обновляет количество товара в корзине покупателя
     */
    public function updateBasket() {
        if ( ! isset($_POST['ids'])) {
            return;
        }
        if ( ! is_array($_POST['ids'])) {
            return;
        }
        foreach ($_POST['ids'] as $key => $value) {
            $key = (string)$key;
            if ( ! ctype_digit($key)) {
                continue;
            }
            if ( ! ctype_digit($value)) {
                continue;
            }
            $key = (int)$key;
            $value = (int)$value;
            if ($value) { // если количество товара не равно нулю
                if ($value > 16000000) {
                    $value = 16000000;
                }
                $query = "UPDATE
                              `baskets`
                          SET
                              `quantity` = :quantity
                          WHERE
                              `product_id` = :product_id AND `visitor_id` = :visitor_id";
                $this->database->execute(
                    $query,
                    array(
                        'quantity'   => $value,
                        'product_id' => $key,
                        'visitor_id' => $this->visitorId
                    )
                );
            } else { // кол-во равно нулю, удаляем товар из корзины
                $query = "DELETE FROM
                              `baskets`
                          WHERE
                              `product_id` = :product_id AND `visitor_id` = :visitor_id";
                $this->database->execute(
                    $query,
                    array(
                        'product_id' => $key,
                        'visitor_id' => $this->visitorId
                    )
                );
            }
        }
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
    }

    /**
     * Функция удаляет товар из корзины покупателя
     */
    public function removeFromBasket($productId) {
        $query = "DELETE FROM
                      `baskets`
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
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
    }

    /**
     * Функция возвращает массив товаров в корзине;
     * для центральной колонки, полный вариант
     */
    public function getBasketProducts() {
        // тип пользователя
        $type = 0;
        if ($this->register->userFrontendModel->isAuthUser()) {
            $type = $this->register->userFrontendModel->getUserType();
        }
        $price = 'price';
        if ($type > 1) {
            $price = 'price' . $type;
        }
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`price` AS `price`,
                      `a`.`" . $price . "` AS `user_price`,
                      `a`.`unit` AS `unit`,
                      `a`.`image` AS `image`,
                      `c`.`id` AS `ctg_id`,
                      `c`.`name` AS `ctg_name`,
                      `d`.`id` AS `mkr_id`,
                      `d`.`name` AS `mkr_name`,
                      DATE_FORMAT(`b`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`b`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`quantity` AS `quantity`,
                      `a`.`price`*`b`.`quantity` AS `cost`,
                      `a`.`" . $price . "`*`b`.`quantity` AS `user_cost`
                  FROM
                      `products` `a`
                      INNER JOIN `baskets` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `a`.`group` = `e`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id  AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC, `b`.`id` DESC";
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем URL ссылок на товары и URL ссылок для удаления товара из корзины
        foreach ($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL ссылки на фото товара
            if ((!empty($value['image'])) && is_file('files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // URL ссылки для удаления товара из корзины
            $products[$key]['url']['remove'] = $this->getURL('frontend/basket/rmvprd/id/' . $value['id']);
        }
        return $products;
    }

    /**
     * Функция возвращает массив товаров в корзине; для правой колонки,
     * сокращенный вариант; результат работы кэшируется
     */
    public function getSideBasketProducts() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->sideBasketProducts();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
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
     * Функция возвращает массив товаров в корзине;
     * для правой колонки, сокращенный вариант
     */
    protected function sideBasketProducts() {
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      DATE_FORMAT(`b`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`b`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`quantity` AS `quantity`,
                      `a`.`price`*`b`.`quantity` AS `cost`
                  FROM
                      `products` `a`
                      INNER JOIN `baskets` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `a`.`group` = `e`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id  AND `a`.`visible` = 1
                  ORDER BY
                      `b`.`added` DESC, `b`.`id` DESC";
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products as $key => $value) {
            $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
        }
        return $products;
    }

    /**
     * Функция возвращает общую стоимость товаров в корзине,
     * для центральной колонки
     */
    public function getTotalCost() {
        // тип пользователя
        $type = 0;
        if ($this->register->userFrontendModel->isAuthUser()) {
            $type = $this->register->userFrontendModel->getUserType();
        }
        $price = 'price';
        if ($type > 1) {
            $price = 'price' . $type;
        }
        $query = "SELECT
                      SUM(`a`.`price` * `b`.`quantity`) AS `amount`,
                      SUM(`a`.`" . $price . "` * `b`.`quantity`) AS `user_amount`
                  FROM
                      `products` `a`
                      INNER JOIN `baskets` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `a`.`group` = `e`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetch($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция возвращает общую стоимость товаров в корзине,
     * для правой колонки, результат работы кэшируется
     */
    public function getSideTotalCost() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->sideTotalCost();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-amount-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает общую стоимость товаров в корзине,
     * для правой колонки, результат работы кэшируется
     */
    protected function sideTotalCost() {
        $query = "SELECT
                      SUM(`a`.`price` * `b`.`quantity`)
                  FROM
                      `products` `a`
                      INNER JOIN `baskets` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `a`.`group` = `e`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция возвращает количество товаров в корзине; результат
     * работы кэшируется
     */
    public function getBasketCount() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->basketCount();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество товаров в корзине
     */
    protected function basketCount() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `baskets` `b` ON `a`.`id` = `b`.`product_id`
                      INNER JOIN `categories` `c` ON `a`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `a`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `a`.`group` = `e`.`id`
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция удаляет все товары из корзины
     */
    public function clearBasket() {
        $query = "DELETE FROM
                      `baskets`
                  WHERE
                      `visitor_id` = :visitor_id";
        $this->database->execute($query, array('visitor_id' => $this->visitorId));
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
    }

    /**
     * Функция удаляет все старые корзины
     */
    public function removeOldBaskets() {
        $query = "DELETE FROM
                      `baskets`
                  WHERE
                      `product_id` NOT IN (SELECT `id` FROM `products` WHERE 1)";
        $this->database->execute($query);

        $query = "DELETE FROM
                      `baskets`
                  WHERE
                      `added` < NOW() - INTERVAL :days DAY";
        $this->database->execute($query, array('days' => $this->config->user->cookie));
    }

    /**
     * Функция создает заказ: записывает данные в таблицы БД orders и orders_prds,
     * удаляет товары из корзины. Дополнительно очищает старые корзины.
     */
    public function createOrder($form) {

        $products = $this->getBasketProducts();
        if (count($products) == 0) {
            return false;
        }

        $data = array();
        // уникальный идентификатор авторизованного пользователя или ноль
        $data['user_id'] =
            $this->register->userFrontendModel->isAuthUser() ? $this->register->userFrontendModel->getUserId() : 0;
        // уникальный идентификатор посетителя сайта
        $data['visitor_id'] = $this->visitorId;
        // общая стоимость товаров в корзине
        $temp = $this->getTotalCost();
        // общая стоимость товаров в корзине без учета скидки
        $data['amount'] = $temp['amount'];
        // общая стоимость товаров в корзине с учетом скидки
        $data['user_amount'] = $temp['user_amount'];
        // подробная информация о покупателе
        $data['details'] = serialize($form);

        // начинаем транзакцию
        try {
            $this->database->beginTransaction();
            $query = "INSERT INTO `orders`
                      (
                          `user_id`,
                          `visitor_id`,
                          `amount`,
                          `user_amount`,
                          `details`,
                          `added`,
                          `status`
                      )
                      VALUES
                      (
                          :user_id,
                          :visitor_id,
                          :amount,
                          :user_amount,
                          :details,
                          NOW(),
                          0
                      )";
            $this->database->execute($query, $data);
            $orderId = $this->database->lastInsertId();

            foreach ($products as $product) {
                $query = "INSERT INTO `orders_prds`
                          (
                              `order_id`,
                              `product_id`,
                              `code`,
                              `name`,
                              `title`,
                              `price`,
                              `user_price`,
                              `unit`,
                              `quantity`,
                              `cost`,
                              `user_cost`
                          )
                          VALUES
                          (
                              :order_id,
                              :id,
                              :code,
                              :name,
                              :title,
                              :price,
                              :user_price,
                              :unit,
                              :quantity,
                              :cost,
                              :user_cost
                          )";
                unset(
                    $product['shortdescr'],
                    $product['image'],
                    $product['ctg_id'],
                    $product['ctg_name'],
                    $product['mkr_id'],
                    $product['mkr_name'],
                    $product['date'],
                    $product['time'],
                    $product['url']
                );
                $product['order_id'] = $orderId;
                $this->database->execute($query, $product);
            }

            // подтверждаем транзакцию
            $this->database->commit();
            $success = true;

        } catch(Exception $e) {
            // откатываем назад транзакцию
            $this->database->rollBack();
            $success = false;
        }

        if ($success) {
            // очищаем корзину
            $this->clearBasket();
            // отправляем письма покупателю и администратору
            $this->sendOrderMail($orderId, $form, $products, $data['user_amount']);
        }

        // удаляем старые корзины
        $this->removeOldBaskets();

        return $success;
    }

    /**
     * Функция формирует и отправляет письма о заказе покупателю и администратору
     */
    private function sendOrderMail($orderId, $details, $products, $user_amount) {

        $html = '<h2>Заявка № '.$orderId.'</h2>' . PHP_EOL;
        // товары
        $html = $html . '<table border="1" cellspacing="0" cellpadding="4">' . PHP_EOL;
        $html = $html . '<tr>' . PHP_EOL;
        $html = $html . '<th>Код</th><th>Наименование</th><th>Кол.</th><th>Цена</th><th>Стоим.</th>' . PHP_EOL;
        $html = $html . '</tr>' . PHP_EOL;
        foreach ($products as $product) {
            $html = $html . '<tr>' . PHP_EOL;
            $html = $html . '<td><a href="' . $product['url']['product'] . '">'.$product['code'].'</a></td>';
            $html = $html . '<td>'.$product['name'].'</td>';
            $html = $html . '<td>'.$product['quantity'].'</td>';
            $html = $html . '<td>'.number_format($product['user_price'], 2, '.', '').'</td>';
            $html = $html . '<td>'.number_format($product['user_cost'], 2, '.', '').'</td>' . PHP_EOL;
            $html = $html . '</tr>' . PHP_EOL;
        }
        $html = $html . '<tr>' . PHP_EOL;
        $html = $html . '<td colspan="4" align="right">Итого</td><td>'.number_format($user_amount, 2, '.', '').'</td>' . PHP_EOL;
        $html = $html . '</tr>' . PHP_EOL;
        $html = $html . '</table>' . PHP_EOL;
        // плательщик
        $html = $html . '<h3>Плательщик</h3>' . PHP_EOL;
        $html = $html . '<ul>' . PHP_EOL;
        $html = $html . '<li>Фамилия: '.$details['payer_surname'].'</li>' . PHP_EOL;
        $html = $html . '<li>Имя: '.$details['payer_name'].'</li>' . PHP_EOL;
        if ( ! empty($details['payer_patronymic'])) {
            $html = $html . '<li>Отчество: '.$details['payer_patronymic'].'</li>' . PHP_EOL;
        }
        $html = $html . '<li>E-mail: '.$details['payer_email'].'</li>' . PHP_EOL;
        if ( ! empty($details['payer_phone'])) {
            $html = $html . '<li>Телефон: '.$details['payer_phone'].'</li>' . PHP_EOL;
        }
        $html = $html . '</ul>' . PHP_EOL;
        if ($details['payer_company']) {
            $html = $html . '<ul>' . PHP_EOL;
            $html = $html . '<li>Название компании: '.$details['payer_company_name'].'</li>' . PHP_EOL;
            $html = $html . '<li>Генеральный директор: '.$details['payer_company_ceo'].'</li>' . PHP_EOL;
            $html = $html . '<li>Юридический адрес: '.$details['payer_company_address'].'</li>' . PHP_EOL;
            $html = $html . '<li>ИНН: '.$details['payer_company_inn'].'</li>' . PHP_EOL;
            $html = $html . '<li>КПП: '.$details['payer_company_kpp'].'</li>' . PHP_EOL;
            $html = $html . '<li>Название банка: '.$details['payer_bank_name'].'</li>' . PHP_EOL;
            $html = $html . '<li>БИК банка: '.$details['payer_bank_bik'].'</li>' . PHP_EOL;
            $html = $html . '<li>Расчетный счет: '.$details['payer_settl_acc'].'</li>' . PHP_EOL;
            $html = $html . '<li>Корреспондентский счет: '.$details['payer_corr_acc'].'</li>' . PHP_EOL;
            $html = $html . '</ul>' . PHP_EOL;
        }
        // получатель
        if ($details['payer_getter_different']) {
           $html = $html . '<h3>Получатель</h3>' . PHP_EOL;
           $html = $html . '<ul>' . PHP_EOL;
           $html = $html . '<li>Фамилия: '.$details['getter_surname'].'</li>' . PHP_EOL;
           $html = $html . '<li>Имя: '.$details['getter_name'].'</li>' . PHP_EOL;
           if ( ! empty($details['getter_patronymic'])) {
               $html = $html . '<li>Отчество: '.$details['getter_patronymic'].'</li>' . PHP_EOL;
           }
           $html = $html . '<li>E-mail: '.$details['getter_email'].'</li>' . PHP_EOL;
           if ( ! empty($details['getter_phone'])) {
               $html = $html . '<li>Телефон: '.$details['getter_phone'].'</li>' . PHP_EOL;
           }
           $html = $html . '</ul>' . PHP_EOL;
           if ($details['getter_company']) {
               $html = $html . '<ul>' . PHP_EOL;
               $html = $html . '<li>Название компании: '.$details['getter_company_name'].'</li>' . PHP_EOL;
               $html = $html . '<li>Генеральный директор: '.$details['getter_company_ceo'].'</li>' . PHP_EOL;
               $html = $html . '<li>Юридический адрес: '.$details['getter_company_address'].'</li>' . PHP_EOL;
               $html = $html . '<li>ИНН: '.$details['getter_company_inn'].'</li>' . PHP_EOL;
               $html = $html . '<li>КПП: '.$details['getter_company_kpp'].'</li>' . PHP_EOL;
               $html = $html . '<li>Название банка: '.$details['getter_bank_name'].'</li>' . PHP_EOL;
               $html = $html . '<li>БИК банка: '.$details['getter_bank_bik'].'</li>' . PHP_EOL;
               $html = $html . '<li>Расчетный счет: '.$details['getter_settl_acc'].'</li>' . PHP_EOL;
               $html = $html . '<li>Корреспондентский счет: '.$details['getter_corr_acc'].'</li>' . PHP_EOL;
               $html = $html . '</ul>' . PHP_EOL;
           }
        }
        // доставка
        $html = $html . '<ul>' . PHP_EOL;
        if ( ! $details['shipping']) {
            $html = $html . '<li>Адрес доставки: '.$details['shipping_address'].'</li>' . PHP_EOL;
            $html = $html . '<li>Город доставки: '.$details['shipping_city'].'</li>' . PHP_EOL;
            $html = $html . '<li>Почтовый индекс: '.$details['shipping_index'].'</li>' . PHP_EOL;
        } else {
            // TODO: Офис самовывоза
            $html = $html . '<li>Самовывоз со склада</li>' . PHP_EOL;
        }
        $html = $html . '</ul>' . PHP_EOL;
        // комментарий
        if ( ! empty($details['comment'])) {
            $html = $html . '<h4>Комментарий</h4>' . PHP_EOL;
            $html = $html . '<p>' . nl2br($details['comment']) . '</p>' . PHP_EOL;
        }
        $html = $html . '<p>';
        $html = $html . $this->config->site->name . '<br/>';
        $html = $html . 'Телефон: ' . $this->config->site->phone . '<br/>';
        $html = $html . 'Почта: <a href="mailto:' . $this->config->site->email . '">' . $this->config->site->email . '</a>';
        $html = $html . '</p>';

        // если пользователь авторизован, отправляем письмо на адрес, указанный при регистрации
        if ($this->register->userFrontendModel->isAuthUser()) {
            $email = $this->register->userFrontendModel->getUserEmail();
        } else { // если не авторизован, отправляем письмо на адрес плательщика
            $email = $details['payer_email'];
        }

        $subject = '=?utf-8?b?' . base64_encode('Заявка № '.$orderId).'?=';
        $headers = 'From: =?utf-8?b?' . base64_encode($this->config->site->name) . '?= <' . $this->config->email->site . '>' . "\r\n";
        $headers = $headers . 'Return-path: <' . $this->config->email->admin . '>' . "\r\n";
        // определяем, кому будем отправлять копии письма
        $carbonCopy = array();
        // если пользователь авторизован, и адреса пользователя  и плательщика
        // не совпадают, отправляем копию письма плательщику
        if ($details['payer_email'] != $email) {
            $carbonCopy[] = $details['payer_email'];
        }
        // если плательщик и получатель различаются, и адрес получателя не совпадает с адресами
        // пользователя (сайта) и плательщика (заказа), отправляем копию письма получателю
        $condition =
            $details['payer_getter_different'] && $details['getter_email'] != $details['payer_email'] && $details['getter_email'] != $email;
        if ($condition) {
            $carbonCopy[] = $details['getter_email'];
        }
        if ( ! empty($carbonCopy)) {
            $headers = $headers . 'Cc: <' . implode(',', $carbonCopy) . '>' . "\r\n";
        }
        // отправляем скрытую копию письма администратору
        $headers = $headers . 'Bcc: <' . $this->config->email->order . '>' . "\r\n";
        $headers = $headers . 'Date: ' . date('r') . "\r\n";
        $headers = $headers . 'Content-type: text/html; charset="utf-8"' . "\r\n";
        $headers = $headers . 'Content-Transfer-Encoding: base64';

        $message = chunk_split(base64_encode($html));

        mail($email, $subject, $message, $headers);

    }

    /**
     * Функция объединяет корзины (ещё) не авторизованного посетителя и (уже)
     * авторизованного пользователя сразу после авторизации, реализация шаблона
     * проектирования «Наблюдатель». См. описание интерфейса SplObserver здесь
     * http://php.net/manual/ru/class.splobserver.php
     */
    public function update(SplSubject $userFrontendModel) {

        /*
         * Уникальный идентификатор посетителя сайта сохраняется в cookie и нужен
         * для работы покупательской корзины. По нему можно получить из таблицы БД
         * `baskets` все товары, добавленные в корзину посетителем.
         *
         * Если в cookie есть идентификатор посетителя, значит он уже просматривал
         * страницы сайта с этого компьютера. Если идентификатора нет в cookie,
         * значит посетитель на сайте первый раз (и просматривает первую страницу),
         * или зашел с другого компьютера. В этом случае записываем в cookie новый
         * идентификатор.
         *
         * Если в cookie не было идентификатора посетителя и ему был записан новый
         * идентификатор, это еще не означает, что посетитель здесь в первый раз.
         * Он мог зайти с другого компьютера, удалить cookie или просто истекло
         * время жизни cookie.
         *
         * Сразу после авторизации проверяем — совпадает временный идентификатор
         * посетителя (который сохранен в cookie) с постоянным (который хранится в
         * в БД `users`). Если совпадает — ничего не делаем, если нет — записываем
         * в cookie постоянный идентификатор вместо временного и обновляем записи
         * таблицы БД `baskets`, заменяя временный идентификатор на постоянный.
         */
        $newVisitorId = $userFrontendModel->getVisitorId();
        $oldVisitorId = $this->visitorId;

        if ($newVisitorId == $oldVisitorId) {
            return;
        }

        /*
         * Объединяем корзины, т.е. заменяем идентификатор посетителя сайта
         */
        $query = "UPDATE
                      `baskets`
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
            $key = __CLASS__ . '-products-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            // кэш (уже) авторизованного пользователя
            $key = __CLASS__ . '-products-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-amount-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;

        // если в корзине пользователя есть два одинаковых товара
        $query = "SELECT
                      MAX(`id`) AS `id`, `product_id`,
                      COUNT(*) AS `count`, SUM(`quantity`) AS `quantity`
                  FROM
                      `baskets`
                  WHERE
                      `visitor_id` = :visitor_id
                  GROUP BY
                      `product_id`
                  HAVING
                      COUNT(*) > 1";
        $res = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        if (empty($res)) { // одинаковых товаров нет, больше ничего делать не надо
            return;
        }
        foreach ($res as $item) {
            // удаляем из корзины товар, который был добавлен раньше ...
            $query = "DELETE FROM
                          `baskets`
                      WHERE
                          `id` < :id AND `product_id` = :product_id AND `visitor_id` = :visitor_id";
            $this->database->execute(
                $query,
                array(
                    'id'         => $item['id'],
                    'product_id' => $item['product_id'],
                    'visitor_id' => $this->visitorId
                )
            );
            // ... и увеличиваем кол-во товара, который был добавлен позже
            $query = "UPDATE
                          `baskets`
                      SET
                          `quantity` = :quantity
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'id'       => $item['id'],
                    'quantity' => $item['quantity']
                )
            );
        }

    }

    /**
     * Функция возвращает массив рекомендованных товаров для товара(ов)
     * с уникальным идентификатором $id(s); результат работы кэшируется
     */
    public function getRecommendedProducts($ids) {
        if (empty($ids)) {
            return array();
        }

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->recommendedProducts($ids);
        }

        if (is_array($ids)) { // рекомендации для массива товаров
            $temp = implode(',', $ids);
        } else { // рекомендации для одного товара
            $temp = $ids;
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-ids-' . $temp;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает массив рекомендованных товаров для товара(ов)
     * с уникальным идентификатором $id(s)
     */
    protected function recommendedProducts($ids) {

        if (is_array($ids) && count($ids) == 1) $ids = $ids[0];

        /*
         * Если рекомендации для нескольких товаров: для каждого товара получаем массив id
         * рекомендованных в формате CSV, а потом объединяем все массивы в один, например
         * $ids = array(123, 456, 789);
         * $first = array(12, 34, 56); $second = array(); third = array(98, 76, 54, 32);
         * $related = array(12, 98, 34, 76, 56, 54, 32);
         */
        if (is_array($ids)) {
            $result = array();
            foreach ($ids as $id) {
                $query = "SELECT `related` FROM `products` WHERE `id` = :id";
                $res = $this->database->fetchOne($query, array('id' => $id));
                if ( ! empty($res)) {
                    $result[] = explode(',', $res);
                }
            }
            if (empty($result)) {
                return array();
            }
            $related = array();
            for ($i = 0; $i < 10; $i++) { //  у одного товара не более 10 рекомендованных
                foreach ($result as $item) {
                    if (isset($item[$i])) $related[] = $item[$i];
                }
            }
            $related = implode(',', $related);
        } else {
            $query = "SELECT `related` FROM `products` WHERE `id` = :id";
            $related = $this->database->fetchOne($query, array('id' => $ids));
            if (empty($related)) {
                return array();
            }
        }

        if (is_array($ids)) {
            $limit = 20;
            $source = implode(',', $ids);
        } else {
            $limit = 8;
            $source = $ids;
        }
        $query = 'SELECT
                      DISTINCT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`,
                      `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`,
                      `c`.`name` AS `mkr_name`,
                      `d`.`id` AS `grp_id`,
                      `d`.`name` AS `grp_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                  WHERE
                      `a`.`id` IN (' . $related . ') AND `a`.`id` NOT IN (' . $source . ')
                  ORDER BY
                      FIND_IN_SET(`a`.`id`, "' . $related . '")
                  LIMIT
                      :limit';

        $products = $this->database->fetchAll($query, array('limit' => $limit));
        // добавляем в массив товаров информацию об URL товаров, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network
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

    /**
     * Функция возвращает массив рекомендованных товаров для товара(ов)
     * с уникальным идентификатором $id(s)
     */
    protected function recommendedProductsOld($ids) {

        $limit = 8;
        if (is_array($ids)) { // рекомендации для массива товаров
            $temp = implode(',', $ids);
            if (count($ids) > 1) {
                $limit = 16;
            }
        } else { // рекомендации для одного товара
            $temp = $ids;
        }
/*
        $query = "SELECT
                      DISTINCT
                      `c`.`id` AS `id`,
                      `c`.`code` AS `code`,
                      `c`.`name` AS `name`,
                      `c`.`title` AS `title`,
                      `c`.`price` AS `price`,
                      `c`.`price2` AS `price2`,
                      `c`.`price3` AS `price3`,
                      `c`.`unit` AS `unit`,
                      `c`.`shortdescr` AS `shortdescr`,
                      `c`.`image` AS `image`,
                      `d`.`id` AS `ctg_id`,
                      `d`.`name` AS `ctg_name`,
                      `e`.`id` AS `mkr_id`,
                      `e`.`name` AS `mkr_name`,
                      `f`.`id` AS `grp_id`,
                      `f`.`name` AS `grp_name`,
                      COUNT(*) AS `count`
                  FROM
                      `orders_prds` `a`
                      INNER JOIN `orders_prds` `b` ON `a`.`order_id` = `b`.`order_id`
                      INNER JOIN `products` `c` ON `b`.`product_id` = `c`.`id`
                      INNER JOIN `categories` `d` ON `c`.`category` = `d`.`id`
                      INNER JOIN `makers` `e` ON `c`.`maker` = `e`.`id`
                      INNER JOIN `groups` `f` ON `c`.`group` = `f`.`id`
                  WHERE
                      `a`.`product_id` <> `b`.`product_id` AND
                      `a`.`product_id` IN (" . $temp . ") AND
                      `b`.`product_id` NOT IN (" . $temp . ")
                  GROUP BY
                      1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16
                  HAVING
                      COUNT(*) > 10
                  ORDER BY
                      COUNT(*) DESC
                  LIMIT
                      " . $limit;
*/

        $query = "SELECT
                      DISTINCT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`price` AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`,
                      `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`,
                      `c`.`name` AS `mkr_name`,
                      `d`.`id` AS `grp_id`,
                      `d`.`name` AS `grp_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                      INNER JOIN `related` `e` ON `a`.`id` = `e`.`id2`
                  WHERE
                      `e`.`id1` IN (".$temp.") AND `a`.`id` NOT IN (".$temp.")
                  ORDER BY
                      `e`.`sortorder`
                  LIMIT
                      " . $limit;

        $products = $this->database->fetchAll($query);
        // добавляем в массив товаров информацию об URL товаров, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network
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