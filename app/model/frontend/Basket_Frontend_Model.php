<?php
/**
 * Класс Basket_Frontend_Model отвечает за корзину покупателя, взаимодействует с
 * базой данных, реализует шаблон проектирования «Наблюдатель», реализует шаблон
 * проектирования «Наблюдатель», общедоступная часть сайта; см. описание интерфейса
 * SplObserver http://php.net/manual/ru/class.splobserver.php
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
            if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
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
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->sideBasketProducts();
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
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetch($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция возвращает общую стоимость товаров в корзине,
     * для правой колонки, результат работы кэшируется
     */
    public function getSideTotalCost() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->sideTotalCost();
        }

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
                  WHERE
                      `b`.`visitor_id` = :visitor_id AND `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }

    /**
     * Функция возвращает количество товаров в корзине; результат
     * работы кэшируется
     */
    public function getBasketCount() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->basketCount();
        }

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
        $html = $html . '<table border="1" cellspacing="0" cellpadding="4">' . PHP_EOL;
        $html = $html . '<tr>' . PHP_EOL;
        $html = $html . '<th>Код</th><th>Наименование</th><th>Кол.</th><th>Цена</th><th>Стоим.</th>' . PHP_EOL;
        $html = $html . '</tr>' . PHP_EOL;
        foreach ($products as $product) {
            $html = $html . '<tr>' . PHP_EOL;
            $html = $html . '<td>'.$product['code'].'</td>';
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

        $html = $html . '<h3>Получатель</h3>' . PHP_EOL;
        $html = $html . '<ul>' . PHP_EOL;
        $html = $html . '<li>Фамилия: '.$details['buyer_surname'].'</li>' . PHP_EOL;
        $html = $html . '<li>Имя: '.$details['buyer_name'].'</li>' . PHP_EOL;
        if ( ! empty($details['buyer_patronymic'])) {
            $html = $html . '<li>Отчество: '.$details['buyer_patronymic'].'</li>' . PHP_EOL;
        }
        $html = $html . '<li>E-mail: '.$details['buyer_email'].'</li>' . PHP_EOL;
        if ( ! empty($details['buyer_phone'])) {
            $html = $html . '<li>Телефон: '.$details['buyer_phone'].'</li>' . PHP_EOL;
        }
        $html = $html . '</ul>' . PHP_EOL;
        $html = $html . '<ul>' . PHP_EOL;
        if ( ! $details['shipping']) {
            $html = $html . '<li>Адрес доставки: '.$details['buyer_shipping_address'].'</li>' . PHP_EOL;
            $html = $html . '<li>Город доставки: '.$details['buyer_shipping_city'].'</li>' . PHP_EOL;
            $html = $html . '<li>Почтовый индекс: '.$details['buyer_shipping_index'].'</li>' . PHP_EOL;
        } else {
            $html = $html . '<li>Самовывоз со склада</li>' . PHP_EOL;
        }
        $html = $html . '</ul>' . PHP_EOL;
        if ($details['buyer_company']) {
            $html = $html . '<ul>' . PHP_EOL;
            $html = $html . '<li>Название компании: '.$details['buyer_company_name'].'</li>' . PHP_EOL;
            $html = $html . '<li>Генеральный директор: '.$details['buyer_company_ceo'].'</li>' . PHP_EOL;
            $html = $html . '<li>Юридический адрес: '.$details['buyer_company_address'].'</li>' . PHP_EOL;
            $html = $html . '<li>ИНН: '.$details['buyer_company_inn'].'</li>' . PHP_EOL;
            $html = $html . '<li>КПП: '.$details['buyer_company_kpp'].'</li>' . PHP_EOL;
            $html = $html . '<li>Название банка: '.$details['buyer_bank_name'].'</li>' . PHP_EOL;
            $html = $html . '<li>БИК банка: '.$details['buyer_bank_bik'].'</li>' . PHP_EOL;
            $html = $html . '<li>Расчетный счет: '.$details['buyer_settl_acc'].'</li>' . PHP_EOL;
            $html = $html . '<li>Корреспондентский счет: '.$details['buyer_corr_acc'].'</li>' . PHP_EOL;
            $html = $html . '</ul>' . PHP_EOL;
        }
        if ($details['buyer_payer_different']) {
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
        }
        if ( ! empty($details['comment'])) {
            $html = $html . '<h4>Комментарий</h4>' . PHP_EOL;
            $html = $html . '<p>'.nl2br($details['comment']).'</p>' . PHP_EOL;
        }
        $html = $html . '<p>';
        $html = $html . $this->config->site->name . '<br/>';
        $html = $html . 'Телефон: ' . $this->config->site->phone . '<br/>';
        $html = $html . 'Почта: <a href="mailto:' . $this->config->site->email . '">' . $this->config->site->email . '</a>';
        $html = $html . '</p>';

        // если пользователь авторизован, отправляем письмо на адрес, указанный при регистрации
        if ($this->register->userFrontendModel->isAuthUser()) {
            $email = $this->register->userFrontendModel->getUserEmail();
        } else { // если не авторизован, отправляем письмо на адрес получателя заказа
            $email = $details['buyer_email'];
        }

        $subject = '=?utf-8?b?'.base64_encode('Заявка № '.$orderId).'?=';
        $headers = 'From: =?utf-8?b?' . base64_encode($this->config->site->name) . '?= <' . $this->config->email->site . '>' . "\r\n";
        $headers = $headers . 'Return-path: <' . $this->config->email->admin . '>' . "\r\n";
        // определяем, кому будем отправлять копии письма
        $carbonCopy = array();
        // если пользователь авторизован, и адреса пользователя (сайта) и получателя (заказа)
        // не совпадают, отправляем копию письма получателю заказа
        if ($details['buyer_email'] != $email) {
            $carbonCopy[] = $details['buyer_email'];
        }
        // если получатель и плательщик различаются, отправляем копию письма плательщику
        if ($details['payer_email'] != $details['buyer_email'] && $details['payer_email'] != $email) {
            $carbonCopy[] = $details['payer_email'];
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

        // mail($email, $subject, $message, $headers);
    }

    /**
     * Функция объединяет корзины (ещё) не авторизованного посетителя и (уже)
     * авторизованного пользователя сразу после авторизации, реализация шаблона
     * проектирования «Наблюдатель»; см. описание интерфейса SplObserver здесь
     * http://php.net/manual/ru/class.splobserver.php
     */
    public function update(SplSubject $userFrontendModel) {

        $newVisitorId = $userFrontendModel->getVisitorId();
        $oldVisitorId = $this->visitorId;

        if ($newVisitorId == $oldVisitorId) {
            return;
        }

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
        if (empty($res)) {
            return;
        }
        foreach ($res as $item) {
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
        $limit = 8;
        if (is_array($ids)) { // рекомендации для массива товаров
            $temp = implode(',', $ids);
            if (count($ids) > 1) {
                $limit = 20;
            }
        } else { // рекомендации для одного товара
            $temp = $ids;
        }
        /*
        SELECT
            `c`.`id` AS `id`, `c`.`name` AS `name`, `c`.`price` AS `price`,
            `c`.`image` AS `image`, COUNT(*) AS `count`
        FROM
            `orders_prds` `a`
            INNER JOIN `orders_prds` `b` ON `a`.`order_id`=`b`.`order_id`
            INNER JOIN `products` `c` ON `b`.`product_id`=`c`.`id`
        WHERE
            `a`.`product_id`<>`b`.`product_id` AND
            `a`.`product_id` IN (1001, 1002) AND
            `b`.`product_id` NOT IN (1001, 1002)
        GROUP BY 1, 2, 3, 4
        HAVING COUNT(*)> 10
        ORDER BY COUNT(*) DESC
        LIMIT 10
        */
        $query = "SELECT
                      DISTINCT
                      `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
                      `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `related` `d` ON `a`.`id` = `d`.`id2`
                  WHERE
                      `d`.`id1` IN (".$temp.") AND `a`.`id` NOT IN (".$temp.")
                  ORDER BY
                      `d`.`sortorder`
                  LIMIT " . $limit;
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