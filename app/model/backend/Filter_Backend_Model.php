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
                $this->database->execute(
                    $query,
                    array(
                        'group_id' => $group_id,
                        'param_id' => $key,
                        'value_id' => $v
                    )
                );
            }
        }
    }

    /**
     * Возвращает наименование функциональной группеы с уникальным идентификатором $id
     */
    public function getGroupName($id) {

        $query = "SELECT
                      `name`
                  FROM
                      `groups`
                  WHERE
                      `id` = :group_id";
        return $this->database->fetchOne($query, array('group_id' => $id));

    }

    /**
     * Возвращает массив идентификаторов параметров, привязанных к функциональной
     * группе с идентификатором $id и массивы привязанных к этим параметрам значений
     */
    public function getGroupParams($id) {

        $query = "SELECT
                      `a`.`param_id` AS `id`, `b`.`name` AS `name`,
                      GROUP_CONCAT(`value_id`) AS `ids`
                  FROM
                      `group_param_value` `a` INNER JOIN `params` `b`
                      ON `a`.`param_id` = `b`.`id`
                  WHERE
                      `a`.`group_id` = :group_id
                  GROUP BY
                      1, 2";
        $result = $this->database->fetchAll($query, array('group_id' => $id));
        
        foreach ($result as $key => $value) {
            $result[$key]['ids'] = explode(',', $value['ids']);
        }

        return $result;

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

        $query = "DELETE FROM
                      `group_param_value`
                  WHERE
                      `group_id` = :group_id";
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
                $this->database->execute(
                    $query,
                    array(
                        'group_id' => $data['id'],
                        'param_id' => $key,
                        'value_id' => $v
                    )
                );
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
                      `group_param_value`
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
                      `group_param_value`
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
