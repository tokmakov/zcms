<?php
/**
 * Класс User_Frontend_Model для для работы с пользователями сайта (регистрация,
 * авторизация, добавление/редактирование профилей, история заказов), взаимодействует
 * с базой данных, общедоступная часть сайта. Реализует шаблон проектирования
 * «Наблюдатель», чтобы извещать классы Basket_Frontend_Model, Wished_Frontend_MOdel,
 * Compared_Frontend_Model и Viewed_Frontend_Model о моменте авторизации посетителя.
 * Это нужно, чтобы синхронизировать эти четыре списка для (еще) не авторизованного
 * посетителя и (уже) авторизованного пользователя.
 */
class User_Frontend_Model extends Frontend_Model implements SplSubject {

    /**
     * посетитель авторизован?
     */
    private $authUser = false;

    /**
     * уникальный идентификатор авторизованного пользователя сайта
     */
    private $userId;

    /**
     * информация об авторизованном пользователе сайта
     */
    private $user;

    /**
     * уникальный идентификатор посетителя сайта, который сохраняется
     * в cookie и нужен для работы покупательской корзины, сохранения
     * списка отложенных товаров, списка товаров для сравнения и истории
     * просмотренных товаров
     */
    private $visitorId;

    /**
     * наблюдатели за моментом авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    private $observers;


    public function __construct() {

        parent::__construct();
        // устанавливаем уникальный идентификатор посетителя сайта
        $this->setVisitorId();
        // пользователь авторизован?
        if (isset($_SESSION['zcmsAuthUser'])) {
            $this->authUser = true;
            $this->userId = $_SESSION['zcmsAuthUser'];
            $this->user = $this->getUser();
        }

    }

    /**
     * Функция генерирует уникальный идентификатор посетителя сайта
     */
    private function setVisitorId() {
        $time = 86400 * $this->config->user->cookie;
        // сохранен идентификатор посетителя в cookie?
        if (isset($_COOKIE['visitor']) && preg_match('~^[a-f0-9]{32}$~', $_COOKIE['visitor'])) {
            // обновляем cookie, чтобы идентификатор хранился еще Config::getInstance()->user->cookie дней
            setcookie('visitor', $_COOKIE['visitor'], time() + $time, '/');
            $this->visitorId = $_COOKIE['visitor'];
        } else { //
            // идентификатора посетителя нет в cookie, формируем его и сохраняем в cookie
            $this->visitorId = md5(uniqid(rand(), true));
            setcookie('visitor', $this->visitorId, time() + $time, '/');
        }
    }

    /**
     * Функция возвращает уникальный идентификатор посетителя сайта
     */
    public function getVisitorId() {
        return $this->visitorId;
    }

    /**
     * Добавить наблюдателя за событием авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    public function attach(SplObserver $observer){
        $this->observers[] = $observer;
    }

    /**
     * Удалить наблюдателя за событием авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    public function detach(SplObserver $observer) {
        $this->observers->detach($observer);
    }

    /**
     * Известить наблюдателей о событии авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    public function notify() {
        foreach($this->observers as $observer){
            $observer->update($this);
        }
    }

    /**
     * Функция возвращает true, если посетитель авторизован
     */
    public function isAuthUser() {
        return $this->authUser;
    }

    /**
     * Функция возвращает данные об авторизованном пользователе
     */
    public function getUser() {

        if (!$this->authUser) {
            throw new Exception('Попытка получить данные не авторизованного пользователя');
        }

        if (!isset($this->user)) {
            $query = "SELECT
                          `id`, `name`, `surname`, `email`, `visitor_id`, `newuser`
                      FROM
                          `users`
                      WHERE
                          `id` = :id
                      LIMIT
                          1";
            $this->user = $this->database->fetch($query, array('id' => $this->userId));
        }

        return $this->user;
    }

    /**
     * Функция проверяет наличие пользователя по его e-mail, возвращает
     * true, если пользователь с таким e-mail уже есть в таблице users
     */
    public function isUserExists($email) {

        $query = "SELECT
                      1
                  FROM
                      `users`
                  WHERE
                      `email` = :email";
        $res = $this->database->fetch($query, array('email' => $email));
        if (false === $res) {
            return false;
        }
        return true;

    }

    /**
     * Регистрация нового пользователя (добавление новой записи в таблицу users БД)
     */
    public function addNewUser($data) {

        $query = "INSERT INTO `users`
                    (
                        `name`,
                        `surname`,
                        `email`,
                        `password`,
                        `visitor_id`,
                        `newuser`
                    )
                    VALUES
                    (
                        :name,
                        :surname,
                        :email,
                        :password,
                        :visitor_id,
                        5
                    )";
        $data['visitor_id'] = md5(uniqid(rand(), true));
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет личную информацию пользователя (имя, фамилия, пароль)
     */
    public function updateUser($data) {

        if (!$this->authUser) {
            throw new Exception('Попытка обновить данные не авторизованного пользователя');
        }

        if ($data['change']) { // изменяем пароль
            $query = "UPDATE
                          `users`
                      SET
                          `name` = :name,
                          `surname` = :surname,
                          `password` = :password
                      WHERE
                          `id` = :id";
        } else { // пароль изменять не нужно
            $query = "UPDATE
                          `users`
                      SET
                          `name` = :name,
                          `surname` = :surname
                      WHERE
                          `id` = :id";
        }
        unset($data['change']);
        $data['id'] = $this->userId;
        $this->database->execute($query, $data);

    }

    /**
     * Функция возвращает true, если пользователь новый
     */
    public function isNewUser() {

        if (!$this->authUser) {
            throw new Exception('Попытка получить статус не авторизованного пользователя');
        }

        if ($this->user['newuser']) {
            $this->user['newuser'] = $this->user['newuser'] - 1;
            $query = "UPDATE
                          `users`
                      SET
                          `newuser` = `newuser` - 1
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('id' => $this->userId));
            return true;
        }

        return false;

    }

    /**
     * Авторизация посетителя, функция проверяет наличие в таблице users
     * пользователя с указанным e-mail и паролем
     */
    public function loginUser($data) {

        if ($this->authUser) {
            throw new Exception('Попытка авторизации уже авторизованного пользователя');
        }

        // запомнить пользователя, чтобы входить автоматически?
        $remember = $data['remember'];
        unset($data['remember']);

        $query = "SELECT
                      `id`
                  FROM
                      `users`
                  WHERE
                      `email` = :email AND `password` = :password
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, $data);
        if (false === $res) {
            return false;
        }

        // авторизация прошла успешно
        $_SESSION['zcmsAuthUser'] = $res;
        $this->authUser = true;
        $this->userId = $res;
        $this->user = $this->getUser();

        // записываем в cookie уникальный идентификатор пользователя,
        // вместо временного идентификатора посетителя
        $this->visitorId = $this->user['visitor_id'];
        $time = 86400 * $this->config->user->cookie;
        setcookie('visitor', $this->visitorId, time() + $time, '/');

        // известить наблюдателей о событии авторизации посетителя, чтобы они
        // синхронизировали корзины, отложенные товары, товары для сравнения и
        // просмотренные товары; реализация шаблона проектирования «Наблюдатель»
        $this->notify();

        // запомнить пользователя, чтобы входить автоматически?
        if ($remember) {

            /*
             * Описание механизма работы функционала «Запомнить меня» см. в
             * комментариях к методу autoLogin()
             */

            $token1 = md5(uniqid(rand(), true));
            $token2 = md5(uniqid(rand(), true));

            // добавляем запись в таблицу БД remember
            $query = "INSERT INTO `remember`
                      (
                          `user_id`,
                          `token1`,
                          `token2`,
                          `updated`
                      )
                      VALUES
                      (
                          :user_id,
                          :token1,
                          :token2,
                          NOW()
                      )";
            $this->database->execute(
                $query,
                array(
                    'user_id' => $this->userId,
                    'token1'  => $token1,
                    'token2'  => $token2
                )
            );

            // устанавливаем cookie, чтобы она хранилась Config::getInstance()->user->cookie
            $time = 86400 * $this->config->user->cookie;
            setcookie('remember', $token1 . $token2, time() + $time, '/');

            // удаляем старые записи в таблице БД remember
            if (rand(1, 100) == 50) {
                $query = "DELETE FROM `remember` WHERE `updated` < NOW() - INTERVAL :days DAY";
                $this->database->execute($query, array('days' => $this->config->user->cookie));
            }

        }

        return true;

    }

    /**
     * Выход из личного кабинета ранее авторизованного пользователя
     */
    public function logoutUser() {

        if (!$this->authUser) {
            throw new Exception('Попытка выхода из личного кабинета не авторизованного пользователя');
        }

        /*
         * Описание механизма работы функционала «Запомнить меня» см. в
         * комментариях к методу autoLogin()
         */
        if (isset($_COOKIE['remember'])) {
            if (preg_match('~^[a-f0-9]{64}$~', $_COOKIE['remember'])) {
                $token2 = substr($_COOKIE['remember'], 32);
                $query = "DELETE FROM `remember` WHERE `token2` = :token2";
                $this->database->execute($query, array('token2' => $token2));
            }
            setcookie ('remember', '', time() - 3600, '/');
        }

        unset($_SESSION['zcmsAuthUser']);
        unset($this->userId);
        unset($this->user);
        $this->authUser = false;

    }

    /**
     * Автоматический вход в личный кабинет для пользователя
     */
    public function autoLogin() {

        if ($this->authUser) {
            throw new Exception('Попытка авторизации уже авторизованного пользователя');
        }

        if (!isset($_COOKIE['remember'])) {
            return false;
        }

        if (!isset($_COOKIE['visitor'])) {
            return false;
        }

        /*
         * У пользователя в cookie с именем remember сохраняются token1 и token2,
         * мы сверяем их с теми, что предоставлены в таблице БД remember. Если они
         * совпадают, то авторизация успешна. Пользователь получает новый token1
         * с предыдущим token2. Если token2 совпадают, а token1 не совпадают, то
         * удаляем все записи в таблице remember со значением token2:
         * DELETE FROM `remember` WHERE `token2` = :token2
         * Потому как злоумышленник заходил в аккаунт, используя cookie, похищенный
         * с этого компьютера. Если пользователь заходит на сайт автоматически еще
         * и с других компьютеров, он сможет с них заходить и далее. А на этом ему
         * надо авторизоваться, введя e-mail и пароль и отметить checkbox «Запомнить
         * меня».
         */
        if (preg_match('~^[a-f0-9]{64}$~', $_COOKIE['remember']) && preg_match('~^[a-f0-9]{32}$~', $_COOKIE['visitor'])) {

            $token1 = substr($_COOKIE['remember'], 0, 32);
            $token2 = substr($_COOKIE['remember'], 32);

            $query = "SELECT
                          `a`.`id` AS `id`
                      FROM
                          `users` `a` INNER JOIN `remember` `b`
                          ON `a`.`id` = `b`.`user_id`
                      WHERE
                          `a`.`visitor_id` = :visitor_id AND `b`.`token1` = :token1 AND `b`.`token2` = :token2";
            $res = $this->database->fetchOne(
                $query,
                array(
                    'visitor_id' => $_COOKIE['visitor'],
                    'token1' => $token1,
                    'token2' => $token2
                )
            );
            if (false === $res) {
                // удаляем запись в таблице БД remember
                $query = "DELETE FROM `remember` WHERE `token2` = :token2";
                $this->database->execute($query, array('token2' => $token2));
                // удаляем cookie с именем remember
                setcookie ('remember', '', time() - 3600, '/');
                return false;
            }

            // авторизация прошла успешно
            $_SESSION['zcmsAuthUser'] = $res;
            $this->authUser = true;
            $this->userId = $res;
            $this->user = $this->getUser();

            // обновляем запись в таблице БД remember
            $token1 = md5(uniqid(rand(), true)); // новое значение token1
            $query = "UPDATE
                          `remember`
                      SET
                          `token1` = :token1,
                          `updated` = NOW()
                      WHERE
                          `token2` = :token2";
            $this->database->execute($query, array('token1' => $token1, 'token2' => $token2));
            // обновляем cookie, чтобы она хранилась еще Config::getInstance()->user->cookie
            $time = 86400 * $this->config->user->cookie;
            setcookie('remember', $token1 . $token2, time() + $time, '/');

            return true;

        } else {

            // удаляем cookie
            setcookie ('remember', '', time() - 3600, '/');

            return false;

        }

    }

    /**
     * Функция возвращает массив профилей авторизованного пользователя
     */
    public function getAllProfiles() {

        if (!$this->authUser) {
            throw new Exception('Попытка получить профили не авторизованного пользователя');
        }

        $query = "SELECT
                      `id`, `title`
                  FROM
                      `profiles`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `id`";
        $profiles = $this->database->fetchAll($query, array('user_id' => $this->userId));
        // добавляем в массив профилей URL ссылок для редактирования и удаления
        foreach($profiles as $key => $value) {
            $profiles[$key]['url'] = array(
                'edit'   => $this->getURL('frontend/user/editprof/id/' . $value['id']),
                'remove' => $this->getURL('frontend/user/rmvprof/id/' . $value['id'])
            );
        }

        return $profiles;

    }

    /**
     * Возвращает информацию о профиле с уникальным идентификатором $id
     */
    public function getProfile($id) {

        if (!$this->authUser) {
            throw new Exception('Попытка получить профиль не авторизованного пользователя');
        }

        $query = "SELECT
                      `title`, `name`, `surname`, `email`, `phone`, `own_shipping`, `physical_address`,
                      `city`, `postal_index`, `legal_person`, `company`, `ceo_name`, `legal_address`,
                      `bank_name`, `inn`, `bik`, `settl_acc`, `corr_acc`
                  FROM
                      `profiles`
                  WHERE
                      `id` = :id AND `user_id` = :user_id";
        return $this->database->fetch($query, array('id' => $id, 'user_id' => $this->userId));

    }

    /**
     * Функция добавляет новый профиль пользователя (новую запись в таблицу profiles БД)
     */
    public function addProfile($data) {

        if (!$this->authUser) {
            throw new Exception('Попытка добавить профиль для не авторизованного пользователя');
        }

        $query = "INSERT INTO `profiles`
                    (
                        `user_id`,
                        `title`,
                        `name`,
                        `surname`,
                        `email`,
                        `phone`,
                        `own_shipping`,
                        `physical_address`,
                        `city`,
                        `postal_index`,
                        `legal_person`,
                        `company`,
                        `ceo_name`,
                        `legal_address`,
                        `bank_name`,
                        `inn`,
                        `bik`,
                        `settl_acc`,
                        `corr_acc`
                    )
                    VALUES
                    (
                        :user_id,
                        :title,
                        :name,
                        :surname,
                        :email,
                        :phone,
                        :own_shipping,
                        :physical_address,
                        :city,
                        :postal_index,
                        :legal_person,
                        :company,
                        :ceo_name,
                        :legal_address,
                        :bank_name,
                        :inn,
                        :bik,
                        :settl_acc,
                        :corr_acc
                    )";
        $data['user_id'] = $this->userId;
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет профиль пользователя (запись в таблице profiles базы данных)
     */
    public function updateProfile($data) {

        if (!$this->authUser) {
            throw new Exception('Попытка обновить профиль не авторизованного пользователя');
        }

        $query = "UPDATE
                      `profiles`
                  SET
                      `title` = :title,
                      `name` = :name,
                      `surname` = :surname,
                      `email` = :email,
                      `phone` = :phone,
                      `own_shipping` = :own_shipping,
                      `physical_address` = :physical_address,
                      `city` = :city,
                      `postal_index` = :postal_index,
                      `legal_person` = :legal_person,
                      `company` = :company,
                      `ceo_name` = :ceo_name,
                      `legal_address` = :legal_address,
                      `bank_name` = :bank_name,
                      `inn` = :inn,
                      `bik` = :bik,
                      `settl_acc` = :settl_acc,
                      `corr_acc` = :corr_acc
                  WHERE
                      `id` = :id AND user_id = :user_id";
        $data['user_id'] = $this->userId;
        $this->database->execute($query, $data);

    }

    /**
     * Функция удаляет профиль пользователя (запись в таблице profiles базы данных)
     */
    public function removeProfile($id) {

        if (!$this->authUser) {
            throw new Exception('Попытка удалить профиль не авторизованного пользователя');
        }

        $query = "DELETE FROM
                      `profiles`
                  WHERE
                      `id` = :id AND `user_id` = :user_id";
        $this->database->execute($query, array('id' => $id, 'user_id' => $this->userId));

    }

    /**
     * Функция изменяет пароль и отправляет письмо с новым паролем на e-mail пользователя
     */
    public function newPassword($email) {

        // формируем новый пароль
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < 10; $i++) {
            $password .= $chars[rand(0, 61)];
        }

        // добавляем к паролю префикс и хэшируем пароль
        $md5 = md5($this->config->user->prefix . $password);

        // изменяем пароль в таблице БД
        $query = "UPDATE
                      `users`
                  SET
                      `password` = :password
                  WHERE
                      `email` = :email";
        $this->database->execute($query, array('password' => $md5, 'email' => $email));

        // отправляем новый пароль по почте
        $subject = '=?utf-8?B?' . base64_encode('Новый пароль') . '?=';
        $headers = 'From: <' . $this->config->email->site . '>' . "\r\n";
        $headers = $headers.'Date: ' . date('r') . "\r\n";
        $headers = $headers.'Content-type: text/plain; charset="utf-8"' . "\r\n";
        $headers = $headers.'Content-Transfer-Encoding: base64';
        $message = 'Добрый день!' . "\r\n\r\n";
        $message .= 'Ваш новый пароль: ' . $password . "\r\n\r\n";
        $message .= $this->config->site->name;
        $message = chunk_split(base64_encode($message));
        mail($email, $subject, $message, $headers);

    }

    /**
     * Функция возвращает массив заказов авторизованного пользователя
     */
    public function getAllOrders($start = 0) {

        if (!$this->authUser) {
            throw new Exception('Попытка получить заказы не авторизованного пользователя');
        }

        $query = "SELECT
                      `id` AS `order_id`, `amount`, DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`, `status`
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->orders->perpage;
        $orders = $this->database->fetchAll($query, array('user_id' => $this->userId));
        // добавляем в массив информацию о товарах для каждого заказа
        foreach ($orders as $key => $value) {
            $query = "SELECT
                          `a`.`product_id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                          `a`.`title` AS `title`, `a`.`price` AS `price`, `a`.`quantity` AS `quantity`,
                          `a`.`cost` AS `cost`, !ISNULL(`b`.`id`) AS `exists`
                      FROM
                          `orders_prds` `a` LEFT JOIN `products` `b`
                          ON `a`.`product_id` = `b`.`id`
                      WHERE
                          `a`.`order_id` = :order_id  AND `b`.`visible` = 1
                      ORDER BY
                          `a`.`id`";
            $orders[$key]['products'] = $this->database->fetchAll($query, array('order_id' => $value['order_id']));
            foreach ($orders[$key]['products'] as $k => $v) {
                if ($v['exists']) { // если товар еще не удален из каталога
                    $orders[$key]['products'][$k]['url'] = $this->getURL('frontend/catalog/product/id/' . $v['id']);
                }
            }
            // URL ссылки для просмотра подробной информации о заказе
            $orders[$key]['url'] = $this->getURL('frontend/user/order/id/' . $value['order_id']);
            // атрибут action тега form для повторения заказа
            $orders[$key]['action'] = $this->getURL('frontend/user/repeat/id/' . $value['order_id']);
        }

        return $orders;

    }

    /**
     * Возвращает общее кол-во заказов авторизованного пользователя
     */
    public function getCountAllOrders() {

        if (!$this->authUser) {
            throw new Exception('Попытка получить кол-во заказов не авторизованного пользователя');
        }

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id";
        $res = $this->database->fetchOne($query, array('user_id' => $this->userId));
        return $res;

    }

    /**
     * Функция возвращает информацию об отдельном заказе авторизованного пользователя
     */
    public function getOrder($id) {

        if (!$this->authUser) {
            throw new Exception('Попытка получить заказ не авторизованного пользователя');
        }

        // общая информация о заказе (сумма, покупатель и плательщик, статус)
        $query = "SELECT
                      `amount`, `details`, `status`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `orders`
                  WHERE
                      `id` = :order_id AND `user_id` = :user_id";
        $result = $this->database->fetch($query, array('order_id' => $id, 'user_id' => $this->userId));
        if (false === $result) {
            return null;
        }
        $order = array_merge(
            array(
                'order_id' => $id,
                'amount' => $result['amount'],
                'date' => $result['date'],
                'time' => $result['time'],
                'status' => $result['status']
            ),
            unserialize($result['details'])
        );
        // добавляем информацию о списке товаров заказа
        $query = "SELECT
                      `product_id`, `code`, `name`, `title`, `price`, `quantity`, `cost`
                  FROM
                      `orders_prds`
                  WHERE
                      `order_id` = :order_id
                  ORDER BY
                      `id`";
        $result = $this->database->fetchAll($query, array('order_id' => $id));
        $products = array();
        foreach($result as $item) {
            $products[] = array(
                'product_id' => $item['product_id'],
                'code' => $item['code'],
                'name' => $item['name'],
                'title' => $item['title'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'cost' => $item['cost'],
            );
        }
        $order['products'] = $products;

        return $order;

    }

    /**
     * Функция добавляет в корзину все товары ранее сделанного заказа
     */
    public function repeatOrder($id) {

        if (!$this->authUser) {
            throw new Exception('Попытка повторить заказ от не авторизованного пользователя');
        }

        // получаем все товары ранее сделанного заказа
        $query = "SELECT
                      `a`.`product_id` AS `id`, `a`.`quantity` AS `count`
                  FROM
                      `orders_prds` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `orders` `e` ON `a`.`order_id` = `d`.`id`
                  WHERE
                      `a`.`order_id` = :order_id AND `e`.`user_id` = :user_id AND `b`.`visible` = 1
                  ORDER BY
                      `a`.`id`";
        $result = $this->database->fetchAll($query, array('order_id' => $id, 'user_id' => $this->userId));

        // через реестр обращаемся к экземпляру класса Basket_Frontend_Model
        // и добавляем эти товары в корзину
        $basketFrontendModel = $this->register->basketFrontendModel;
        foreach ($result as $key => $value) {
            $basketFrontendModel->addToBasket($value['id'], $value['count'], $key);
        }

    }

}
