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

}
