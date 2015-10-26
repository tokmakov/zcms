<?php
/**
 * Класс Filter_Backend_Model для работы с главным фильтром товаров,
 * взаимодействует с базой данных, административная часть сайта
 */
class Filter_Backend_Model extends Backend_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Функция возвращает массив всех функциональных групп
	 */
	public  function getGroups($limit = 0) {
		$query = "SELECT
                      `id`, `name`
                  FROM
                      `groups`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        if ($limit) {
            $query = $query . " LIMIT " . $limit;
        }
		$groups = $this->database->fetchAll($query);
		return $groups;
	}

    /**
     * Функция возвращает массив всех параметров подбора
     */
    public  function getParams($limit = 0) {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `params`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        if ($limit) {
            $query = $query . " LIMIT " . $limit;
        }
        $params = $this->database->fetchAll($query);
        return $params;
    }

    /**
     * Функция возвращает массив всех значений параметров
     */
    public  function getValues($limit = 0) {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `values`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        if ($limit) {
            $query = $query . " LIMIT " . $limit;
        }
        $values = $this->database->fetchAll($query);
        return $values;
    }

    /**
     * Функция добавляет новую функциональную группу
     */
    public  function addGroup($data) {
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

        foreach ($data['params'] as $param_id) {
            $query = "INSERT INTO `group_param`
                      (
                          `group_id`,
                          `param_id`
                      )
                      VALUES
                      (
                          :group_id,
                          :param_id
                      )";
            $this->database->execute($query, array('group_id' => $group_id, 'param_id' => $param_id));
        }
    }

    /**
     * Возвращает информацию о функциональной группе с уникальным идентификатором
     * $id: наименование и массив идентификаторов параметров, привязанных к группе
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
                      `param_id`
                  FROM
                      `group_param`
                  WHERE
                      `group_id` = :group_id";
        $result = $this->database->fetchAll($query, array('group_id' => $id));
        $group['params'] = array();
        foreach ($result as $item) {
            $group['params'][] = $item['param_id'];
        }
        return $group;
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

        $query = "DELETE FROM `group_param` WHERE `group_id` = :group_id";
        $this->database->execute($query, array('group_id' => $data['id']));

        foreach ($data['params'] as $param_id) {
            $query = "INSERT INTO `group_param`
                      (
                          `group_id`,
                          `param_id`
                      )
                      VALUES
                      (
                          :group_id,
                          :param_id
                      )";
            $this->database->execute($query, array('group_id' => $data['id'], 'param_id' => $param_id));
        }
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
     * Функция возвращает информацию о значении параметра $id
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

}
