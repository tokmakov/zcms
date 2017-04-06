<?php
/**
 * Класс Order_Backend_Model для работы с заказами в магазине,
 * взаимодействует с базой данных, административная часть сайта
 */
class Order_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив заказов в магазине
     */
    public function getAllOrders($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `order_id`, `a`.`user_id` AS `user_id`,
                      `a`.`amount` AS `amount`, `a`.`status` AS `status`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      IFNULL(`b`.`name`, '') AS `user_name`,
                      IFNULL(`b`.`patronymic`, '') AS `user_patronymic`,
                      IFNULL(`b`.`surname`, '') AS `user_surname`,
                      IFNULL(`b`.`email`, '') AS `user_email`
                  FROM
                      `orders` `a` LEFT JOIN `users` `b` ON `a`.`user_id` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->orders->perpage;
        $orders = $this->database->fetchAll($query, array());
        // добавляем в массив URL ссылок для перехода на страницу заказа
        foreach($orders as $key => $value) {
            $orders[$key]['url'] = $this->getURL('backend/order/show/id/' . $value['order_id']);
        }
        return $orders;
    }

    /**
     * Функция возвращает общее количество заказов в магазине
     */
    public function getCountOrders() {
        $query = "SELECT COUNT(*) FROM `orders` WHERE 1";
        $res = $this->database->fetchOne($query);
        return $res;
    }

    /**
     * Функция возвращает подробную информацию о заказе
     */
    public function getOrder($id) {

        $query = "SELECT
                      `a`.`id` AS `order_id`, `a`.`user_id` AS `user_id`, `a`.`amount` AS `amount`,
                      `a`.`details` AS `details`, DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`, `a`.`status` AS `status`,
                      IFNULL(`b`.`name`, '') AS `user_name`, IFNULL(`b`.`patronymic`, '') AS `user_patronymic`,
                      IFNULL(`b`.`surname`, '') AS `user_surname`, IFNULL(`b`.`email`, '') AS `user_email`
                  FROM
                      `orders` `a` LEFT JOIN `users` `b` ON `a`.`user_id` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        $result = $this->database->fetch($query, array('id' => $id));
        if (false === $result) {
            return null;
        }
        $details = array();
        if ( ! empty($result['details'])) {
            $details = unserialize($result['details']);
        }
        $order = array_merge(
            array(
                'order_id'        => $id,
                'user_name'       => $result['user_name'],
                'user_patronymic' => $result['user_patronymic'],
                'user_surname'    => $result['user_surname'],
                'user_email'      => $result['user_email'],
                'amount'          => $result['amount'],
                'date'            => $result['date'],
                'time'            => $result['time'],
                'status'          => $result['status']
            ),
            $details
        );
        $query = "SELECT
                      `product_id`, `code` AS `code`, `name`,
                      `title`, `price`, `unit`, `quantity`, `cost`
                  FROM
                      `orders_prds`
                  WHERE
                      `order_id` = :order_id";
        $order['products'] = $this->database->fetchAll($query, array('order_id' => $id));

        return $order;

    }
}