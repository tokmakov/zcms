<?php
/**
 * Класс Exchange_Frontend_Model для обмена данными с 1С, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Exchange_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив новых заказов в магазине
     */
    public function getNewOrders() {

        $query = "SELECT
                      `id`
                  FROM
                      `orders`
                  WHERE
                      `status` = 0
                  ORDER BY
                      `added`
                  LIMIT
                      100";
        return $this->database->fetchAll($query);

    }

    /**
     * Функция возвращает заказ в магазине с уникальным идентификатором $id
     */
    public function getOrder($id) {

        // общая информация о заказе (сумма, покупатель и плательщик, статус)
        $query = "SELECT
                      `a`.`user_id` AS `user_id`, `a`.`details` AS `details`,
                      `a`.`status` AS `status`, `b`.`name` AS `user_name`,
                      `b`.`surname` AS `user_surname`, `b`.`email` AS `user_email`
                  FROM
                      `orders` `a` LEFT JOIN `users` `b` ON `a`.`user_id` = `b`.`id`
                  WHERE
                      `a`.`id` = :order_id";
        $result = $this->database->fetch($query, array('order_id' => $id));
        if (false === $result) {
            return null;
        }
        if ( ! empty($result['details'])) {
            $result['details'] = unserialize($result['details']);
        } else {
            $result['details'] = array();
        }
        $order = array_merge(
            array(
                'order_id'     => $id,
                'user_id'      => $result['user_id'],
                'user_name'    => $result['user_name'],
                'user_surname' => $result['user_surname'],
                'user_email'   => $result['user_email'],
                'status'       => $result['status']
            ),
            $result['details']
        );
        // добавляем информацию о списке товаров заказа
        $query = "SELECT
                      `code`, `quantity`
                  FROM
                      `orders_prds`
                  WHERE
                      `order_id` = :order_id
                  ORDER BY
                      `id`";
        $order['products'] = $this->database->fetchAll($query, array('order_id' => $id));

        return $order;

    }

    /**
     * Функция проверяет существование заказа с уникальным идентификатором $id
     */
    public function isOrderExists($id) {

        $query = "SELECT
                      1
                  FROM
                      `orders`
                  WHERE
                      `id` = :id";
        $res = $this->database->fetch($query, array('id' => $id));
        if (false === $res) {
            return false;
        }
        return true;

    }

    /**
     * Функция изменяет статус заказа с уникальным идентификатором $id
     */
    public function setOrderStatus($id, $status) {

        $query = "UPDATE
                      `orders`
                  SET
                      `status` = :status
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id, 'status' => $status));

    }

}