<?php
/**
 * Класс User_Frontend_Model для для работы с пользователями сайта (регистрация,
 * авторизация, добавление/редактирование профилей, история заказов), взаимодействует
 * с базой данных, общедоступная часть сайта. Реализует шаблон проектирования
 * «Наблюдатель», чтобы извещать классы Basket_Frontend_Model, Wished_Frontend_Model,
 * Compare_Frontend_Model и Viewed_Frontend_Model о моменте авторизации посетителя.
 * Это нужно, чтобы синхронизировать эти четыре списка для (еще) не авторизованного
 * посетителя и (уже) авторизованного пользователя. См. описание интерфейса SplSubject
 * http://php.net/manual/ru/class.splsubject.php
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

    /*
     * Уникальный идентификатор посетителя сайта сохраняется в cookie и нужен
     * для работы покупательской корзины, списка отложенных товаров, списка
     * товаров для сравнения и истории просмотренных товаров. По нему можно
     * получить из таблиц БД `baskets`, `wished`, `compare` и `viewed` товары
     * в корзине, отложенные товары, товары в сравнении, просмотренные товары.
     *
     * Если в cookie есть идентификатор посетителя, значит он уже просматривал
     * страницы сайта с этого компьютера. Если идентификатора нет в cookie,
     * значит посетитель на сайте первый раз (и просматривает первую страницу),
     * или зашел с другого компьютера. В этом случае записываем в cookie новый
     * идентификатор.
     *
     * Если в cookie не было идентификатора посетителя и ему был записан новый
     * идентификатор, это еще не означает, что посетитель здесь в первый раз.
     * Он мог зайти с другого компьютера, удалить cookie или истекло время жизни
     * cookie.
     *
     * Сразу после авторизации проверяем — совпадает временный идентификатор
     * посетителя (который сохранен в cookie) с постоянным (который хранится в
     * в БД `users`). Если совпадает — ничего не делаем, если нет — записываем
     * в cookie вместо временного постоянный идентификатор и обновляем записи
     * таблиц БД `baskets`, `wished`, `compare` и `viewed`, заменяя временный
     * идентификатор на постоянный.
     */
    private $visitorId;

    /**
     * наблюдатели за моментом авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    private $observers;


    public function __construct() {

        parent::__construct();

        // пользователь авторизован?
        if (isset($_SESSION['zcmsAuthUser'])) {
            $this->authUser = true;
            $this->userId = $_SESSION['zcmsAuthUser'];
            $this->user = $this->getUser();
        }

        // устанавливаем уникальный идентификатор посетителя сайта
        $this->setVisitorId();

    }

    /**
     * Функция генерирует уникальный идентификатор посетителя сайта
     */
    private function setVisitorId() {

        $time = 86400 * $this->config->user->cookie;
        // сохранен идентификатор посетителя в cookie?
        if (isset($_COOKIE['visitor']) && preg_match('~^[a-f0-9]{32}$~', $_COOKIE['visitor'])) {
            // обновляем cookie, чтобы идентификатор хранился
            // еще Config::getInstance()->user->cookie дней
            setcookie('visitor', $_COOKIE['visitor'], time() + $time, '/');
            $this->visitorId = $_COOKIE['visitor'];
        } else {
            // идентификатора посетителя нет в cookie,
            // формируем его и сохраняем в cookie
            if ($this->authUser) {
                $this->visitorId = $this->user['visitor_id'];
            } else {
                $this->visitorId = md5(uniqid(mt_rand(), true));
            }
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
     * Функция возвращает тип пользователя или ноль, если пользователь
     * не авторизован
     */
    public function getUserType() {
        if ( ! $this->authUser) {
            throw new Exception('Попытка получить тип не авторизованного пользователя');
        }
        return $this->user['type'];
    }

    /**
     * Функция возвращает уникальный идентификатор авторизованного
     * пользователя
     */
    public function getUserId() {
        if ( ! $this->authUser) {
            throw new Exception('Попытка получить идентификатор не авторизованного пользователя');
        }
        return $this->userId;
    }

    /**
     * Функция возвращает адрес электронной почты авторизованного
     * пользователя
     */
    public function getUserEmail() {
        if ( ! $this->authUser) {
            throw new Exception('Попытка получить e-mail не авторизованного пользователя');
        }
        return $this->user['email'];
    }

    /**
     * Добавить наблюдателя за событием авторизации посетителя,
     * реализация шаблона проектирования «Наблюдатель»
     */
    public function attach(SplObserver $observer) {
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

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить данные не авторизованного пользователя');
        }

        if ( ! isset($this->user)) {
            $query = "SELECT
                          `id`, `name`, `patronymic`, `surname`,
                          `email`, `type`, `visitor_id`, `newuser`
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
     * true, если пользователь с таким e-mail уже есть в таблице `users`
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

        /*
         * Проверка, что одним компом не пользуются два человека. Если запрос ниже
         * что-то возвращает, значит с этого компа кто-то уже регистрировался на
         * сайте. Надо присвоить этому пользователю новый идентификатор для записи
         * в таблицу БД `users` и для сохранения в cookie.
         */
        $query = "SELECT 1 FROM `users` WHERE `visitor_id` = :visitor_id";
        $res = $this->database->fetchOne(
            $query,
            array(
                'visitor_id' => $this->visitorId
            )
        );
        if ($res) { // запрос что-то вернул, нужен новый идентификатор
            $this->visitorId = md5(uniqid(mt_rand(), true));
            $time = 86400 * $this->config->user->cookie;
            setcookie('visitor', $this->visitorId, time() + $time, '/');
        }

        /*
         * Добавляем нового пользователя
         */
        $query = "INSERT INTO `users`
                  (
                      `name`,
                      `patronymic`,
                      `surname`,
                      `email`,
                      `type`,
                      `password`,
                      `visitor_id`,
                      `newuser`
                  )
                  VALUES
                  (
                      :name,
                      :patronymic,
                      :surname,
                      :email,
                      0,
                      :password,
                      :visitor_id,
                      5
                  )";

        $data['visitor_id'] = $this->visitorId;
        $this->database->execute($query, $data);

        /*
         * Сразу авторизуем пользователя
         */
        $this->authUser = true;
        $_SESSION['zcmsAuthUser'] = $this->database->lastInsertId();
        $this->userId = $_SESSION['zcmsAuthUser'];
        $this->user = $this->getUser();

    }

    /**
     * Функция обновляет личную информацию пользователя (имя, фамилия, пароль)
     */
    public function updateUser($data) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка обновить данные не авторизованного пользователя');
        }

        if ($data['change']) { // изменяем пароль
            $query = "UPDATE
                          `users`
                      SET
                          `name`       = :name,
                          `patronymic` = :patronymic,
                          `surname`    = :surname,
                          `password`   = :password
                      WHERE
                          `id` = :id";
        } else { // пароль изменять не нужно
            $query = "UPDATE
                          `users`
                      SET
                          `name`       = :name,
                          `patronymic` = :patronymic,
                          `surname`    = :surname
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

        if ( ! $this->authUser) {
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
        $this->userId   = $res;
        $this->user     = $this->getUser();

        /*
         * Записываем в cookie уникальный идентификатор пользователя, вместо временного
         * идентификатора посетителя. Учитываем, что у посетителя может быть два аккаунта
         * или что он мог войти под чужой учетной записью.
         */
        $query = "SELECT 1 FROM `users` WHERE `visitor_id` = :visitor_id AND `id` <> :user_id";
        $res = $this->database->fetchOne(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                'user_id'    => $this->userId
            )
        );
        // постоянный идентификатор пользователя вместо временного идентификатора посетителя
        $this->visitorId = $this->user['visitor_id'];
        $time = 86400 * $this->config->user->cookie;
        setcookie('visitor', $this->visitorId, time() + $time, '/');
        /*
         * Если запрос что-то вернул, значит у посетителя два или более аккаунтов или он
         * вошел под чужой учетной записью. В этом случае мы не объединяем корзины и т.п.
         * чтобы не создавать «мешанину» из двух аккаунтов.
         */
        if (false === $res) {
            /*
             * Известить наблюдателей о событии авторизации посетителя, чтобы они
             * синхронизировали корзины, отложенные товары, товары для сравнения и
             * просмотренные товары. Реализация шаблона проектирования «Наблюдатель»,
             * см. описание здесь http://php.net/manual/ru/class.splobserver.php
             */
            $this->notify();
        }

        /*
         * Запомнить пользователя, чтобы входить автоматически?
         */
        if ($remember) {

            /*
             * Описание механизма работы функционала «Запомнить меня» см. в
             * комментариях к методу autoLogin()
             */

            $token1 = md5(uniqid(mt_rand(), true));
            $token2 = md5(uniqid(mt_rand(), true));

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
            if (mt_rand(1, 100) === 50) {
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

        if ( ! $this->authUser) {
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

        if ( ! isset($_COOKIE['remember'])) {
            return false;
        }
        if ( ! isset($_COOKIE['visitor'])) {
            return false;
        }
        if ( ! preg_match('~^[a-f0-9]{64}$~', $_COOKIE['remember'])) {
            return false;
        }
        if ( ! preg_match('~^[a-f0-9]{32}$~', $_COOKIE['visitor'])) {
            return false;
        }

        /*
         * У пользователя в cookie с именем remember сохраняются token1 и token2,
         * мы сверяем их с теми, что представлены в таблице БД remember. Если они
         * совпадают, то авторизация успешна. Пользователь получает новый token1
         * с предыдущим token2. Если token2 совпадают, а token1 не совпадают, то
         * удаляем все записи в таблице remember со значением token2:
         * DELETE FROM `remember` WHERE `token2` = :token2
         * Потому как злоумышленник заходил в аккаунт, используя cookie, похищенный
         * с этого компьютера. Если пользователь заходит на сайт автоматически еще
         * и с других компьютеров, он сможет с них заходить и далее. А на этом ему
         * надо авторизоваться, введя e-mail, пароль и отметить checkbox «Запомнить
         * меня».
         */

        $token1 = substr($_COOKIE['remember'], 0, 32);
        $token2 = substr($_COOKIE['remember'], 32);

        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `users` `a` INNER JOIN `remember` `b`
                      ON `a`.`id` = `b`.`user_id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id
                      AND `b`.`token1` = :token1
                      AND `b`.`token2` = :token2";
        $res = $this->database->fetchOne(
            $query,
            array(
                'visitor_id' => $_COOKIE['visitor'],
                'token1'     => $token1,
                'token2'     => $token2
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
        $this->userId   = $res;
        $this->user     = $this->getUser();

        // обновляем запись в таблице БД remember
        $token1 = md5(uniqid(mt_rand(), true)); // новое значение token1
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

    }

    /**
     * Функция возвращает массив профилей авторизованного пользователя
     */
    public function getAllProfiles() {

        if ( ! $this->authUser) {
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
     * Функция возвращает массив профилей авторизованного пользователя
     */
    public function getUserProfiles() {

        if ( ! $this->authUser) {
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
        return $this->database->fetchAll($query, array('user_id' => $this->userId));

    }

    /**
     * Возвращает информацию о профиле с уникальным идентификатором $id
     */
    public function getProfile($id) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить профиль не авторизованного пользователя');
        }

        $query = "SELECT
                      `title`, `name`, `surname`, `patronymic`, `email`, `phone`, `shipping`,
                      `shipping_address`, `shipping_city`, `shipping_index`, `company`,
                      `company_name`, `company_ceo`, `company_address`, `company_inn`,
                      `company_kpp`, `bank_name`, `bank_bik`, `settl_acc`, `corr_acc`
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

        if ( ! $this->authUser) {
            throw new Exception('Попытка добавить профиль для не авторизованного пользователя');
        }

        $query = "INSERT INTO `profiles`
                  (
                      `user_id`,
                      `title`,
                      `name`,
                      `patronymic`,
                      `surname`,
                      `email`,
                      `phone`,
                      `shipping`,
                      `shipping_address`,
                      `shipping_city`,
                      `shipping_index`,
                      `company`,
                      `company_name`,
                      `company_ceo`,
                      `company_address`,
                      `company_inn`,
                      `company_kpp`,
                      `bank_name`,
                      `bank_bik`,
                      `settl_acc`,
                      `corr_acc`
                  )
                  VALUES
                  (
                      :user_id,
                      :title,
                      :name,
                      :patronymic,
                      :surname,
                      :email,
                      :phone,
                      :shipping,
                      :shipping_address,
                      :shipping_city,
                      :shipping_index,
                      :company,
                      :company_name,
                      :company_ceo,
                      :company_address,
                      :company_inn,
                      :company_kpp,
                      :bank_name,
                      :bank_bik,
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

        if ( ! $this->authUser) {
            throw new Exception('Попытка обновить профиль не авторизованного пользователя');
        }

        $query = "UPDATE
                      `profiles`
                  SET
                      `title`            = :title,
                      `name`             = :name,
                      `patronymic`       = :patronymic,
                      `surname`          = :surname,
                      `email`            = :email,
                      `phone`            = :phone,
                      `shipping`         = :shipping,
                      `shipping_address` = :shipping_address,
                      `shipping_city`    = :shipping_city,
                      `shipping_index`   = :shipping_index,
                      `company`          = :company,
                      `company_name`     = :company_name,
                      `company_ceo`      = :company_ceo,
                      `company_address`  = :company_address,
                      `company_inn`      = :company_inn,
                      `company_kpp`      = :company_kpp,
                      `bank_name`        = :bank_name,
                      `bank_bik`         = :bank_bik,
                      `settl_acc`        = :settl_acc,
                      `corr_acc`         = :corr_acc
                  WHERE
                      `id` = :id AND user_id = :user_id";
        $data['user_id'] = $this->userId;
        $this->database->execute($query, $data);

    }

    /**
     * Функция удаляет профиль пользователя (запись в таблице profiles базы данных)
     */
    public function removeProfile($id) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка удалить профиль не авторизованного пользователя');
        }

        $query = "DELETE FROM
                      `profiles`
                  WHERE
                      `id` = :id AND `user_id` = :user_id";
        $this->database->execute($query, array('id' => $id, 'user_id' => $this->userId));

    }

    /**
     * Функция возвращает ошибки, которые были допущены при создании профилей; эти
     * ошибки возможны, потому что много пользователей импортировано из Magento
     */
    public function getProfilesErrors() {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить ошибки профилей не авторизованного пользователя');
        }

        $errors = array();
        $query = "SELECT
                      *
                  FROM
                      `profiles`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `id`";
        $profiles = $this->database->fetchAll($query, array('user_id' => $this->userId));
        if (empty($profiles)) {
            return $errors;
        }

        foreach ($profiles as $profile) {
            $messages = $this->getProfileErrors($profile);
            if ( ! empty($messages)) {
                $errors[] = array(
                    'title' => empty($profile['title']) ? 'Без названия' : $profile['title'],
                    'messages' => $messages
                );
            }
        }

        return $errors;

    }

    /**
     * Функция возвращает ошибки, которые были допущены при создании профиля; эти
     * ошибки возможны, потому что много пользователей импортировано из Magento
     */
    private function getProfileErrors($data) {

        $errors = array();

        if (empty($data['title'])) {
            $errors[] = 'Не заполнено обязательное поле «Название профиля»';
        }
        if (empty($data['surname'])) {
            $errors[] = 'Не заполнено обязательное поле «Фамилия контактного лица»';
        }
        if (empty($data['name'])) {
            $errors[] = 'Не заполнено обязательное поле «Имя контактного лица»';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Не заполнено обязательное поле «Телефон контактного лица»';
        }
        if (empty($data['email'])) {
            $errors[] = 'Не заполнено обязательное поле «E-mail контактного лица»';
        } elseif ( ! preg_match('#^[_0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z][0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errors[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        }
        if ($data['company']) { // для юридического лица

            if (empty($data['company_inn'])) {
                $errors[] = 'Не заполнено обязательное поле «ИНН»';
            } elseif ( ! preg_match('#^(\d{10}|\d{12})$#i', $data['company_inn'])) {
                $errors[] = 'Поле «ИНН» должно содержать 10 или 12 цифр';
            }
            if ( ! empty($data['company_kpp'])) {
                if ( ! preg_match('#^\d{9}$#i', $data['company_kpp'])) {
                    $errors[] = 'Поле «КПП» должно содержать 9 цифр';
                }
            }
            if ( ! empty($data['bank_bik'])) {
                if ( ! preg_match('#^\d{9}$#i', $data['bank_bik'])) {
                    $errors[] = 'Поле «БИК банка» должно содержать 9 цифр';
                }
            }
            if ( ! empty($data['settl_acc'])) {
                if ( ! preg_match('#^\d{20}$#i', $data['settl_acc'])) {
                    $errors[] = 'Поле «Расчетный счет» должно содержать 20 цифр';
                }
            }
            if ( ! empty($data['corr_acc'])) {
                if ( ! preg_match('#^\d{20}$#i', $data['corr_acc'])) {
                    $errors[] = 'Поле «Корреспондентский счет» должно содержать 20 цифр';
                }
            }
        }
        if ( ! $data['shipping']) {
            if (empty($data['shipping_address'])) {
                $errors[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
            if ( ! empty($data['shipping_index'])) {
                if ( ! preg_match('#^\d{6}$#i', $data['shipping_index'])) {
                    $errors[] = 'Поле «Почтовый индекс» должно содержать 6 цифр';
                }
            }
        }

        return $errors;

    }

    /**
     * Функция изменяет пароль и отправляет письмо с новым паролем на e-mail пользователя
     */
    public function newPassword($email) {

        // формируем новый пароль
        $chars = '@#$%0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < 10; $i++) {
            $password .= $chars[rand(0, 65)];
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

        /*
         * отправляем новый пароль по почте
         */
        $subject = '=?utf-8?B?' . base64_encode('Новый пароль') . '?=';

        $name  = $this->config->site->name;
        $mail  = $this->config->email->site;
        $phone = $this->config->site->phone;

        $headers  = 'From: =?utf-8?b?' . base64_encode($name) . '?= <' . $mail . '>' . "\r\n";
        $headers .= 'Date: ' . date('r') . "\r\n";
        $headers .= 'Content-type: text/plain; charset="utf-8"' . "\r\n";
        $headers .= 'Content-Transfer-Encoding: base64';

        $message  = 'Добрый день!' . "\r\n\r\n";
        $message .= 'Ваш новый пароль: ' . $password . "\r\n\r\n";
        $message .= $name . "\r\n";
        $message .= 'Телефон: ' . $phone . "\r\n";
        $message .= 'Почта: ' . $mail;
        $message  = chunk_split(base64_encode($message));

        mail($email, $subject, $message, $headers);

    }

    /**
     * Функция возвращает массив заказов авторизованного пользователя
     */
    public function getAllOrders($start = 0) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить заказы не авторизованного пользователя');
        }

        $query = "SELECT
                      `id` AS `order_id`, `amount`, `user_amount`, DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i') AS `time`, `status`
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `added` DESC
                  LIMIT
                      :start, :limit";
        $orders = $this->database->fetchAll(
            $query,
            array(
                'user_id' => $this->userId,
                'start'   => $start,
                'limit'   => $this->config->pager->frontend->orders->perpage
            )
        );
        // добавляем в массив информацию о товарах для каждого заказа
        foreach ($orders as $key => $value) {
            $orders[$key]['repeat'] = false;
            $query = "SELECT
                          `a`.`product_id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                          `a`.`title` AS `title`, `a`.`price` AS `price`, `a`.`user_price` AS `user_price`,
                          `a`.`unit` AS `unit`, `a`.`quantity` AS `quantity`, `a`.`cost` AS `cost`,
                          `a`.`user_cost` AS `user_cost`, !ISNULL(`b`.`id`) AS `exists`
                      FROM
                          `orders_prds` `a` LEFT JOIN `products` `b`
                          ON `a`.`product_id` = `b`.`id` AND `b`.`visible` = 1
                      WHERE
                          `a`.`order_id` = :order_id
                      ORDER BY
                          `a`.`id`";
            $orders[$key]['products'] = $this->database->fetchAll($query, array('order_id' => $value['order_id']));
            foreach ($orders[$key]['products'] as $k => $v) {
                if ($v['exists']) { // если товар еще не удален из каталога
                    $orders[$key]['products'][$k]['url'] = $this->getURL('frontend/catalog/product/id/' . $v['id']);
                    $orders[$key]['repeat'] = true;
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
     * Функция возвращает массив заказов авторизованного пользователя
     */
    public function getAllOrdersNew($start = 0) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить заказы не авторизованного пользователя');
        }

        /*
         * получаем идентификаторы заказов пользователя
         */
        $query = "SELECT
                      `id`
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `added` DESC
                  LIMIT
                      :start, :limit";
        $items = $this->database->fetchAll(
            $query,
            array(
                'user_id' => $this->userId,
                'start'   => $start,
                'limit'   => $this->config->pager->frontend->orders->perpage
            )
        );
        if (empty($items)) {
            return array();
        }
        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item['id'];
        }
        $ids = implode(',', $ids);

        /*
         * получаем заказы пользователя и товары каждого заказа
         */
        $query = "SELECT
                      `a`.`id` AS `order_id`, `a`.`amount` AS `order_amount`, `a`.`user_amount` AS
                      `order_user_amount`, DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `order_date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i') AS `order_time`, `a`.`status` AS `order_status`,
                      `b`.`product_id` AS `product_id`, `b`.`code` AS `product_code`,
                      `b`.`name` AS `product_name`, `b`.`title` AS `product_title`, `b`.`price` AS
                      `product_price`, `b`.`user_price` AS `product_user_price`, `b`.`unit` AS
                      `product_unit`, `b`.`quantity` AS `product_quantity`, `b`.`cost` AS `product_cost`,
                      `b`.`user_cost` AS `product_user_cost`, !ISNULL(`c`.`id`) AS `product_exists`
                  FROM
                      `orders` `a`
                      INNER JOIN `orders_prds` `b` ON `a`.`id` = `b`.`order_id`
                      LEFT JOIN `products` `c` ON `b`.`product_id` = `c`.`id` AND `c`.`visible` = 1
                  WHERE
                      `a`.`id` IN (" . $ids . ")
                  ORDER BY
                      `a`.`added` DESC, `b`.`id` ASC";
        $result = $this->database->fetchAll($query);

        /*
         * преобразуем полученные данные к виду, удобному для вывода в шаблоне
         */
        $orders = array();
        $order_id = 0;
        $counter = -1;
        foreach ($result as $value) {
            if ($order_id != $value['order_id']) {
                $counter++;
                $order_id = $value['order_id'];
                $orders[$counter] = array(
                    'order_id'          => $value['order_id'],
                    'order_amount'      => $value['order_amount'],
                    'order_user_amount' => $value['order_user_amount'],
                    'order_date'        => $value['order_date'],
                    'order_time'        => $value['order_time'],
                    'order_status'      => $value['order_status'],
                    // URL ссылки для просмотра подробной информации о заказе
                    'url'    => $this->getURL('frontend/user/order/id/' . $value['order_id']),
                    // атрибут action тега form для повторения заказа
                    'action' => $this->getURL('frontend/user/repeat/id/' . $value['order_id'])
                );
                if ($counter) { // возможность повторить заказ
                    $orders[$counter-1]['repeat'] = $repeat;
                }
                $repeat = false;
            }
            $orders[$counter]['products'][] = array(
                'product_id'         => $value['product_id'],
                'product_code'       => $value['product_code'],
                'product_name'       => $value['product_name'],
                'product_title'      => $value['product_title'],
                'product_price'      => $value['product_price'],
                'product_user_price' => $value['product_user_price'],
                'product_unit'       => $value['product_unit'],
                'product_quantity'   => $value['product_quantity'],
                'product_cost'       => $value['product_cost'],
                'product_user_cost'  => $value['product_user_cost'],
                'product_exists'     => $value['product_exists'],
                'product_url'        => $this->getURL('frontend/catalog/product/id/' . $v['id'])
            );
            if ($value['product_exists']) { // товар еще не удален из каталога?
                // если есть хоть один товар в каталоге, заказ можно повторить
                $repeat = true;
            }
        }
        $orders[$counter]['repeat'] = $repeat;

        return $orders;

    }

    /**
     * Возвращает общее кол-во заказов авторизованного пользователя
     */
    public function getCountAllOrders() {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить кол-во заказов не авторизованного пользователя');
        }

        $query = "SELECT
                      COUNT(*)
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id";
        return $this->database->fetchOne($query, array('user_id' => $this->userId));

    }

    /**
     * Функция возвращает информацию об отдельном заказе авторизованного пользователя
     */
    public function getOrder($id) {

        if ( ! $this->authUser) {
            throw new Exception('Попытка получить заказ не авторизованного пользователя');
        }

        /*
         * общая информация о заказе (сумма, покупатель и плательщик, статус)
         */
        $query = "SELECT
                      `amount`, `user_amount`, `details`, `status`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `orders`
                  WHERE
                      `id` = :order_id AND `user_id` = :user_id";
        $result = $this->database->fetch(
            $query,
            array('order_id' => $id, 'user_id' => $this->userId)
        );
        if (false === $result) {
            return null;
        }
        $details = array();
        if ( ! empty($result['details'])) {
            $details = unserialize($result['details']);
        }
        $order = array_merge(
            array(
                'order_id' => $id,
                'amount' => $result['amount'],
                'user_amount' => $result['user_amount'],
                'date' => $result['date'],
                'time' => $result['time'],
                'status' => $result['status']
            ),
            $details
        );

        /*
         * добавляем информацию о списке товаров заказа
         */
        $query = "SELECT
                      `a`.`product_id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`title` AS `title`, `a`.`price` AS `price`, `a`.`user_price` AS `user_price`,
                      `a`.`unit` AS `unit`, `a`.`quantity` AS `quantity`, `a`.`cost` AS `cost`,
                      `a`.`user_cost` AS `user_cost`, !ISNULL(`b`.`id`) AS `exists`
                  FROM
                      `orders_prds` `a` LEFT JOIN `products` `b`
                      ON `a`.`product_id` = `b`.`id` AND `b`.`visible` = 1
                  WHERE
                      `a`.`order_id` = :order_id
                  ORDER BY
                      `a`.`id`";
        $order['products'] = $this->database->fetchAll($query, array('order_id' => $id));
        $order['repeat'] = false; // признак того, что пользователь может повторить заказ
        foreach ($order['products'] as $k => $v) {
            if ($v['exists']) { // если товар еще не удален из каталога
                $order['products'][$k]['url'] = $this->getURL('frontend/catalog/product/id/' . $v['id']);
                $order['repeat'] = true;
            }
        }

        return $order;

    }

    /**
     * Функция добавляет в корзину все товары ранее сделанного заказа
     */
    public function repeatOrder($id) {

        if ( ! $this->authUser) {
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
                      INNER JOIN `orders` `e` ON `a`.`order_id` = `e`.`id`
                  WHERE
                      `a`.`order_id` = :order_id AND `e`.`user_id` = :user_id AND `b`.`visible` = 1
                  ORDER BY
                      `a`.`id`";
        $products = $this->database->fetchAll(
            $query,
            array('order_id' => $id, 'user_id' => $this->userId)
        );

        // через реестр обращаемся к экземпляру класса Basket_Frontend_Model
        // и добавляем эти товары в корзину
        $basketFrontendModel = $this->register->basketFrontendModel;
        foreach ($products as $key => $value) {
            $basketFrontendModel->addToBasket($value['id'], $value['count'], $key);
        }

    }

    /*
     * Функция возвращает информацию о последнем заказе пользователя не
     * авторизованного пользователя, если таковой имеется
     */
    public function getLastOrderData() {

        if ($this->authUser) {
            return array();
        }
        $query = "SELECT
                      `details`
                  FROM
                      `orders`
                  WHERE
                      `visitor_id` = :visitor_id AND
                      `details` <> ''
                  ORDER BY
                      `added` DESC
                  LIMIT
                      1";
        $result = $this->database->fetchOne(
            $query,
            array('visitor_id' => $this->visitorId)
        );
        if (false === $result) {
            return array();
        }

        return unserialize($result);

    }

}
