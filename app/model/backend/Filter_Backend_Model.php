<?php
/**
 * Класс Filter_Backend_Model для работы с фильтром товаров,
 * взаимодействует с базой данных, административная часть сайта
 */
class Filter_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех функциональных групп
     */
    public function getAllGroups() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `groups`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        $groups = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($groups as $key => $value) {
            $groups[$key]['url'] = array(
                'edit'   => $this->getURL('backend/filter/editgroup/id/' . $value['id']),
                'remove' => $this->getURL('backend/filter/rmvgroup/id/' . $value['id'])
            );
        }
        return $groups;
    }

    /**
     * Функция возвращает массив всех параметров подбора
     */
    public function getAllParams() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `params`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        $params = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($params as $key => $value) {
            $params[$key]['url'] = array(
                'edit'   => $this->getURL('backend/filter/editparam/id/' . $value['id']),
                'remove' => $this->getURL('backend/filter/rmvparam/id/' . $value['id'])
            );
        }
        return $params;
    }

    /**
     * Функция возвращает массив всех значений параметров
     */
    public function getAllValues() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `values`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        $values = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($values as $key => $value) {
            $values[$key]['url'] = array(
                'edit'   => $this->getURL('backend/filter/editvalue/id/' . $value['id']),
                'remove' => $this->getURL('backend/filter/rmvvalue/id/' . $value['id'])
            );
        }
        return $values;
    }

    /**
     * Функция возвращает массив всех функциональных групп
     * для сводной страницы фильтра товаров
     */
    public function getGroups() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `groups`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает массив всех параметров подбора
     * для сводной страницы фильтра товаров
     */
    public function getParams() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `params`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает массив всех значений параметров
     * для сводной страницы фильтра товаров
     */
    public function getValues() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `values`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция добавляет новую функциональную группу
     */
    public function addGroup($data) {
        $query = "INSERT INTO `groups`
                  (
                      `name`
                  )
                  VALUES
                  (
                      :name
                  )";
        $this->database->execute($query, array('name' => $data['name']));
        $group_id = $this->database->lastInsertId();

        foreach ($data['params_values'] as $key => $value) {
            foreach ($value as $k => $v) {
                $query = "INSERT INTO `group_param_value`
                          (
                              `group_id`,
                              `param_id`,
                              `value_id`
                          )
                          VALUES
                          (
                              :group_id,
                              :param_id,
                              :value_id
                          )";
                $this->database->execute($query, array('group_id' => $group_id, 'param_id' => $key, 'value_id' => $v));
            }
        }
    }

    /**
     * Возвращает информацию о функциональной группе с уникальным идентификатором
     * $id: наименование и массив идентификаторов параметров, привязанных к группе
     * и массивы привязанных к параметрам значений
     */
    public function getGroup($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `groups`
                  WHERE `id` = :group_id";
        $result = $this->database->fetchOne($query, array('group_id' => $id));
        if (false === $result) {
            return null;
        }
        $group['name'] = $result;
        $query = "SELECT
                      `param_id`, GROUP_CONCAT(`value_id`) AS `ids`
                  FROM
                      `group_param_value`
                  WHERE
                      `group_id` = :group_id
                  GROUP BY `param_id`";
        $result = $this->database->fetchAll($query, array('group_id' => $id));
        $group['params_values'] = array();
        foreach ($result as $item) {
            $group['params_values'][$item['param_id']] = explode(',', $item['ids']);
        }
        return $group;
    }

    /**
     * Функция возвращает массив параметров, привязанных к группе $id и массивы
     * привязанных к этим параметрам значений
     */
    public function getGroupParams($id) {
        $query = "SELECT
                      `a`.`id` AS `param_id`, `a`.`name` AS `param_name`,
                      `c`.`id` AS `value_id`, `c`.`name` AS `value_name`
                  FROM
                      `params` `a`
                      INNER JOIN `group_param_value` `b` ON `a`.`id` = `b`.`param_id`
                      INNER JOIN `values` `c` ON `b`.`value_id` = `c`.`id`
                  WHERE
                      `b`.`group_id` = :group_id
                  ORDER BY
                      `param_name`, `value_name`";
        $result = $this->database->fetchAll($query, array('group_id' => $id));

        $params = array();
        $param_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $params[$counter] = array('id' => $value['param_id'], 'name' => $value['param_name']);
            }
            $params[$counter]['values'][] = array('id' => $value['value_id'], 'name' => $value['value_name']);
        }

        return $params;
    }

    /**
     * Функция возвращает массив параметров, привязанных к товару $id и массивы
     * привязанных к этим параметрам значений
     */
    public function getProductParams($id) {
        if (0 == $id) {
            return array();
        }
        $query = "SELECT
                      `a`.`id` AS `param_id`, GROUP_CONCAT(`b`.`id`) AS `ids`
                  FROM
                      `product_param_value` `c`
                      INNER JOIN `params` `a` ON `c`.`param_id` = `a`.`id`
                      INNER JOIN `values` `b` ON `c`.`value_id` = `b`.`id`
                  WHERE
                      `c`.`product_id` = :product_id
                  GROUP BY
                      `a`.`id`
                  ORDER BY
                      `a`.`name`, `b`.`name`";
        $result = $this->database->fetchAll($query, array('product_id' => $id));
        $params = array();
        foreach ($result as $item) {
            $params[$item['param_id']] = explode(',', $item['ids']);
        }
        return $params;
    }

    /**
     * Функция обновляет функциональную группу
     */
    public  function updateGroup($data) {
        $query = "UPDATE
                      `groups`
                  SET
                      `name` = :name
                  WHERE
                      `id` = :group_id";
        $this->database->execute($query, array('name' => $data['name'], 'group_id' => $data['id']));

        $query = "DELETE FROM `group_param_value` WHERE `group_id` = :group_id";
        $this->database->execute($query, array('group_id' => $data['id']));

        foreach ($data['params_values'] as $key => $value) {
            foreach ($value as $k => $v) {
                $query = "INSERT INTO `group_param_value`
                          (
                              `group_id`,
                              `param_id`,
                              `value_id`
                          )
                          VALUES
                          (
                              :group_id,
                              :param_id,
                              :value_id
                          )";
                $this->database->execute($query, array('group_id' => $data['id'], 'param_id' => $key, 'value_id' => $v));
            }
        }
    }

    /**
     * Функция удаляет функциональную группу
     */
    public function removeGroup($id) {
        $query = "UPDATE
                      `products`
                  SET
                      `group` = 0
                  WHERE
                      `group` = :group_id";
        $this->database->execute($query, array('group_id' => $id));
        $query = "DELETE FROM
                      `group_param_value`
                  WHERE
                      `group_id` = :group_id";
        $this->database->execute($query, array('group_id' => $id));
        $query = "DELETE FROM
                      `groups`
                  WHERE
                      `id` = :group_id";
        $this->database->execute($query, array('group_id' => $id));
    }

    /**
     * Функция возвращает информацию о параметре с уникальным идентификатором $id
     */
    public function getParam($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `params`
                  WHERE
                      `id` = :id";
        $param = $this->database->fetchOne($query, array('id' => $id));
        if (false === $param) {
            return null;
        }
        return $param;
    }

    /**
     * Функция добавляет новый параметр подбора
     */
    public function addParam($data) {
        $query = "INSERT INTO `params`
                  (
                      `name`
                  )
                  VALUES
                  (
                      :name
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет параметр подбора
     */
    public function updateParam($data) {
        $query = "UPDATE
                      `params`
                  SET
                      `name` = :name
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет параметр подбора
     */
    public function removeParam($id) {
        $query = "DELETE FROM
                      `group_param`
                  WHERE
                      `param_id` = :param_id";
        $this->database->execute($query, array('param_id' => $id));
        $query = "DELETE FROM
                      `product_param_value`
                  WHERE
                      `param_id` = :param_id";
        $this->database->execute($query, array('param_id' => $id));
        $query = "DELETE FROM
                      `params`
                  WHERE
                      `id` = :param_id";
        $this->database->execute($query, array('param_id' => $id));
    }

    /**
     * Функция возвращает информацию о значении параметра
     */
    public function getvalue($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `values`
                  WHERE
                      `id` = :id";
        $value = $this->database->fetchOne($query, array('id' => $id));
        if (false === $value) {
            return null;
        }
        return $value;
    }

    /**
     * Функция добавляет новое значение параметра подбора
     */
    public function addValue($data) {
        $query = "INSERT INTO `values`
                  (
                      `name`
                  )
                  VALUES
                  (
                      :name
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет значение параметра подбора
     */
    public function updateValue($data) {
        $query = "UPDATE
                      `values`
                  SET
                      `name` = :name
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет значение параметра подбора
     */
    public function removeValue($id) {
        $query = "DELETE FROM
                      `product_param_value`
                  WHERE
                      `value_id` = :value_id";
        $this->database->execute($query, array('value_id' => $id));
        $query = "DELETE FROM
                      `values`
                  WHERE
                      `id` = :value_id";
        $this->database->execute($query, array('value_id' => $id));
    }

}
