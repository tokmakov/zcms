<?php
/**
 * Класс User_Backend_Model для работы с пользователями, взаимодействует
 * с базой данных, административная часть сайта
 */
class User_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех пользователей
     */
    public function getAllUsers($start) {
        $query = "SELECT
                      `id`, `name`, `patronymic`, `surname`, `email`
                  FROM
                      `users`
                  WHERE
                      1
                  ORDER BY
                      `surname`
                  LIMIT " . $start . ", " . $this->config->pager->backend->users->perpage;
        $users = $this->database->fetchAll($query);
        // добавляем в массив пользователей ссылки для просмотра, редактирования, удаления
        foreach ($users as $key => $value) {
            $users[$key]['url']['show'] = $this->getURL('backend/user/show/id/' . $value['id']);
            $users[$key]['url']['edit'] = $this->getURL('backend/user/edit/id/' . $value['id']);
            $users[$key]['url']['remove'] = $this->getURL('backend/user/remove/id/' . $value['id']);
        }
        return $users;
    }

    /**
     * Возвращает общее количество пользователей
     */
    public function getCountAllUsers() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `users`
                  WHERE
                      1";
        return $this->database->fetchOne($query);
    }

    /**
     * Возвращает информацию о пользователе с уникальным идентификатором $id
     */
    public function getUser($id) {
        $query = "SELECT
                      `name`, `patronymic`, `surname`, `email`, `type`
                  FROM
                      `users`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает массив заказов пользователя с уникальным идентификатором $id
     */
    public function getUserOrders($id, $start = 0) {

        $query = "SELECT
                         `id` AS `order_id`, `amount`, DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                         DATE_FORMAT(`added`, '%H:%i:%s') AS `time`, `status`
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->orders->perpage;
        $orders = $this->database->fetchAll($query, array('user_id' => $id));

        foreach ($orders as $key => $value) {
            $query = "SELECT
                          `product_id`, `code`, `name`, `title`,
                          `price`, `quantity`, `cost`
                      FROM
                          `orders_prds`
                      WHERE
                          `order_id` = :order_id
                      ORDER BY
                          `id`";
            // добавляем в массив список товаров для каждого заказа
            $orders[$key]['products'] = $this->database->fetchAll($query, array('order_id' => $value['order_id']));
            // добавляем в массив URL ссылок для просмотра подробной информации о заказе
            $orders[$key]['url'] = $this->getURL('backend/order/show/id/' . $value['order_id']);
        }

        return $orders;

    }

    /**
     * Возвращает кол-во заказов пользователя с уникальным идентификатором $id
     */
    public function getCountUserOrders($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `orders`
                  WHERE
                      `user_id` = :user_id";
        return $this->database->fetchOne($query, array('user_id' => $id));
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
     * Функция добавляет пользователя (новую запись в таблицу users базы данных)
     */
    public function addNewUser($data) {
        $query = "INSERT INTO `users`
                    (
                        `name`,
                        `patronymic`,
                        `surname`,
                        `email`,
                        `type`,
                        `password`,
                        `visitor_id`
                    )
                    VALUES
                    (
                        :name,
                        :patronymic,
                        :surname,
                        :email,
                        :type,
                        :password,
                        :visitor_id
                    )";
        $data['visitor_id'] = md5(uniqid(rand(), true));
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет личную информацию пользователя (имя, фамилия, пароль)
     */
    public function updateUser($data) {
        if ($data['change']) { // изменяем пароль
            $query = "UPDATE
                          `users`
                      SET
                          `name`       = :name,
                          `patronymic` = :patronymic,
                          `surname`    = :surname,
                          `type`       = :type,
                          `password`   = :password
                      WHERE
                          `id` = :id";
        } else { // пароль изменять не нужно
            $query = "UPDATE
                          `users`
                      SET
                          `name`       = :name,
                          `patronymic` = :patronymic,
                          `surname`    = :surname,
                          `type`       = :type
                      WHERE
                          `id` = :id";
        }
        unset($data['change']);
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет пользователя
     */
    public function removeUser($id) {

    }

    /**
     * Функция возвращает типы пользователя; тип пользователя определяет скидку
     */
    public function getUserTypes() {
        $types = array(
            1 => 'Розница',
            2 => 'Цена 2',
            3 => 'Цена 3',
            4 => 'Цена 4',
            5 => 'Цена 5',
            6 => 'Цена 6',
            7 => 'Цена 7',
        );
        return $types;
    }

}